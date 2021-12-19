<?php
/**
 * Sunbrains
 * @category  Sunbrains
 * @package   Sunbrains_SMSProfile
 * @author    Sunbrains
 */

namespace Sunbrains\SMSProfile\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\Locale\CurrencyInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHElper
{
    const XML_PATH_ENABLE = 'sunbrainssmsprofile/general/enable';
    const XML_PATH_API_GATEWAY = 'sunbrainssmsprofile/general/api_gateway';
    const XML_PATH_TWILIO_ACCOUNT_SID = 'sunbrainssmsprofile/general/account_sid';
    const XML_PATH_TWILIO_ACCOUNT_TOKEN = 'sunbrainssmsprofile/general/auth_token';
    const XML_PATH_TWILIO_PHONE = 'sunbrainssmsprofile/general/twilio_phone';
    const XML_PATH_TWILIO_SERVICE_ID = 'sunbrainssmsprofile/general/twilio_service_id';
    const XML_PATH_API_URL = 'sunbrainssmsprofile/general/api_url';
    const XML_PATH_API_CREDENTIAL = 'sunbrainssmsprofile/general/api_credential';
    const XML_PATH_API_TO = 'sunbrainssmsprofile/general/api_to';
    const XML_PATH_API_BODY = 'sunbrainssmsprofile/general/api_message';
    const XML_PATH_API_COUNTRY = 'sunbrainssmsprofile/general/api_country';
    const XML_PATH_API_PARAMS = 'sunbrainssmsprofile/general/api_params';
    const XML_PATH_API_UPDATEPARAMS = 'sunbrainssmsprofile/general/api_updateparams';
    const XML_PATH_API_URL_PROMOTIONAL = 'sunbrainssmsprofile/general/api_url_promotional';
    const XML_PATH_API_FETCHURL = 'sunbrainssmsprofile/general/api_url_status';
    const XML_PATH_API_PROCESS_STATUS = 'sunbrainssmsprofile/general/process_status';
    const XML_PATH_API_FAIL_STATUS = 'sunbrainssmsprofile/general/fail_status';
    const XML_PATH_API_ERROR = 'sunbrainssmsprofile/general/error_key';
    const XML_PATH_DEFAULT_COUNTRY = 'sunbrainssmsprofile/general/default_country';
    const XML_PATH_FAILURE_NOTIFICATION_ENABLE = 'sunbrainssmsprofile/failurenotification/enable';
    const XML_PATH_NOTIFY_TO_MAIL = 'sunbrainssmsprofile/failurenotification/notifytomail';
    const XML_PATH_NOTIFY_FROM_MAIL = 'sunbrainssmsprofile/failurenotification/notifymailsender';
    const XML_PATH_SMSPROFILELOG_ENABLE = 'sunbrainssmsprofile/smsprofilelog/enable';
    const XML_PATH_SMSPROFILELOG_CRON_ENABLE = 'sunbrainssmsprofile/smsprofilelog/cron_enable';
    const XML_PATH_SMSPROFILELOG_TIMEOUT = 'sunbrainssmsprofile/smsprofilelog/cron_timeout';
    const XML_PATH_PHONENUMBERREQUIRED_SIGNUP = 'sunbrainssmsprofile/otpsetting/required_phone';
    const XML_PATH_PHONE_MAX = 'sunbrainssmsprofile/otpsetting/phone_max';
    const XML_PATH_PHONE_MIN = 'sunbrainssmsprofile/otpsetting/phone_min';
    const XML_PATH_OTP_FORMAT = 'sunbrainssmsprofile/otpsetting/otp_format';
    const XML_PATH_OTP_LENGTH = 'sunbrainssmsprofile/otpsetting/otp_length';
    const XML_PATH_OTP_LOGIN = 'sunbrainssmsprofile/otpsetting/otp_login';
    const XML_PATH_PHONE_NOTE = 'sunbrainssmsprofile/otpsetting/phone_note';
    const XML_PATH_PHONE_EAV = 'sunbrainssmsprofile/otpsetting/phone_eav';
    const XML_PATH_OTP_COD = 'sunbrainssmsprofile/otpsetting/otp_cod';
    const XML_PATH_OTP_EXPIRY = 'sunbrainssmsprofile/otpsetting/otp_expiry';
    const XML_PATH_XML_REQUEST_RESPONSE = 'sunbrainssmsprofile/general/api_xml';
    const XML_PATH_OTP_API_GET_REQUEST = 'sunbrainssmsprofile/general/api_get_request';

    /**  @var StoreManagerInterface */
    private $storeManager;

    /**  @var CurrencyInterface */
    private $localecurrency;
    
