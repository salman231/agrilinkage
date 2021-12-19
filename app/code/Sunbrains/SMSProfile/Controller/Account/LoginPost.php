<?php
/**
 * Sunbrains
 * @category  Sunbrains
 * @package   Sunbrains_SMSProfile
 * @author    Sunbrains
 */
 
namespace Sunbrains\SMSProfile\Controller\Account;

use Magento\Customer\Model\Account\Redirect as AccountRedirect;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Framework\Exception\EmailNotConfirmedException;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\State\UserLockedException;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Customer\Model\Customer;
use Sunbrains\SMSProfile\Helper\Data as HelperData;
use Magento\Store\Model\StoreManagerInterface;

class LoginPost extends \Magento\Customer\Controller\Account\LoginPost 
{
    public function __construct(
        Context $context,
        Customer $customer,
        HelperData $datahelper,
        StoreManagerInterface $store,
        Session $customerSession,
        AccountManagementInterface $customerAccountManagement,
        CustomerUrl $customerHelperData,
        Validator $formKeyValidator,
        AccountRedirect $accountRedirect,
        CollectionFactory $customerCollection
    ){
        $this->customerCollection = $customerCollection;
        $this->customer = $customer;
        $this->store = $store;
        $this->datahelper = $datahelper;
        parent::__construct($context,$customerSession,$customerAccountManagement,$customerHelperData,$formKeyValidator,$accountRedirect);
    }
    
    /**
     * Retrieve cookie metadata factory
     *
     * @deprecated 100.1.0
     * @return \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    private function getCookieMetadataFactory()
    {
        if (!$this->cookieMetadataFactory) {
            $this->cookieMetadataFactory = \Magento\Framework\App\ObjectManager::getInstance()->get(
                \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory::class
            );
        }
        return $this->cookieMetadataFactory;
    }

    /**
     * Login post action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        if ($this->session->isLoggedIn() || !$this->formKeyValidator->validate($this->getRequest())) {
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*/*/');
            return $resultRedirect;
        }

        if ($this->getRequest()->isPost()) {
            $login = $this->getRequest()->getPost('login');
            if (is_numeric($login['username']) && $login['otp'] != 1) {
                 $message = __(
                    'Please verify OTP.'
                );
                $this->messageManager->addError($message);
                $this->session->setUsername($login['username']);
                return $this->accountRedirect->getRedirect();
            }
            if (is_numeric($login['username'])) {
                $email ='';
                $customerCollections = $this->getCustomerByPhone($login['username']);
                foreach ($customerCollections as $customer) {
                    $email = $customer->getEmail();
                }
                if ($email == '') {
                    $message = __(
                        'Account with this number doesn\'t exist'
                    );
                    $this->messageManager->addError($message);
                    $this->session->setUsername($login['username']);
                    return $this->accountRedirect->getRedirect();
                }
                $login['username'] = $email;
            } 
            
            if ($this->datahelper->getSmsProfileOtpOnLogin() == 'login_otp' && (!empty($login['username']) && ($login['otp'] == 1))) {
                try {
                    $this->customer->setWebsiteId($this->store->getStore()->getWebsiteId());
                    $customer = $this->customer->loadByEmail($login['username']);
                    $this->session->setCustomerAsLoggedIn($customer);                    
                    
                    $redirectUrl = $this->accountRedirect->getRedirectCookie();
                   
                } catch (EmailNotConfirmedException $e) {
                    $value = $this->customerUrl->getEmailConfirmationUrl($login['username']);
                    $message = __(
                        'This account is not confirmed. <a href="%1">Click here</a> to resend confirmation email.',
                        $value
                    );
                } catch (UserLockedException $e) {
                    $message = __(
                        'You did not sign in correctly or your account is temporarily disabled.'
                    );
                } catch (AuthenticationException $e) {
                    $message = __('You did not sign in correctly or your account is temporarily disabled.');
                } catch (LocalizedException $e) {
                    $message = $e->getMessage();
                } catch (\Exception $e) {
                    // PA DSS violation: throwing or logging an exception here can disclose customer password
                    $this->messageManager->addError(
                        __('An unspecified error occurred. Please contact us for assistance.')
                    );
                } finally {
                    if (isset($message)) {
                        $this->messageManager->addError($message);
                        $this->session->setUsername($login['username']);
                    }
                }

            } else if (!empty($login['username']) && !empty($login['password'])) {
                try {
                    $customer = $this->customerAccountManagement->authenticate($login['username'], $login['password']);
                    $this->session->setCustomerDataAsLoggedIn($customer);
                    $this->session->regenerateId();
                    
                    $redirectUrl = $this->accountRedirect->getRedirectCookie();
                   
                } catch (EmailNotConfirmedException $e) {
                    $value = $this->customerUrl->getEmailConfirmationUrl($login['username']);
                    $message = __(
                        'This account is not confirmed. <a href="%1">Click here</a> to resend confirmation email.',
                        $value
                    );
                } catch (UserLockedException $e) {
                    $message = __(
                        'You did not sign in correctly or your account is temporarily disabled.'
                    );
                } catch (AuthenticationException $e) {
                    $message = __('You did not sign in correctly or your account is temporarily disabled.');
                } catch (LocalizedException $e) {
                    $message = $e->getMessage();
                } catch (\Exception $e) {
                    // PA DSS violation: throwing or logging an exception here can disclose customer password
                    $this->messageManager->addError(
                        __('An unspecified error occurred. Please contact us for assistance.')
                    );
                } finally {
                    if (isset($message)) {
                        $this->messageManager->addError($message);
                        $this->session->setUsername($login['username']);
                    }
                }
            } else {
                $this->messageManager->addError(__('A login and a password are required.'));
            }

            /*if (!(is_numeric($login['username'])) && !empty($login['username'])) {
                if ($this->getCurrentCustomerPhone($login['username']) === null) {
                    $resultRedirect = $this->resultRedirectFactory->create();
                    $resultRedirect->setPath('customer/account/edit');
                    $this->messageManager->addNotice(__('Please enter mobile number.'));
                    return $resultRedirect;
                }
            }*/
        }

        return $this->accountRedirect->getRedirect();
    }

    public function getCustomerByPhone($phone)
    {   
        $customerCollection = $this->customerCollection->create();
        $customerCollection->addAttributeToSelect('*')
                           ->addAttributeToFilter('customer_mobile', $phone)
                           ->load();
        return $customerCollection;
    }


    public function getCurrentCustomerPhone($email)
    {
        $tel = '';
        $customerCollection = $this->customerCollection->create();
        $customerCollection->addAttributeToSelect('*')
                           ->addFieldToFilter('email', $email)
                           ->load();
        foreach ($customerCollection as $_customer) {
            $tel = $_customer->getCustomerMobile();
        }
        if ($tel != '')
        {
            return $tel;
        }
        return null;
    }
}
