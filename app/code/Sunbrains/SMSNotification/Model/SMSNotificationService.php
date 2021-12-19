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
namespace Sunbrains\SMSNotification\Model;

use Twilio\Rest\ClientFactory;
use Sunbrains\SMSNotification\Model\SMSLogFactory;
use Sunbrains\SMSNotification\Helper\Data as HelperData;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Json\Helper\Data as JsonHelper;

class SMSNotificationService
{
    /** @var SMSLogFactory */
    private $smslog;

    /** @var HelperData */
    private $dataHelper;

    /** @var EncryptorInterface */
    private $encryptor;

    /** @var Curl */
    private $curl;

    /**
     * toNumber
     * @var $toNumber
     */

    private $toNumber;

    /**
     * messageContent
     * @var $messageContent
     */

    private $messageContent;
    
    /**
     * transactionType
     * @var $transactionType
     */

    private $transactionType;

    /**
     * apiVersion
     * @var $apiVersion
     */

    private $apiVersion;

    /**
     * @var JsonHelper
     */
    private $jsonHelper;

    /**
     * Constructor
     * @param SMSLogFactory $smslog
     * @param HelperData $dataHelper
     * @param EncryptorInterface $encryptor
     * @param ClientFactory $twilioClientFactory
     */

    public function __construct(
        SMSLogFactory $smslog,
        HelperData $dataHelper,
        EncryptorInterface $encryptor,
        Curl $curl,
        JsonHelper $jsonHelper,
        ClientFactory $twilioClientFactory
    ) {
        $this->smslog = $smslog;
        $this->datahelper = $dataHelper;
        $this->encryptor = $encryptor;
        $this->curl = $curl;
        $this->jsonHelper = $jsonHelper;
        $this->twilioClientFactory = $twilioClientFactory;
    }

    private function getTwilioToken()
    {
        $token = $this->datahelper->getAuthToken();
        return $this->encryptor->decrypt($token);
    }

    private function getTwilioSid()
    {
        return $this->datahelper->getAccountSID();
    }

    private function getDefaultCountryCode()
    {
        return $this->datahelper->getDefaultCountry();
    }