    /**
     * Constructor
     * @param Context $context
     * @param CurrencyInterface $localeCurrency
     * @param StoreManagerInterface $storeManager
     */

    public function __construct(
        Context $context,
        CurrencyInterface $localeCurrency,
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
        $this->localecurrency = $localeCurrency;
        parent::__construct($context);
    }

    /** @return bool */

    public function getModuleStatus()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ENABLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /** @return bool */

    public function getFailureNotificationStatus()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_FAILURE_NOTIFICATION_ENABLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /** @return bool */

    public function getSmsProfileLogStatus()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SMSPROFILELOG_ENABLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /** @return bool */

    public function getAPIRequestInGet()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_OTP_API_GET_REQUEST,
            ScopeInterface::SCOPE_STORE
        );
    }


    /** @return bool */

    public function getApiReauestResponseXML()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_XML_REQUEST_RESPONSE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /** @return bool */

    public function getSmsProfileCronStatus()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SMSPROFILELOG_CRON_ENABLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /** @return bool */

    public function getSmsProfilePhoneRequiredOnSignUp()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PHONENUMBERREQUIRED_SIGNUP,
            ScopeInterface::SCOPE_STORE
        );
    }

    /** @return bool */

    public function getOtpForCOD()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_OTP_COD,
            ScopeInterface::SCOPE_STORE
        );
    }

    /** @return string */

    public function getSmsProfileOtpOnLogin()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_OTP_LOGIN,
            ScopeInterface::SCOPE_STORE
        );
    }

    /** @return string */

    public function getSmsProfileApiGateWay()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_API_GATEWAY,
            ScopeInterface::SCOPE_STORE
        );
    }

    /** @return string */

    public function getSmsProfileTwilioAccountSId()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_TWILIO_ACCOUNT_SID,
            ScopeInterface::SCOPE_STORE
        );
    }

    /** @return string */
    
    public function getSmsProfileTwilioAccountToken()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_TWILIO_ACCOUNT_TOKEN,
            ScopeInterface::SCOPE_STORE
        );
    }

    /** @return string */
    
    public function getSmsProfileTwilioPhoneNumber()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_TWILIO_PHONE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /** @return string */
    
    public function getSmsProfileTwilioServiceId()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_TWILIO_SERVICE_ID,
            ScopeInterface::SCOPE_STORE
        );
    }

    /** @return string */
    
    public function getSmsProfileDefaultCountry()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_DEFAULT_COUNTRY,
            ScopeInterface::SCOPE_STORE
        );
    }

    /** @return array */
    
    public function getSmsProfileNotifyToMail()
    {
        $toMail = $this->scopeConfig->getValue(
            self::XML_PATH_NOTIFY_TO_MAIL,
            ScopeInterface::SCOPE_STORE
        );

        $toMail = explode(",",$toMail);
        return $toMail;

    }

    /** @return string */
    
    public function getSmsProfileNotifyFromMail()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NOTIFY_FROM_MAIL,
            ScopeInterface::SCOPE_STORE
        );
    }

    /** @return string */
    
    public function getSmsProfilePhoneMaxLength()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PHONE_MAX,
            ScopeInterface::SCOPE_STORE
        );
    }

    /** @return string */
    
    public function getSmsProfilePhoneMinLength()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PHONE_MIN,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getSmsProfilePhoneValidationClass()
    {
        $maxLength = $this->getSmsProfilePhoneMaxLength();
        $minLength = $this->getSmsProfilePhoneMinLength();
        $validateMaxLength ='';
        $validateMinLength ='';
        if($maxLength)
        {
            $validateMaxLength ='maximum-length-'.$maxLength;
        }
        if($minLength)
        {
            $validateMinLength ='minimum-length-'.$minLength;
        }
        return 'validate-number profile-validate-length '.$validateMaxLength .' '.$validateMinLength;
    }

    /** return string */

    public function getOtpFormat()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_OTP_FORMAT,
            ScopeInterface::SCOPE_STORE
        );
    }

    /** return string */

    public function getOtpLength()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_OTP_LENGTH,
            ScopeInterface::SCOPE_STORE
        );
    }

    /** return string */

    public function getPhoneNote()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PHONE_NOTE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /** return string */

    public function getAddressType()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PHONE_EAV,
            ScopeInterface::SCOPE_STORE
        );
    }

    /** return string */

    public function getTimeoutSeconds()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SMSPROFILELOG_TIMEOUT,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function generateOTP()
    {
        $length = $this->getOtpLength();
        $format = $this->getOtpFormat();
        if($format == 'alphanum'){
            $characters = array_merge(range('A','Z'), range('a','z'), range('0','9'));
        } elseif ($format == 'alpha') {
           $characters = array_merge(range('A','Z'), range('a','z'));
        } else {
            $characters = array_merge(range('0','9'));
        }

        $otp_string = '';
        $max = count($characters) - 1;
        for ($i = 0; $i < $length; $i++) {
             $otp_string .= $characters[mt_rand(0, $max)];
        }

        return $otp_string;
    }

    /**  @return string */
    public function getCurrentStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }

    /**  @return string */
    public function getSmsProfileApiUrl()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_API_URL,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**  @return array */
    public function getSmsProfileApiCredential()
    {
        $credential_array = [];
        $credential = $this->scopeConfig->getValue(
            self::XML_PATH_API_CREDENTIAL,
            ScopeInterface::SCOPE_STORE
        );
        if ($credential) {
            $paramArray =  explode(',', $credential);
            $credential_array[preg_replace('/^([^:]*).*$/', '$1', $paramArray[0])]  = substr($paramArray[0], strpos($paramArray[0], ":") + 1);
            $credential_array[preg_replace('/^([^:]*).*$/', '$1', $paramArray[1])]  = substr($paramArray[1], strpos($paramArray[1], ":") + 1);
        }   

        return $credential_array;
    }

    /**  @return string */
    public function getSmsProfileApiTo()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_API_TO,
            ScopeInterface::SCOPE_STORE
        );
    }


    /**  @return string */
    public function getSmsProfileApiSmsBody()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_API_BODY,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**  @return string */
    public function getSmsProfileApiCountryRequired()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_API_COUNTRY,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**  @return array */
    public function getSmsProfileApiParams()
    {
        $param = [];
        $data = $this->scopeConfig->getValue(
            self::XML_PATH_API_PARAMS,
            ScopeInterface::SCOPE_STORE
        );
        if ($data) {
            $dataArray =  explode(',', $data);
            foreach ($dataArray as $dataArray) {
                $param[preg_replace('/^([^:]*).*$/', '$1', $dataArray)] = substr($dataArray, strpos($dataArray, ":") + 1);
            }
        }    
        return $param;
    }

    /**  @return array */
    public function getSmsProfileApiUpdateParams()
    {
        $param = [];
        $data = $this->scopeConfig->getValue(
            self::XML_PATH_API_UPDATEPARAMS,
            ScopeInterface::SCOPE_STORE
        );
        if ($data) {
            $dataArray =  explode(',', $data);
            foreach ($dataArray as $dataArray) {
                $param[preg_replace('/^([^:]*).*$/', '$1', $dataArray)] = substr($dataArray, strpos($dataArray, ":") + 1);
            }
        }
        return $param;
    }

    /**  @return array */
    public function getSmsProfileApiParamsForPromotional() {
        $param = $this->getSmsProfileApiParams();
        $updateparam = $this->getSmsProfileApiUpdateParams();
        $arrayKeys = array_keys($updateparam);
        foreach ($arrayKeys as $key) {
            if (array_key_exists($key, $param)) {
                $param[$key] = $updateparam[$key] ;
                if($updateparam[$key] === 'false') {
                    unset($param[$key]);
                }
            }
        }
        return $param;
    }
    /**  @return string */
    public function getSmsProfileApiUrlForPromotional()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_API_URL_PROMOTIONAL,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**  @return string */
    public function getSmsProfileApiSmsFetchUrl()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_API_FETCHURL,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**  @return array */
    public function getSmsProfileApiProcessStatus()
    {
        $data = $this->scopeConfig->getValue(
            self::XML_PATH_API_PROCESS_STATUS,
            ScopeInterface::SCOPE_STORE
        );

        return explode(',', $data);
    }

    /**  @return array */
    public function getSmsProfileApiFailureStatus()
    {
        $data =  $this->scopeConfig->getValue(
            self::XML_PATH_API_FAIL_STATUS,
            ScopeInterface::SCOPE_STORE
        );

        return explode(',', $data);
    }

    /**  @return string */
    public function getSmsProfileApiSmsErrorKey()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_API_ERROR,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**  @return string */
    public function getSmsProfileOTPExpiry()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_OTP_EXPIRY,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getSmsProfileMailPhoneValidationClass()
    {
        $maxLength = $this->getSmsProfilePhoneMaxLength();
        $minLength = $this->getSmsProfilePhoneMinLength();
        $validateMaxLength ='';
        $validateMinLength ='';
        if($maxLength)
        {
            $validateMaxLength ='maximum-length-'.$maxLength;
        }
        if($minLength)
        {
            $validateMinLength ='minimum-length-'.$minLength;
        }
        return 'profile-validate-mobile-mail '.$validateMaxLength .' '.$validateMinLength;
    }

}
