<?php
/**
 * Sunbrains
 * @category  Sunbrains
 * @package   Sunbrains_SMSProfile
 * @author    Sunbrains
 */
 
namespace Sunbrains\SMSProfile\Model;

use Twilio\Rest\ClientFactory;
use Sunbrains\SMSProfile\Model\SMSProfileLogFactory;
use Sunbrains\SMSProfile\Helper\Data as HelperData;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Json\Helper\Data as JsonHelper;

class SMSProfileService
{
    /** @var SMSProfileLogFactory */
    private $smsprofilelog;

    /** @var HelperData */
    private $dataHelper;

    /** @var EncryptorInterface */
    private $encryptor;

     /** @var Curl */
    private $curl;

    /**
    * @var JsonHelper
    */
    private $jsonHelper;

    /**
     * toNumber
     * @var $toNumber
     */

    private $toNumber;

    /**
     * phoneId
     * @var $phoneId
     */

    private $phoneId;

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
     * toBinding
     * @var $toBinding
     */

    private $toBinding;

    /**
     * Constructor
     * @param SMSProfileLogFactory $smsprofilelog
     * @param HelperData $dataHelper
     * @param EncryptorInterface $encryptor
     * @param ClientFactory $twilioClientFactory
     */

    public function __construct(
        SMSProfileLogFactory $smsprofilelog,
        HelperData $dataHelper,
        EncryptorInterface $encryptor,
        Curl $curl,
        JsonHelper $jsonHelper,
        ClientFactory $twilioClientFactory
    ) {
        $this->smsprofilelog = $smsprofilelog;
        $this->datahelper = $dataHelper;
        $this->encryptor = $encryptor;
        $this->curl = $curl;
        $this->jsonHelper = $jsonHelper;
        $this->twilioClientFactory = $twilioClientFactory;
    }

    private function getTwilioToken()
    {
        $token = $this->datahelper->getSmsProfileTwilioAccountToken();
        return $this->encryptor->decrypt($token);
    }

    private function getTwilioSid()
    {
        return $this->datahelper->getSmsProfileTwilioAccountSId();
    }

    private function getDefaultCountryCode()
    {
        return $this->datahelper->getSmsProfileDefaultCountry();
    }

    public function getTwilioClient()
    {
        return $this->twilioClientFactory->create([
            'username' => $this->getTwilioSid(),
            'password' => $this->getTwilioToken()
        ]);
    }

    public function setToNumber($toNumber)
    {
        $this->toNumber = $toNumber;
    }

