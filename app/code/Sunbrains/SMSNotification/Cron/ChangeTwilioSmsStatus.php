<?php
/**
 * Sunbrains
 * Copyright (C) 2019 Sunbrains <info@sunbrains.com>
 *
 * @category Sunbrains
 * @package Sunbrains_SMSNotification
 * @copyright Copyright (c) 2019 Mage Delight (http://www.sunbrains.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Sunbrains <info@sunbrains.com>
 */
 
namespace Sunbrains\SMSNotification\Cron;

use Sunbrains\SMSNotification\Model\SMSLogFactory;
use Sunbrains\SMSNotification\Helper\Data as HelperData;
use Sunbrains\SMSNotification\Model\SMSNotificationService;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\Encryption\EncryptorInterface;

class ChangeTwilioSmsStatus
{
    /**
     * @var SMSNotificationService
     */
    private $smsService;

    /**
     * @var SMSLogFactory
     */
    private $smslog;

    /**
     * @var HelperData
     */
    private $datahelper;

    /**
     * @param SMSLogFactory $smslog
     * @param SMSNotificationService $smsService
     * @param HelperData $dataHelper
     * @param Curl $curl
     * @param JsonHelper $jsonHelper
     * @param EncryptorInterface $encryptor
     */
    public function __construct(
        HelperData $dataHelper,
        SMSNotificationService $smsService,
        Curl $curl,
        JsonHelper $jsonHelper,
        EncryptorInterface $encryptor,
        SMSLogFactory $smslog
    ) {
        $this->smslog = $smslog;
        $this->smsService = $smsService;
        $this->curl =$curl;
        $this->jsonHelper = $jsonHelper;
        $this->encryptor = $encryptor;
        $this->datahelper = $dataHelper;
    }

     /**
      * SmsLog change status for Cron request
      *
      * @return RedirectFactory
      */
    public function execute()
    {
        $Client = $this->smsService->getTwilioClient();
        $smslog = $this->smslog->create();
        $sms = $smslog->getCollection();
        $sms->addFieldToFilter('api_service', 'Twilio Api Service');
        $sms->addFieldToFilter('status', ['in' => ['queued','sent']]);

        $bulk_sms =$smslog->getCollection();
        $bulk_sms->addFieldToFilter('api_service', 'BulkSms');
        $bulk_sms->addFieldToFilter('status', ['in' => ['SENT']]);

        $other_sms =$smslog->getCollection();
        $other_sms->addFieldToFilter('api_service', 'Other');
        if ($this->datahelper->getApiIntialStatus()) {
            $_status =explode(",", $this->datahelper->getApiIntialStatus());
            $other_sms->addFieldToFilter('status', ['in' => $_status ]);
        }
        if ($sms) {
            $this->changeSMSStatusWithTwilio($sms, $Client, $smslog);
        }
        if ($bulk_sms) {
            $this->changeSMSStatusWithBulkSMSSevice($bulk_sms, $smslog);
        }
        if ($other_sms) {
            $this->changeSMSStatusWithOtherSevice($other_sms, $smslog);
        }
    }

    public function getDetailedErrorForTwilio($errorCode)
    {
        if ($errorCode == 30008) {
            return 'Error 30008 "Message Delivery - Unknown error": The destination carrier has returned a generic error message.';
        } elseif ($errorCode == 30005) {
            return 'Error 30005 "Message Delivery - Unknown destination handset": The destination carrier is reporting the To number is unknown, or no longer in service.';
        } elseif ($errorCode == 30003) {
            return 'Error 30003 "Message Delivery - Unreachable destination handset": The destination carrier is reporting the \'To\' number is unreachable - the device is likely powered down, out of the service area, or may not accept your messages.';
        } elseif ($errorCode == 30007) {
            return 'Error 30007 "Message Delivery - Carrier Violation": The destination carrier is filtering out your messages for delivery.';
        } elseif ($errorCode == 30004) {
            return 'Error 30004 "Message Delivery - Message blocked": Your message has been blocked from reaching the destination.(For eg. DND Activated )';
        }
    }

    private function getBulkSmsUserName()
    {
        return $this->datahelper->getBulkSmsUserName();
    }

    private function getBulkSmsPassword()
    {
        $password = $this->datahelper->getBulkSmsPassword();
        return $this->encryptor->decrypt($password);
    }

