<?php
/**
 * Sunbrains
 * @category  Sunbrains
 * @package   Sunbrains_SMSProfile
 * @author    Sunbrains
 */
 
namespace Sunbrains\SMSProfile\Controller\Ajax;

use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Framework\Exception\EmailNotConfirmedException;
use Magento\Framework\Exception\InvalidEmailOrPasswordException;
use Magento\Framework\App\ObjectManager;
use Magento\Customer\Model\Account\Redirect as AccountRedirect;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Customer\Model\Customer;
use Sunbrains\SMSProfile\Helper\Data as HelperData;
use Magento\Store\Model\StoreManagerInterface;

class Login extends \Magento\Customer\Controller\Ajax\Login
{
    public function __construct(
        Context $context,
        Customer $customer,
        HelperData $datahelper,
        CollectionFactory $customerCollection,
        StoreManagerInterface $store,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Json\Helper\Data $helper,
        AccountManagementInterface $customerAccountManagement,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        CookieManagerInterface $cookieManager = null,
        CookieMetadataFactory $cookieMetadataFactory = null
    ){
        $this->customerCollection = $customerCollection;
        $this->customer = $customer;
        $this->datahelper = $datahelper;
        $this->store = $store;
        $this->cookieManager = $cookieManager ?: ObjectManager::getInstance()->get(
            CookieManagerInterface::class
        );
        $this->cookieMetadataFactory = $cookieMetadataFactory ?: ObjectManager::getInstance()->get(
            CookieMetadataFactory::class
        );
        parent::__construct($context,$customerSession,$helper,$customerAccountManagement,$resultJsonFactory,$resultRawFactory);
    }

    /**
     * Login registered users and initiate a session.
     *
     * Expects a POST. ex for JSON {"username":"user@magento.com", "password":"userpassword"}
     *
     * @return \Magento\Framework\Controller\ResultInterface
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        $credentials = null;
        $httpBadRequestCode = 400;

        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();
        try {
            $credentials = $this->helper->jsonDecode($this->getRequest()->getContent());
        } catch (\Exception $e) {
            return $resultRaw->setHttpResponseCode($httpBadRequestCode);
        }
        if (!$credentials || $this->getRequest()->getMethod() !== 'POST' || !$this->getRequest()->isXmlHttpRequest()) {
            return $resultRaw->setHttpResponseCode($httpBadRequestCode);
        }

        $response = [
            'errors' => false,
            'message' => __('Login successful.')
        ];
        if (is_numeric($credentials['username']) && $credentials['otp'] != 1) { 
                $response = [
                    'errors' => true,
                    'message' => __('Please verify OTP.')
                ];
        } else {
            try {
                if (is_numeric($credentials['username'])) {
            		$customerCollections = $this->getCustomerByPhone($credentials['username']);
                    foreach ($customerCollections as $_customer) {
                        $credentials['username'] = $_customer->getEmail();
                    }
            	}
                if ($this->datahelper->getSmsProfileOtpOnLogin() == 'login_otp' && (!empty($credentials['username']) && ($credentials['otp'] == 1))) {

                    $this->customer->setWebsiteId($this->store->getStore()->getWebsiteId());
                    $customer = $this->customer->loadByEmail($credentials['username']);
                    $this->customerSession->setCustomerAsLoggedIn($customer);
                    $this->customerSession->regenerateId();

                } else {
                    $customer = $this->customerAccountManagement->authenticate(
                        $credentials['username'],
                        $credentials['password']
                    );
                    $this->customerSession->setCustomerDataAsLoggedIn($customer);
                    $this->customerSession->regenerateId();
                }    
                $redirectRoute = $this->getAccountRedirect()->getRedirectCookie();
                if ($this->cookieManager->getCookie('mage-cache-sessid')) {
                    $metadata = $this->cookieMetadataFactory->createCookieMetadata();
                    $metadata->setPath('/');
                    $this->cookieManager->deleteCookie('mage-cache-sessid', $metadata);
                }
                if (!$this->getScopeConfig()->getValue('customer/startup/redirect_dashboard') && $redirectRoute) {
                    $response['redirectUrl'] = $this->_redirect->success($redirectRoute);
                    $this->getAccountRedirect()->clearRedirectCookie();
                }
            } catch (EmailNotConfirmedException $e) {
                $response = [
                    'errors' => true,
                    'message' => $e->getMessage()
                ];
            } catch (InvalidEmailOrPasswordException $e) {
                $response = [
                    'errors' => true,
                    'message' => $e->getMessage()
                ];
            } catch (LocalizedException $e) {
                $response = [
                    'errors' => true,
                    'message' => $e->getMessage()
                ];
            } catch (\Exception $e) {
                $response = [
                    'errors' => true,
                    'message' => __('Invalid login or password.')
                ];
            }
        }    
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($response);
    }

    public function getCustomerByPhone($phone)
    {   
        $customerCollection = $this->customerCollection->create();
        $customerCollection->addAttributeToSelect('*')
                           ->addAttributeToFilter('customer_mobile', $phone)
                           ->load();
        return $customerCollection;
    }
}