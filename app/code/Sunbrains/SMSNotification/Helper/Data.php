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

namespace Sunbrains\SMSNotification\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\Locale\CurrencyInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHElper
{
    const XML_PATH_ENABLE = 'sunbrainssmsnotification/general/enable';
    const XML_PATH_GATEWAY = 'sunbrainssmsnotification/general/api_gateway';
    const XML_PATH_API_ENDPOINT = 'sunbrainssmsnotification/general/api_endpoints';
    const XML_PATH_API_CREDENTIAL = 'sunbrainssmsnotification/general/api_credential';
    const XML_PATH_API_TO = 'sunbrainssmsnotification/general/api_to';
    const XML_PATH_API_MESSAGE = 'sunbrainssmsnotification/general/api_message';
    const XML_PATH_API_COUNTRY = 'sunbrainssmsnotification/general/api_country';
    const XML_PATH_API_ADDITINAL_PARAM = 'sunbrainssmsnotification/general/api_params';
    const XML_PATH_API_ENDPOINT_STATUS = 'sunbrainssmsnotification/general/api_endpoints_status';
    const XML_PATH_API_INITIAL_STATUS = 'sunbrainssmsnotification/general/intial_status';
    const XML_PATH_API_FIAL_STATUS = 'sunbrainssmsnotification/general/fail_status';
    const XML_PATH_API_ERROR_KEY = 'sunbrainssmsnotification/general/error_key';
    const XML_PATH_ACCOUNT_SID = 'sunbrainssmsnotification/general/account_sid';
    const XML_PATH_AUTH_TOKEN = 'sunbrainssmsnotification/general/auth_token';
    const XML_PATH_TWILIO_PHONE = 'sunbrainssmsnotification/general/twilio_phone';
    const XML_PATH_DEFAULT_COUNRTY = 'sunbrainssmsnotification/general/default_country';
    const XML_PATH_NOTIFYADMIN_ENABLE = 'sunbrainssmsnotification/adminSms/notifyadmin';
    const XML_PATH_ADMIN_CONTACT = 'sunbrainssmsnotification/adminSms/admin_no';
    const XML_PATH_ADMIN_EVENTS = 'sunbrainssmsnotification/adminSms/admin_events';
    const XML_PATH_ADMIN_NOTIFYFAILURE = 'sunbrainssmsnotification/adminSms/failurenotification';
    const XML_PATH_ADMIN_NOTIFYTOMAIL = 'sunbrainssmsnotification/adminSms/notifytomail';
    const XML_PATH_ADMIN_NOTIFYFROMMAIL = 'sunbrainssmsnotification/adminSms/notifymailsender';
    const XML_PATH_CUSTOMER_EVENTS = 'sunbrainssmsnotification/customerSms/customer_events';
    const XML_PATH_SMSLOG_ENABLE = 'sunbrainssmsnotification/smslog/enable';
    const XML_PATH_SELECT_CUSTOMER_NO = 'sunbrainssmsnotification/customerSms/customer_no';
    const XML_PATH_PHONE_MAX_LENGTH = 'sunbrainssmsnotification/customerSms/phone_max';
    const XML_PATH_PHONE_MIN_LENGTH = 'sunbrainssmsnotification/customerSms/phone_min';
    const XML_PATH_PHONE_NOTICE = 'sunbrainssmsnotification/customerSms/phone_notice';
    const XML_PATH_SMSLOG_CRON_ENABLE = 'sunbrainssmsnotification/smslog/cron_enable';
    const XML_PATH_BULKSMS_URL = 'sunbrainssmsnotification/general/bilksmsurl';
    const XML_PATH_BULKSMS_USER = 'sunbrainssmsnotification/general/bilksmsusername';
    const XML_PATH_BULKSMS_PASSWORD = 'sunbrainssmsnotification/general/bilksmspassword';

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
        return true;
            /*$this->scopeConfig->getValue(
            self::XML_PATH_ENABLE,
            ScopeInterface::SCOPE_STORE
        );*/
    }

    /**  @return string */

    public function getApiGateway()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GATEWAY,
            ScopeInterface::SCOPE_STORE
        );
    }

   /**  @return string */

    public function getApiEndPoints()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_API_ENDPOINT,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**  @return string */

    public function getApiCredential()
    {
        $credential_array = [];
        $credential = $this->scopeConfig->getValue(
            self::XML_PATH_API_CREDENTIAL,
            ScopeInterface::SCOPE_STORE
        );
        $paramArray =  explode(',', $credential);
        $credential_array[preg_replace('/^([^:]*).*$/', '$1', $paramArray[0])]  = substr($paramArray[0], strpos($paramArray[0], ":") + 1);
        $credential_array[preg_replace('/^([^:]*).*$/', '$1', $paramArray[1])]  = substr($paramArray[1], strpos($paramArray[1], ":") + 1);

        return $credential_array;
    }
    /**  @return string */

    public function getApiRequiredCountryCode()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_API_COUNTRY,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**  @return array */
    public function getApiParams()
    {
        $param = [];
        $data = $this->scopeConfig->getValue(
            self::XML_PATH_API_ADDITINAL_PARAM,
            ScopeInterface::SCOPE_STORE
        );
        $dataArray =  explode(',', $data);
        foreach ($dataArray as $dataArray) {
            $param[preg_replace('/^([^:]*).*$/', '$1', $dataArray)] = substr($dataArray, strpos($dataArray, ":") + 1);
        }
        return $param;
    }

    /**  @return string */

    public function getApiTo()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_API_TO,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**  @return string */

    public function getApiMessage()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_API_MESSAGE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**  @return string */
    
    public function getUrlToChangeStatus()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_API_ENDPOINT_STATUS,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getApiIntialStatus()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_API_INITIAL_STATUS,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getApiFailureStatus()
    {
        $fail =  $this->scopeConfig->getValue(
            self::XML_PATH_API_FIAL_STATUS,
            ScopeInterface::SCOPE_STORE
        );
        return explode(',', $fail);
    }

    public function getApiAdditionalParam()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_API_ADDITINAL_DATA,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getApiErrorkey()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_API_ERROR_KEY,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**  @return string */

    public function getAccountSID()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ACCOUNT_SID,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**  @return string */

    public function getAuthToken()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_AUTH_TOKEN,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**  @return string */

    public function getTwilioPhone()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_TWILIO_PHONE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**  @return string */

    public function getDefaultCountry()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_DEFAULT_COUNRTY,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**  @return string */

    public function getAdminContactNumbers()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ADMIN_CONTACT,
            ScopeInterface::SCOPE_STORE
        );
    }
    
    /** @return bool */

    public function getNotifyFailureStatus()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ADMIN_NOTIFYFAILURE,
            ScopeInterface::SCOPE_STORE
        );
    }
    
    /**  @return array */

    public function getNotifyToMail()
    {
        $toMail =  $this->scopeConfig->getValue(
            self::XML_PATH_ADMIN_NOTIFYTOMAIL,
            ScopeInterface::SCOPE_STORE
        );
        $toMail = explode(",", $toMail);
        return $toMail;
    }

    /**  @return string */

    public function getNotifySenderMail()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ADMIN_NOTIFYFROMMAIL,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**  @return array */

    public function getAdminEvents()
    {
        $admin_events = $this->scopeConfig->getValue(
            self::XML_PATH_ADMIN_EVENTS,
            ScopeInterface::SCOPE_STORE
        );

        $admin_events_array = explode(",", $admin_events);
        return $admin_events_array;
    }

    /**  @return array */

    public function getCustomerEvents()
    {
        $customer_events = $this->scopeConfig->getValue(
            self::XML_PATH_CUSTOMER_EVENTS,
            ScopeInterface::SCOPE_STORE
        );

        $customer_events_array = explode(",", $customer_events);
        return $customer_events_array;
    }

    /**  @return bool */

    public function getSmsLogEnable()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SMSLOG_ENABLE,
            ScopeInterface::SCOPE_STORE
        );
    }

     /** @return bool */

    public function getNotifyAdmin()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NOTIFYADMIN_ENABLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**  @return string */

    public function geSelectedCustomerNumber()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SELECT_CUSTOMER_NO,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**  @return string */

    public function getCustomerNumberMaxLength()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PHONE_MAX_LENGTH,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**  @return string */

    public function getCustomerNumberMinLength()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PHONE_MIN_LENGTH,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**  @return string */

    public function getNoticeBelowTelephone()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PHONE_NOTICE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /** @return bool */

    public function getCronStatus()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SMSLOG_CRON_ENABLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**  @return string */

    public function getBulkSmsUserName()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_BULKSMS_USER,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**  @return string */
    
    public function getBulkSmsPassword()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_BULKSMS_PASSWORD,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**  @return string */
    
    public function getBulkSmsUrl()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_BULKSMS_URL,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getPhoneValidationClass()
    {
        $maxLength = $this->getCustomerNumberMaxLength();
        $minLength = $this->getCustomerNumberMinLength();
        $validateMaxLength ='';
        $validateMinLength ='';
        if ($maxLength) {
            $validateMaxLength ='maximum-length-'.$maxLength;
        }
        if ($minLength) {
            $validateMinLength ='minimum-length-'.$minLength;
        }
        return 'validate-number custom-validate-length '.$validateMaxLength .' '.$validateMinLength;
    }

    /**  @return array */
    public function getOrderData($order)
    {
        $_order = $order->getOrder($order);
        if ($order->getGrandTotal()) {
            $total =$order->getGrandTotal();
        } else {
            $total = $order->getPayment()->getAmountOrdered();
        }
        
        $currency_code = $order->getOrderCurrencyCode();
        $currency_symbol = $this->localecurrency->getCurrency($currency_code)
                                ->getSymbol();

        $orderData          =   [
            'firstname'     =>  $order->getCustomerFirstname(),
            'lastname'      =>  $order->getCustomerLastname(),
            'order_id'      =>  ($_order) ? $_order->getIncrementId() : $order->getIncrementId(),
            'total'         =>  $currency_symbol.$total,
            'orderitem'     =>  $this->getOrderedItems($order->getAllItems()),
            'store'         => $this->getCurrentStoreName()
        ];
        return $orderData;
    }

    /**  @return array */
    public function getOrderedData($order)
    {
        $_order = $order->getOrder($order);
        if ($order->getGrandTotal()) {
            $total =$order->getGrandTotal();
        } else {
            $total = $order->getPayment()->getAmountOrdered();
        }
        
        $currency_code = $order->getOrderCurrencyCode();
        $currency_symbol = $this->localecurrency->getCurrency($currency_code)
                                ->getSymbol();

        $orderData          =   [
            'firstname'     =>  $order->getCustomerFirstname(),
            'lastname'      =>  $order->getCustomerLastname(),
            'order_id'      =>  ($_order) ? $_order->getIncrementId() : $order->getIncrementId(),
            'total'         =>  $currency_symbol.$total,
            'orderitem'     =>  $this->getOrderedItems($order->getAllVisibleItems()),
            'store'         => $this->getCurrentStoreName()
        ];
        return $orderData;
    }

    /**  @return string */
    public function getCurrentStoreName()
    {
        return $this->storeManager->getStore()->getName();
    }

    /**  @return string */
    public function getCurrentStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }

    /**  @return string */
    public function getOrderedItems($items)
    {
        $order_items = [];
        foreach ($items as $item) {
            $order_items[] = $item->getName();
        }

        return implode(",", $order_items);
    }

    /**  @return string */

    public function setOrderMesageText($message, $data)
    {
        $keywords   = [
            '{firstname}',
            '{lastname}',
            '{order_id}',
            '{total}',
            '{orderitem}',
            '{store}'
        ];
        $message = str_replace($keywords, $data, $message);
        return $message;
    }

    public function getExtensionKey()
    {
        return 'ek-magento2-sms-notification';
    }
    
    public function getExtensionDisplayName()
    {
        return 'SMSNotification';
    }
}