    public function getToNumber()
    {
        if (is_array($this->toNumber)) {
            $no = [];
            foreach ($this->toNumber as $number ) {
                if (strpos($number, '+') === false && $this->datahelper->getSmsProfileApiCountryRequired()) {
                    //$number =  $this->getDefaultCountryCode().$number;
                    $number = $number;
                }
                $no[] = $number;
            }
            //return $no;
            return implode(",",$no);

        } else {
            if ($this->datahelper->getSmsProfileApiGateWay() =='Other' && $this->datahelper->getSmsProfileApiCountryRequired() && (strpos($this->toNumber, '+') === false)) {
                 //$this->toNumber =  $this->getDefaultCountryCode().$this->toNumber;
                 $this->toNumber =  $this->toNumber;
            }
            if (strpos($this->toNumber, '+') === false && $this->datahelper->getSmsProfileApiGateWay() !='Other') {
                   //$this->toNumber =  $this->getDefaultCountryCode().$this->toNumber;
                   $this->toNumber =  $this->toNumber;
            }
             
            return "+".$this->toNumber;
        }    
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

    public function getAdditionalData()
    {
        $additionalData = [
            'apiVersion' => $this->getApiVersion(),
            'transactionType' =>   $this->getTransactionType(),
            'toNumber' => (is_array($this->getToNumber())) ? implode(",",$this->getToNumber()) : $this->getToNumber()
        ];

        return $additionalData;
    }

    public function sendOTPSmsWithTwilio()
    {
        $sms  = $this->smsprofilelog->create();
        try {
            $client = $this->getTwilioClient();
            $result = $client->messages->create(
                $this->getToNumber(),
                [
                        'from' => $this->datahelper->getSmsProfileTwilioPhoneNumber(),
                        'body' => $this->getMessageContent()
                    ]
            ); /* get result json */
            
            $sms->addSmsProfileLog($result, $this->getAdditionalData(), null);
        } catch (\Exception $e) {
            $sms->addSmsProfileLog($result = 'fail', $this->getAdditionalData(), $e->getMessage());
            return $e->getMessage();
        }
        return $this;
    }

    public function sendPromotionalSmsTextWithTwilio()
    {
        $sms  = $this->smsprofilelog->create();

        try {
            $client = $this->getTwilioClient();

            $result = $client->notify->services($this->getServiceSid())
            ->notifications->create([
                 "ToBinding" => $this->getToBinding(),
                 "body" =>  $this->getMessageContent()
            ]); /* get result json */


            $sms->addSmsProfileLog($result, $this->getSmsPromotionalAdditionalDataTwilio(), null);
        } catch (\Exception $e) {
           
            $sms->addSmsProfileLog($result = 'fail', $this->getSmsPromotionalAdditionalDataTwilio(), $e->getMessage());
        }
        return $this;
    }

    public function setToBinding($toNumbers)
    {
        $_no =array();
        foreach ($toNumbers as $toNumbers) {
            if (strpos($toNumbers, '+') === false) {
               $toNumbers =  $this->getDefaultCountryCode().$toNumbers;
            }
            $_no[]  ="{\"binding_type\":\"sms\",\"address\":\"".$toNumbers."\"}";
        }

        $this->toBinding = $_no;
    }

    public function getToBinding()
    {
       return $this->toBinding;
    }

    public function getServiceSid()
    {
        return $this->datahelper->getSmsProfileTwilioServiceId();
    }

    public function getSmsPromotionalAdditionalDataTwilio()
    {
        $additionalData = [
            'apiVersion' => $this->getApiVersion(),
            'transactionType' =>   $this->getTransactionType(),
            'toNumber' => implode(",", $this->getToBinding())
        ];
        return $additionalData;
    }

    public function sendSmsProfileOTPViaOtherServices()
    {
        $url = $this->datahelper->getSmsProfileApiUrl();
        $user  = $this->getApiUser();
        $password = $this->getApiUserPassword();
        $data = $this->getApiData();
        $sms  = $this->smsprofilelog->create();
        $getstring ='';
        foreach ($data as $key => $value) {
            if($key === $this->datahelper->getSmsProfileApiSmsBody()){
                 $getstring .= $key."=".urlencode($value)."&";
            } else {
                $getstring .= $key."=".$value."&";
            }            
        }
        $getstring = substr($getstring, 0, -1);

        $_url = $url.'?'.$getstring;

        try {
            if ($this->datahelper->getAPIRequestInGet()) { 
                $this->curl->get($_url);
            } else {
                $this->curl->setCredentials($user,$password); 
                $this->curl->setOption(CURLOPT_RETURNTRANSFER, true);
                $this->curl->setOption(CURLOPT_SSL_VERIFYPEER, false);
                $this->curl->setOption(CURLOPT_HEADER, true);
                $this->curl->setOption(CURLOPT_ENCODING, 'UTF-8');
                if(isset($data['authorization'])  && (strpos($url, 'fast2sms') !== false)){
                    $headers = ["Content-Type" => "application/x-www-form-urlencoded",
                            "authorization" => $data['authorization'],
                            "cache-control"=>'no-cache'];
                    $this->curl->setHeaders($headers);
                }
                $this->curl->post($url,$data);
            }   
            $response = $this->curl->getBody();
            $result =[];
            if (strpos($response, '{') !== false) {
                $response=  strstr($response, '{');
                $result = $this->jsonHelper->jsonDecode($response); //get result array
                if ((strpos($url, 'fast2sms') !== false) && is_array($result)) { 
                      $result['body'] = $data[$this->datahelper->getSmsProfileApiSmsBody()];
                }
            }
            elseif (strpos($url, 'ui.netsms.co.in') !== false) {
                $_result = explode("|",$response); //get result array
                $result['status'] = str_replace('<br />', '', $_result[0]);
                $result['body'] = $data[$this->datahelper->getSmsProfileApiSmsBody()];

                if(sizeof($_result) == 3){
                    $result['sid'] = $_result[2];
                }
                else {
                    $result['status'] = 'Failed';
                    $result['error'] = $_result[0];
                }
            }
            else {
                $_result = explode(" ",$response); //get result array

                if (strpos($url, 'msg91') !== false) {
                    $result['status'] = $_result[2];
                    $sid_array = explode("\n", $_result[2]);
                    if(!is_null($sid_array))
                    {
                        $result['status'] =   $sid_array[0];
                    }
                    $result['body'] = $data[$this->datahelper->getSmsProfileApiSmsBody()];
                    $result['sid'] = (isset($_result[32])) ? '' : $_result[31];
                    if(isset($_result[31])){
                        $status_array = explode("\n", $result['sid']);
                        if(!is_null($status_array))
                        {
                             $result['sid'] =  end($status_array);
                        }
                    }
                    if(isset($_result[32])){
                        $result['error_m'] = $_result[32].' '.$_result[33].' '.$_result[34].' '.$_result[35].' '.$_result[36];
                        $result['status'] = __('failed'); 
                    }
                }    
            }    
            
            if(isset($result['error_m'])){ /** case of msg91*/
                $sms->addSmsProfileLog($result , $this->getAdditionalData(), $result['error_m']);
            }  elseif (isset($result['ErrorMessage']) && $result['ErrorMessage'] != "Success") { /** case of smsindihub*/
                $sms->addSmsProfileLog($result , $this->getAdditionalData(), $result['ErrorMessage']); 
            } elseif (isset($result['errors'][0]['description'])) { /*case of message bird */
               $sms->addSmsProfileLog($result , $this->getAdditionalData(), $result['errors'][0]['description']);
            } elseif (isset($result['status_code'])) { /*case of FAST2SMS */
               $sms->addSmsProfileLog($result , $this->getAdditionalData(), $result['status_code'].' - '.$result['message']);
            } else if(isset($result['error'])){ /** case of netsms*/
                $sms->addSmsProfileLog($result , $this->getAdditionalData(), $result['error']);
            } else {
                $sms->addSmsProfileLog($result , $this->getAdditionalData(), is_array($result) ? null :  __('Unable to initialize cURL request'));
            }    
        } catch (\Exception $e) {
           $sms->addSmsProfileLog($result = 'fail', $this->getAdditionalData(), $e->getMessage());
        }
        return $this;
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

    public function getSmsProfileApiParams()
    {
        $apiAdditionalParam = $this->datahelper->getApiAdditionalParam();
        return $apiAdditionalParam;
    }

    public function getApiData()
    {
        $data = $this->datahelper->getSmsProfileApiParams();
        $data[$this->datahelper->getSmsProfileApiTo()] = $this->getToNumber();
        $data[$this->datahelper->getSmsProfileApiSmsBody()] = $this->getMessageContent();
        return $data;
    }

    public function getApiDataForPromotionalSms()
    {
        $datanew = $this->datahelper->getSmsProfileApiParamsForPromotional();
        $datanew[$this->datahelper->getSmsProfileApiTo()] = $this->getToNumber();
        $datanew[$this->datahelper->getSmsProfileApiSmsBody()] = $this->getMessageContent();
        return $datanew;
    }

    public function sendPromotionalSMSViaOtherServices()
    {
        $url = $this->datahelper->getSmsProfileApiUrl();
        if ($this->datahelper->getSmsProfileApiUrlForPromotional()) {
            $url = $this->datahelper->getSmsProfileApiUrlForPromotional();    
        }
        $user  = $this->getApiUser();
        $password = $this->getApiUserPassword();
        $postdata = $this->getApiDataForPromotionalSms();
        $sms  = $this->smsprofilelog->create();

        $getstring ='';
        foreach ($postdata as $key => $value) {
            if($key == $this->datahelper->getSmsProfileApiSmsBody()){
                 $getstring .= $key."=".urlencode($value)."&";
            } else {
                $getstring .= $key."=".$value."&";
            }            
        }
       
        $getstring = substr($getstring, 0, -1);
        $_url = $url.'?'.$getstring; 

        try {
            if ($this->datahelper->getAPIRequestInGet()) { 
                $this->curl->get($_url);
            } else {
                $this->curl->setCredentials($user,$password); 
                $this->curl->setOption(CURLOPT_RETURNTRANSFER, true);
                $this->curl->setOption(CURLOPT_SSL_VERIFYPEER, false);
                $this->curl->setOption(CURLOPT_HEADER, true);
                $this->curl->setOption(CURLOPT_ENCODING, 'UTF-8');
                if(isset($postdata['authorization'])  && (strpos($url, 'fast2sms') !== false)){
                    $headers = ["authorization" => $postdata['authorization']];
                    $this->curl->setHeaders($headers);
                }
                $this->curl->post($url,$postdata);
            }    
            $response = $this->curl->getBody();
            $result =[];
            if (strpos($response, '{') !== false) {
                $response=  strstr($response, '{');
                $result = $this->jsonHelper->jsonDecode($response); //get result array
                if ((strpos($url, 'fast2sms') !== false) && is_array($result)) { 
                      $result['body'] = $postdata[$this->datahelper->getSmsProfileApiSmsBody()];
                }
            }
            elseif (strpos($url, 'ui.netsms.co.in') !== false) {
                $_result = explode("|",$response); //get result array
                $result['status'] = str_replace('<br />', '', $_result[0]);
                $result['body'] = $postdata[$this->datahelper->getSmsProfileApiSmsBody()];

                if(sizeof($_result) == 3){
                    $result['sid'] = $_result[2];
                }
                else {
                    $result['error'] = $_result[1];
                }
            }
            else {
                $_result = explode(" ",$response); //get result array
                
                if (strpos($url, 'msg91') !== false) {
                    
                    $result['status'] = $_result[2];
                    $sid_array = explode("\n", $_result[2]);
                    if(!is_null($sid_array))
                    {
                        $result['status'] =   $sid_array[0];
                    }
                    $result['body'] = $postdata[$this->datahelper->getSmsProfileApiSmsBody()];
                    $result['sid'] = (isset($_result[32])) ? '' : $_result[31];
                    if(isset($_result[31])){
                        $status_array = explode("\n", $result['sid']);
                        if(!is_null($status_array))
                        {
                             $result['sid'] =  end($status_array);
                        }
                    }
                    if(isset($_result[32])){
                        $result['error_m'] = $_result[32].' '.$_result[33].' '.$_result[34].' '.$_result[35].' '.$_result[36];
                        $result['status'] = __('failed');
                    }
                }    
            }    
            
            if(isset($result['error_m'])){ /** case of msg91*/
                $sms->addSmsProfileLog($result , $this->getAdditionalData(), $result['error_m']);
            } elseif (isset($result['ErrorMessage']) && $result['ErrorMessage'] != "Success") { /** case of smsindihub*/
                $sms->addSmsProfileLog($result , $this->getAdditionalData(), $result['ErrorMessage']); 
            } elseif (isset($result['errors'][0]['description'])) { /*case of message bird */
               $sms->addSmsProfileLog($result , $this->getAdditionalData(), $result['errors'][0]['description']);
            } elseif (isset($result['status_code'])) { /*case of FAST2SMS */
               $sms->addSmsProfileLog($result , $this->getAdditionalData(), $result['status_code'].' - '.$result['message']);
            } else if(isset($result['error'])){ /** case of netsms*/
                $sms->addSmsProfileLog($result , $this->getAdditionalData(), $result['error']);
            } else {
                $sms->addSmsProfileLog($result , $this->getAdditionalData(), is_array($result) ? null :  __('Unable to initialize cURL request'));
            }    
        } catch (\Exception $e) {
           $sms->addSmsProfileLog($result = 'fail', $this->getAdditionalData(), $e->getMessage());
        }
        return $this;
    }

    public function sendSmsProfileOTPViaOtherServicesXML()
    {
        $url = $this->datahelper->getSmsProfileApiUrl();
        $user  = $this->getApiUser();
        $password = $this->getApiUserPassword();
        $data = $this->getApiDataXML();
        $sms  = $this->smsprofilelog->create();
        try {
            $this->curl->setCredentials($user,$password); 
            $this->curl->setOption(CURLOPT_RETURNTRANSFER, true);
            $this->curl->setOption(CURLOPT_SSL_VERIFYPEER, false);
            $this->curl->setOption(CURLOPT_HEADER, true);
            $this->curl->setOption(CURLOPT_ENCODING, '');
            $this->curl->setOption(CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            if(isset($data['authorization'])  && (strpos($url, 'fast2sms') !== false)){
                $headers = ["Content-Type" => "application/x-www-form-urlencoded",
                        "authorization" => $data['authorization'],
                        "cache-control"=>'no-cache'];
                $this->curl->setHeaders($headers);
            }
            $this->curl->post($url,$data);
            $response = $this->curl->getBody();            
            
            $result = substr($response, strpos($response, "<?xm"));
            
            $xml = simplexml_load_string($result);
            $sms->addSmsProfileLogXML($xml , $this->getAdditionalDataXML(), ((string) $xml->status == 0 ) ? null : (string) $xml->message );   

        }
        catch (\Exception $e) {
           $sms->addSmsProfileLogXML($result = 'fail', $this->getAdditionalDataXML(), $e->getMessage());
        }
        return $this;
    }

     public function sendPromotionalSMSViaOtherServicesXML()
    {
        $url = $this->datahelper->getSmsProfileApiUrl();
        if ($this->datahelper->getSmsProfileApiUrlForPromotional()) {
            $url = $this->datahelper->getSmsProfileApiUrlForPromotional();    
        }
        $user  = $this->getApiUser();
        $password = $this->getApiUserPassword();
        $postdata = $this->getApiDataForPromotionalSmsXML();        
        $sms  = $this->smsprofilelog->create();
        try {
            $this->curl->setCredentials($user,$password); 
            $this->curl->setOption(CURLOPT_RETURNTRANSFER, true);
            $this->curl->setOption(CURLOPT_SSL_VERIFYPEER, false);
            $this->curl->setOption(CURLOPT_HEADER, true);
            $this->curl->setOption(CURLOPT_ENCODING, '');
            $this->curl->setOption(CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            if(isset($postdata['authorization'])  && (strpos($url, 'fast2sms') !== false)){
                $headers = ["authorization" => $postdata['authorization']];
                $this->curl->setHeaders($headers);
            }
            $this->curl->post($url,$postdata);
            $response = $this->curl->getBody();
            $result = substr($response, strpos($response, "<?xm"));
            
            $xml = simplexml_load_string($result);
            $sms->addSmsProfileLogXML($xml , $this->getAdditionalDataXML(), ((string) $xml->status == 0 ) ? null : (string) $xml->message );
              
        } catch (\Exception $e) {
           $sms->addSmsProfileLogXML($result = 'fail', $this->getAdditionalDataXML(), $e->getMessage());
        }
        return $this;
    }

    public function getApiDataXML()
    {
        $data = $this->datahelper->getSmsProfileApiParams();
       // $data[$this->datahelper->getSmsProfileApiTo()] = 545797827;
         $data[$this->datahelper->getSmsProfileApiTo()] = $this->getToNumber();
        // $data[$this->datahelper->getSmsProfileApiSmsBody()] = 'samplesms';
        $data[$this->datahelper->getSmsProfileApiSmsBody()] = $this->getMessageContent();
        $this->phoneId = $data[$this->datahelper->getSmsProfileApiTo()].'-'. $this->generateRandomStringForPhoneId();
        $xmlData = "<?xml version='1.0' encoding='UTF-8'?>
                         <sms>
                              <user>
                              <username>".$this->getApiUser()."</username>
                              <password>".$this->getApiUserPassword()."</password>
                          </user>
                          <source>".$data['source']."</source>
                          <destinations>
                            <phone id='".$this->phoneId."'>".$data[$this->datahelper->getSmsProfileApiTo()]."</phone>
                          </destinations>
                          <message>".$data[$this->datahelper->getSmsProfileApiSmsBody()]."</message>
                        </sms>";                      
        return $xmlData;

    }

    public function getAdditionalDataXML()
    {
        $additionalData = [
            'apiVersion' => $this->getApiVersion(),
            'transactionType' =>   $this->getTransactionType(),
            'phoneId' => $this->phoneId,
            'smsBody' => $this->getMessageContent(),
            'toNumber' => (is_array($this->getToNumber())) ? implode(",",$this->getToNumber()) : $this->getToNumber()            
        ];

        return $additionalData;
    }

    public function generateRandomStringForPhoneId()
    {
        $length = 3;
        $characters = array_merge(range('A','Z'), range('a','z'), range('0','9'));       
        $string = '';
        $max = count($characters) - 1;
        for ($i = 0; $i < $length; $i++) {
             $string .= $characters[mt_rand(0, $max)];
        }

        return $string;
    }


    public function getApiDataForPromotionalSmsXML()
    {
        $datanew = $this->datahelper->getSmsProfileApiParamsForPromotional();
        $datanew[$this->datahelper->getSmsProfileApiTo()] = explode(",",$this->getToNumber());
        $datanew[$this->datahelper->getSmsProfileApiSmsBody()] = $this->getMessageContent();
        $_phone = [];
        foreach ($datanew[$this->datahelper->getSmsProfileApiTo()] as $phone) {
           $_phone[] = "<phone>". $phone."</phone>";
         }
        $XMLData ="<?xml version='1.0' encoding='UTF-8'?>
            <bulk>
                <user>
                    <username>".$this->getApiUser()."</username>
                    <password>".$this->getApiUserPassword()."</password>
                </user>
                <messages>
                    <sms>
                        <source>".$datanew['source']."</source>
                        <destinations>";
         foreach ($datanew[$this->datahelper->getSmsProfileApiTo()] as $phone) {
            $XMLData .= "<phone>". $phone."</phone>";
         }
         $XMLData .="</destinations>
                        <message>".$datanew[$this->datahelper->getSmsProfileApiSmsBody()]."</message>
                    </sms>
                </messages>
                <response>0</response>
            </bulk>" ; 
             
        return $XMLData;
    }
}