    private function changeSMSStatusWithTwilio($sms, $Client, $smslog)
    {
        foreach ($sms as $sms) {
            $message = $Client->messages($sms->getSId())->fetch();
            $sms->setStatus($message->status);
            $additionalData['toNumber'] = $message->to;
            $id ='';
            if (is_string($message->body) && $message->body != null) {
                preg_match_all('!\d+!', $message->body, $matches);
                if (isset($matches[0][0])) {
                    if (strlen($matches[0][0]) >= 9) {
                        $id = $matches[0][0];
                    } elseif (isset($matches[0][1])) {
                        $id = $matches[0][1];
                    }
                }
            }
            $additionalData['orderNo'] = $id;
            $additionalData["transactionType"] =$sms->getTransactionType();
            $error = '';
            $errorCode = $message->errorCode;
            if ($this->getDetailedErrorForTwilio($errorCode)) {
                $error = $this->getDetailedErrorForTwilio($errorCode);
            }
            if ($error === '' && isset($message->errorCode)) {
                $error = $message->errorCode.'  '.$message->errorMessage;
            }
            if ($message->status === 'undelivered') {
                $sms->setIsError(1);
            }
            $sms->setErrorMessage($error);
            $this->saveSMS($sms);
            if ($message->status === 'undelivered') {
                $smslog->sendFailureMail($additionalData, $error);
            }
        }
    }

    private function changeSMSStatusWithBulkSMSSevice($bulk_sms, $smslog)
    {
        foreach ($bulk_sms as $sms) {
            $url = $this->datahelper->getBulkSmsUrl().$sms->getSId();
            $additionalData['toNumber'] = $sms->getRecipientPhone();
            $additionalData["transactionType"] =$sms->getTransactionType();
            $id ='';
            if (is_string($sms->getMessageBody()) && $sms->getMessageBody() != null) {
                preg_match_all('!\d+!', $sms->getMessageBody(), $matches);
                if (isset($matches[0][0])) {
                    if (strlen($matches[0][0]) >= 9) {
                        $id = $matches[0][0];
                    } elseif (isset($matches[0][1])) {
                        $id = $matches[0][1];
                    }
                }
            }
            $additionalData['orderNo'] = $id;

            $this->curl->setCredentials($this->getBulkSmsUserName(), $this->getBulkSmsPassword());
            $this->curl->get($url);
            $response = $this->curl->getBody();
            $result = $this->jsonHelper->jsonDecode($response); /* get result array  */
            $sms_status = $result['status']['type'];
            $sms->setStatus($sms_status);
            if ($sms_status == 'FAILED') {
                $error = __('Delivery Failed due to unknown reason.');
                $sms->setIsError(1);
                $sms->setErrorMessage($error);
                $smslog->sendFailureMail($additionalData, $error);
            }
            $this->saveSMS($sms);
        }
    }

    private function changeSMSStatusWithOtherSevice($other_sms, $smslog)
    {
        foreach ($other_sms as $sms) {
            if ($this->datahelper->getUrlToChangeStatus()) {
                $_url = $this->datahelper->getUrlToChangeStatus();
                $url = str_replace("{msid}", $sms->getSId(), $_url);
                $additionalData['toNumber'] = $sms->getRecipientPhone();
                $additionalData["transactionType"] =$sms->getTransactionType();
                preg_match_all('!\d+!', $sms->getMessageBody(), $matches);
                $id ='';
                if (isset($matches[0][0])) {
                    if (strlen($matches[0][0]) >= 9) {
                         $id = $matches[0][0];
                    } elseif (isset($matches[0][1])) {
                        $id = $matches[0][1] ;
                    }
                }
                $additionalData["orderNo"] = $id;
                $this->curl->setCredentials($this->getApiUser(), $this->getApiUserPassword());
                $this->curl->get($url);
                $response = $this->curl->getBody();
                $res = $response;
                if (is_string($response) && is_array($this->jsonHelper->jsonDecode($response, true))) {
                    $sms_status = '';
                    $res= $this->jsonHelper->jsonDecode($response, true);
                    if (isset($res['recipients']['items'][0]['status'])) { /*case of message bird*/
                        $sms_status = $res['recipients']['items'][0]['status'];
                    }

                    if (isset($res['status'])) {
                        $sms_status = $res['status'];
                    }
                    if (isset($res['Status'])) {/* case of sms india hub*/
                        $sms_status = $res['Status'];
                    }
                    if ($sms_status != '') {
                        $sms->setStatus($sms_status);
                        if ($this->datahelper->getApiFailureStatus()) {
                            if (in_array($sms_status, $this->datahelper->getApiFailureStatus())) {
                                $error = __('Delivery Failed due to unknown reason.');
                                if ($this->datahelper->getApiErrorkey()) {
                                    if (isset($res[$this->datahelper->getApiErrorkey()]) &&
                                             $res[$this->datahelper->getApiErrorkey()] != null
                                       ) {
                                            $error = $res[$this->datahelper->getApiErrorkey()];
                                    }
                                }
                                $sms->setIsError(1);
                                $sms->setErrorMessage($error);
                                $smslog->sendFailureMail($additionalData, $error);
                            }
                        }
                        $this->saveSMS($sms);
                    }
                }
            }
        }
    }

    public function getApiUser()
    {
        $apiCredential = $this->datahelper->getApiCredential();
        return $apiCredential['username'];
    }

    public function getApiUserPassword()
    {
        $apiCredential = $this->datahelper->getApiCredential();
        return $apiCredential['password'];
    }

    public function saveSMS($sms)
    {
        $sms->save();
    }
}
