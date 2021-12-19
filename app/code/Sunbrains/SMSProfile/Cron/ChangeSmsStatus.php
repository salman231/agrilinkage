<?php
/**
 * Sunbrains
 * @category  Sunbrains
 * @package   Sunbrains_SMSProfile
 * @author    Sunbrains
 */

namespace Sunbrains\SMSProfile\Cron;

use Sunbrains\SMSProfile\Model\SMSProfileLogFactory;
use Sunbrains\SMSProfile\Helper\Data as HelperData;
use Sunbrains\SMSProfile\Model\SMSProfileService;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Json\Helper\Data as JsonHelper;

class ChangeSmsStatus 
{
    
    /** @var SMSProfileService */
    private $smsService;

    /** @var SMSProfileLogFactory */
    private $smslog;

    /**  @var HelperData */
    private $datahelper;

    /**  @var TimezoneInterface */
    private $timezone;

    /** @var Curl */
    private $curl;

    /**
    * @var JsonHelper
    */
    private $jsonHelper;

    /**
     * @param SMSProfileLogFactory $smslog
     * @param SMSProfileService $smsService
     * @param HelperData $dataHelper
     * @param TimezoneInterface $datetime
     */
    public function __construct(
        HelperData $dataHelper,
        SMSProfileService $smsService,
        TimezoneInterface $timezone,
        Curl $curl,
        JsonHelper $jsonHelper,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date,
        SMSProfileLogFactory $smslog
    ) {
        $this->timezone = $timezone;
        $this->smslog = $smslog;
        $this->smsService = $smsService;
        $this->datahelper = $dataHelper;
        $this->curl = $curl;
        $this->date = $date;
        $this->jsonHelper = $jsonHelper;
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
        $other_url =$this->datahelper->getSmsProfileApiUrl();

        /*code for timeout*/
       
        if($this->datahelper->getSmsProfileApiGateWay() =='Twilio Api Service') {
            $this->changeSMSStatusForTimeOut($smslog);    
        } else {        
            $this->changeSMSStatusForTimeOutWithOther($smslog);
        }    
        /*code for timeout*/

        
        $sms = $smslog->getCollection();
        $sms->addFieldToFilter('api_service','Twilio Api Service');
        $sms->addFieldToFilter('status',array('in' => array('queued','sent')));
        $sms->setPageSize(10);
        $sms->setCurPage(1);
        $sms->load();

        $other_sms =$smslog->getCollection();
        $other_sms->addFieldToFilter('api_service','Other');
        if ($this->datahelper->getSmsProfileApiProcessStatus()) {
            $_status =$this->datahelper->getSmsProfileApiProcessStatus();
            $other_sms->addFieldToFilter('status',array('in' => $_status));
        }
        $other_sms->setPageSize(10);
        $other_sms->setCurPage(1);
        $other_sms->load();

        if ($sms) {
            $this->changeSMSStatusWithTwilio($sms, $Client, $smslog);
        }

        if ($other_sms) {
            if ($this->datahelper->getApiReauestResponseXML()) {
                $this->changeSMSStatusWithOtherXML($other_sms, $smslog);
            } else {
                $this->changeSMSStatusWithOther($other_sms, $smslog);
            }
            
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

    private function changeSMSStatusWithTwilio($sms,$Client,$smslog)
    {
        foreach ($sms as $sms) {
            $message = $Client->messages($sms->getSId())->fetch();
            $sms->setStatus($message->status);
            $additionalData['toNumber'] = $message->to;
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
            $sms->save();
            if ($message->status === 'undelivered') {
               $smslog->sendSmsProfileFailureMail($additionalData,$error);
            }
        }
    }
    public function changeSMSStatusWithOther($other_sms, $smslog)
    {
        foreach ($other_sms as $sms) {
            if($this->datahelper->getSmsProfileApiSmsFetchUrl()){
                $_url = $this->datahelper->getSmsProfileApiSmsFetchUrl();
                $url = str_replace("{msid}",$sms->getSId(),$_url);
                $additionalData['toNumber'] = $sms->getRecipientPhone();
                $additionalData["transactionType"] =$sms->getTransactionType();
                $this->curl->setCredentials($this->getApiUser(),$this->getApiUserPassword()); 
                $this->curl->get($url);
                $response = $this->curl->getBody();  
                $res = $response;
                if(is_string($response) && is_array($this->jsonHelper->jsonDecode($response, true))){
                    $sms_status = '';
                    $res= $this->jsonHelper->jsonDecode($response, true);
                    if(isset($res['recipients']['items'][0]['status'])) {
                        /*case of message bird*/
                        $sms_status = $res['recipients']['items'][0]['status'];
                    } else if(isset($res['status'])) {
                        $sms_status = $res['status'];   
                    } else if(isset($res['Status'])) {
                        $sms_status = $res['Status'];   
                    }
                    if($sms_status != ''){
                        $sms->setStatus($sms_status);
                        if($this->datahelper->getSmsProfileApiFailureStatus() && in_array($sms_status, $this->datahelper->getSmsProfileApiFailureStatus())) {
                            $error = __('Delivery Failed due to unknown reason.');
                            if($this->datahelper->getSmsProfileApiSmsErrorKey())
                            { 
                               if(isset($res[$this->datahelper->getSmsProfileApiSmsErrorKey()]) && $res[$this->datahelper->getSmsProfileApiSmsErrorKey()] != null ) {
                                    $error = $res[$this->datahelper->getSmsProfileApiSmsErrorKey()];
                               }                                    
                            }
                            $sms->setIsError(1);
                            $sms->setErrorMessage($error);
                            $smslog->sendSmsProfileFailureMail($additionalData,$error);
                        }
                        $sms->save();
                    }    

                }
            }    
        }
    }

    private function changeSMSStatusForTimeOut($smslog)
    {
       $seconds = $this->datahelper->getTimeoutSeconds(); 
       $condition = '(`api_service` = \'Twilio Api Service\') AND (`status` IN(\'queued\', \'sent\')) AND( DATE_ADD(`created_at`, INTERVAL '. $seconds.' second) <= now())'; 

       $smslog->getCollection()->setTableRecords(
               $condition, ['status' => 'Timeout']
            );

       return;
    }

    private function changeSMSStatusForTimeOutWithOther($smslog)
    {
       $seconds = $this->datahelper->getTimeoutSeconds();
       $_status =implode(",",$this->datahelper->getSmsProfileApiProcessStatus()); 
       $condition = '(`api_service` = \'Other\') AND (`status` IN(\''.$_status.'\')) AND( DATE_ADD(`created_at`, INTERVAL '. $seconds.' second) <= now())'; 

       $smslog->getCollection()->setTableRecords(
               $condition, ['status' => 'Timeout']
            );

       return;
    }

    public function getApiUser()
    {
        $apiCredential = $this->datahelper->getSmsProfileApiCredential();
        return $apiCredential['username'];
    }

    public function getApiUserPassword()
    {
        $apiCredential = $this->datahelper->getSmsProfileApiCredential();
        if (!isset($apiCredential['password'])) {
            return '';
        }
        return $apiCredential['password'];
    }

    public function changeSMSStatusWithOtherXML($other_sms, $smslog)
    {
        foreach ($other_sms as $sms) {
            if($this->datahelper->getSmsProfileApiSmsFetchUrl()){
                $_url = $this->datahelper->getSmsProfileApiSmsFetchUrl();
                $postData = $this->getApiDLRXML($sms);
                $additionalData['toNumber'] = $sms->getRecipientPhone();
                $additionalData["transactionType"] =$sms->getTransactionType();
                $this->curl->setCredentials($this->getApiUser(),$this->getApiUserPassword()); 
                $this->curl->setOption(CURLOPT_RETURNTRANSFER, true);
                $this->curl->setOption(CURLOPT_SSL_VERIFYPEER, false);
                $this->curl->setOption(CURLOPT_HEADER, true);
                $this->curl->setOption(CURLOPT_ENCODING, '');
                $this->curl->setOption(CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
                $this->curl->post($_url,$postData);
                $response = $this->curl->getBody();  
                $result = substr($response, strpos($response, "<?xm"));
            
                $xml = simplexml_load_string($result);
                
                if($xml->transactions->transaction->external_id != null && $sms->getSId() == $xml->transactions->transaction->external_id)
                {
                     if ($xml->transactions->transaction->status != null) {
                            $sms->setStatus($xml->transactions->transaction->status);
                            if($this->datahelper->getSmsProfileApiFailureStatus() && in_array($xml->transactions->transaction->status, $this->datahelper->getSmsProfileApiFailureStatus())) {
                                $error = __('Delivery Failed due to unknown reason.');

                                    $sms->setIsError(1);
                                    $sms->setErrorMessage($error);
                                    $smslog->sendSmsProfileFailureMail($additionalData,$error);
                            }
                            $sms->save();   
                     }
                }               
            }
        }
    }

    public function getApiDLRXML($other_sms)
    {
        $frormDate = $this->date->date('-2 day')->format('d/m/y H:i') ;
        $toDate = $this->date->date('+1 day')->format('d/m/y H:i') ;

        $XMLData = "<?xml version='1.0' encoding='UTF-8'?>                        
                    <dlr>
                        <user>
                          <username>". $this->getApiUser(). "</username>
                          <password>". $this->getApiUserPassword(). "</password>
                        </user>
                        <transactions>        
                            <external_id>". $other_sms->getSId()."</external_id>
                        </transactions>
                        <from>".$frormDate."</from>
                        <to>".$toDate."</to>
                  </dlr>";

        return $XMLData;
    }
}