    public function getTwilioClient()
    {
        return $this->twilioClientFactory->create([
            'username' => $this->getTwilioSid(),
            'password' => $this->getTwilioToken()
        ]);
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

    public function setToNumber($toNumber)
    {
        $this->toNumber = $toNumber;
    }

    public function getToNumber()
    {
        if ($this->datahelper->getApiGateway() == "Other") {
            if ($this->datahelper->getApiRequiredCountryCode()) {
                if (strpos($this->toNumber, '+') === false) {
                    $this->toNumber =  $this->getDefaultCountryCode().$this->toNumber;
                }
            }
        }
        if (strpos($this->toNumber, '+') === false && $this->datahelper->getApiGateway() != "Other") {
               $this->toNumber =  $this->getDefaultCountryCode().$this->toNumber;
        }
        return  $this->toNumber ;
    }

    public function setMessageContent($message)
    {
        $this->messageContent = $message;
    }

    public function getMessageContent()
    {
        return $this->messageContent;
    }

    public function setTransactionType($transactionType)
    {
        $this->transactionType = $transactionType;
    }

    public function getTransactionType()
    {
        return $this->transactionType;
    }

    public function setApiVersion($apiVersion)
    {
        $this->apiVersion = $apiVersion;
    }

    public function getApiVersion()
    {
        return $this->apiVersion;
    }

    private function getAdditionalData()
    {
        $id ='';
        if (is_string($this->getMessageContent()) && $this->getMessageContent() != null) {
            preg_match_all('!\d+!', $this->getMessageContent(), $matches);
            if (isset($matches[0][0])) {
                if (strlen($matches[0][0]) >= 9) {
                    $id = $matches[0][0];
                } elseif (isset($matches[0][1])) {
                    $id = $matches[0][1];
                }
            }
        }
        $additionalData = [
            'apiVersion' => $this->getApiVersion(),
            'transactionType' =>   $this->getTransactionType(),
            'toNumber' => $this->getToNumber(),
            'orderNo' => $id,
        ];

        return $additionalData;
    }

    public function sendSmsWithTwilio()
    {
        $sms  = $this->smslog->create();
        try {
            $client = $this->getTwilioClient();
            $result = $client->messages->create(
                $this->getToNumber(),
                [
                        'from' => $this->datahelper->getTwilioPhone(),
                        'body' => $this->getMessageContent()
                    ]
            ); /* get result json */
            $sms->addLog($result, $this->getAdditionalData(), null);
        } catch (\Exception $e) {
            $sms->addLog($result = 'fail', $this->getAdditionalData(), $e->getMessage());
        }
        return $this;
    }

    private function getBulkSmsData()
    {
        $data = array (
            'to' => $this->getToNumber(),
            "body" => $this->getMessageContent(),
        );
        return $data;
    }

    public function sendSmsWithBulkSmsService()
    {
        $url = $this->datahelper->getBulkSmsUrl();
        $data =  $this->getBulkSmsData();
        $sms  = $this->smslog->create();
        try {
            $this->curl->setCredentials($this->getBulkSmsUserName(), $this->getBulkSmsPassword());
            $this->curl->setOption(CURLOPT_RETURNTRANSFER, true);
            $this->curl->setOption(CURLOPT_SSL_VERIFYPEER, false);
            $this->curl->setOption(CURLOPT_HEADER, true);
            $this->curl->setOption(CURLOPT_ENCODING, 'UTF-8');
            $this->curl->post($url, $data);
            $response = $this->curl->getBody();
            if (strpos($response, '[') !== false) {
                $response=  strstr($response, '[');
            } elseif (strpos($response, '{') !== false) {
                $response=  strstr($response, '{');
            }
            $result = $this->jsonHelper->jsonDecode($response); /* get result array  */
            if (isset($result['title'])) {
                  $sms->addLog($result, $this->getAdditionalData(), $result['title']);
            } else {
                $sms->addLog(
                    $result,
                    $this->getAdditionalData(),
                    is_array($result) ? null :  __('Unable to initialize cURL request')
                );
            }
        } catch (\Exception $e) {
            $sms->addLog($result = 'fail', $this->getAdditionalData(), $e->getMessage());
        }
        return $this;
    }

    public function sendSmsViaOtherServices()
    {
        $url = $this->datahelper->getApiEndPoints();
        $user  = $this->getApiUser();
        $password = $this->getApiUserPassword();
        $data = $this->getApiData();
        $sms  = $this->smslog->create();
        try {
            $this->curl->setCredentials($user, $password);
            $this->curl->setOption(CURLOPT_RETURNTRANSFER, true);
            $this->curl->setOption(CURLOPT_SSL_VERIFYPEER, false);
            $this->curl->setOption(CURLOPT_HEADER, true);
            $this->curl->setOption(CURLOPT_ENCODING, 'UTF-8');
            $this->curl->post($url, $data);
            $response = $this->curl->getBody();

            if (strpos($response, '{') !== false) {
                $response=  strstr($response, '{');
                
                $result = $this->jsonHelper->jsonDecode($response); //get result array
            } else {
                if (strpos($url, 'msg91') !== false) {
                    $_result = explode(" ", $response); //get result array
                    
                    $result['status'] = $_result[2];
                    $sid_array = explode("\n", $_result[2]);
                    if (!is_null($sid_array)) {
                        $result['status'] =   $sid_array[0];
                    }
                    $result['body'] = $data[$this->datahelper->getApiMessage()];
                    $result['sid'] = (isset($_result[32])) ? '' : $_result[31];
                    if (isset($_result[31])) {
                        $status_array = explode("\n", $result['sid']);
                        if (!is_null($status_array)) {
                             $result['sid'] =  end($status_array);
                        }
                    }
                    if (isset($_result[32])) {
                         $result['error_m'] = $_result[32].' '.$_result[33].' '.$_result[34].' '.$_result[35].' '.$_result[36];
                        $result['status'] = __('failed');
                    }
                }
            }
            if (isset($result['error_m'])) {
                /*case of msg91 */
                $sms->addLog($result, $this->getAdditionalData(), $result['error_m']);
            } elseif (isset($result['ErrorMessage']) && $result['ErrorMessage'] != "Success") {
                 /** case of smsindihub*/
                $sms->addLog($result, $this->getAdditionalData(), $result['ErrorMessage']);
            } elseif (isset($result['errors'][0]['description'])) {
                /*case of message bird */
                $sms->addLog($result, $this->getAdditionalData(), $result['errors'][0]['description']);
            } else {
                $sms->addLog(
                    $result,
                    $this->getAdditionalData(),
                    is_array($result) ? null :  __('Unable to initialize cURL request')
                );
            }
        } catch (\Exception $e) {
            $sms->addLog($result = 'fail', $this->getAdditionalData(), $e->getMessage());
        }
        return $this;
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

    public function getApiData()
    {
        $data = $this->datahelper->getApiParams();
        $data[$this->datahelper->getApiTo()] = $this->getToNumber();
        $data[$this->datahelper->getApiMessage()] = $this->getMessageContent();
        return $data;
    }
}
