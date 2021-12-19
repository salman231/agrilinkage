<?php
/**
 * Sunbrains
 * @category  Sunbrains
 * @package   Sunbrains_SMSProfile
 * @author    Sunbrains
 */

namespace Sunbrains\SMSProfile\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\EmailNotConfirmedException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\InvalidEmailOrPasswordException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Exception\State\ExpiredException;
use Magento\Framework\Exception\State\InputMismatchException;
use Magento\Framework\Exception\State\InvalidTransitionException;
use Magento\Framework\Exception\State\UserLockedException;
use Sunbrains\SMSProfile\Helper\Data as HelperData;
use Sunbrains\SMSProfile\Model\SMSProfileOtpFactory;
use Sunbrains\SMSProfile\Api\SMSProfileTemplatesRepositoryInterface;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Customer\Model\AccountConfirmation;
use Magento\Customer\Model\EmailNotificationInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Customer\Model\Config\Share as ConfigShare;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Framework\Math\Random;
use Magento\Customer\Api\Data\CustomerInterface;
use Psr\Log\LoggerInterface as PsrLogger;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Intl\DateTimeFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Encryption\EncryptorInterface as Encryptor;
use Magento\Framework\Stdlib\StringUtils as StringHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\GuestPaymentMethodManagementInterface;
use Magento\Quote\Model\Quote;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class SMSProfileApiHelper
{
    const NEW_ACCOUNT_EMAIL_REGISTERED = 'registered';
    const NEW_ACCOUNT_EMAIL_REGISTERED_NO_PASSWORD = 'registered_no_password';
    const NEW_ACCOUNT_EMAIL_CONFIRMATION = 'confirmation';
    const XML_PATH_MINIMUM_PASSWORD_LENGTH = 'customer/password/minimum_password_length';
    const XML_PATH_REQUIRED_CHARACTER_CLASSES_NUMBER = 'customer/password/required_character_classes_number';
    const MAX_PASSWORD_LENGTH = 15;

    /**  @var HelperData */
    private $datahelper;

    
    /** @var SMSProfileTemplatesRepositoryInterface */
    private $smsProfileTemplates;

    /**
    * sms profile otp  factory
    *
    * @var SMSProfileOtpFactory
    */
    private $smsProfileOtp;
   
    /**
    * @var JsonHelper
    */
    private $jsonHelper;

    /**
     * @var AuthenticationInterface
     */
    private $authentication;

    /**
     * @var CustomerFactory
     */
    private $customerFactory;

    /**
     * @var ManagerInterface
     */
    private $eventManager;

    /**
     * @var ConfigShare
     */
    private $configShare;

    /**
     * @var AddressRepositoryInterface
     */
    private $addressRepository;

    /**
     * @var Random
     */
    private $mathRandom;

    /**
     * @var EmailNotificationInterface
     */
    private $emailNotification;

    /**
     * @var CustomerRegistry
     */
    private $customerRegistry;

    /**
     * @var Encryptor
     */
    private $encryptor;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**  @var TimezoneInterface */
    private $timezone;

    /**
     *  Initialize service
     * @param SMSProfileOtpFactory $smsProfileOtp
     * @param SMSProfileTemplatesRepositoryInterface $smsProfileTemplates
     * @param HelperData $dataHelper
     */


    public function __construct(
        SMSProfileOtpFactory $smsProfileOtp,
        JsonHelper $jsonHelper,
        SMSProfileTemplatesRepositoryInterface $smsProfileTemplates,
        HelperData $dataHelper,
        CustomerRepositoryInterface $customerRepository,
        CustomerFactory $customerFactory,
        CollectionFactory $customerCollection,
        ConfigShare $configShare,
        StoreManagerInterface $storeManager,
        AddressRepositoryInterface $addressRepository,
        Random $mathRandom,
        PsrLogger $logger,
        Encryptor $encryptor,
        CustomerRegistry $customerRegistry,
        DateTimeFactory $dateTimeFactory,
        AccountConfirmation $accountConfirmation = null,
        ScopeConfigInterface $scopeConfig,
        StringHelper $stringHelper,
        QuoteIdMaskFactory $quoteIdMaskFactory,
        CartRepositoryInterface $cartRepository,
        TimezoneInterface $timezone,
        GuestPaymentMethodManagementInterface $paymentMethodManagement,
        ManagerInterface $eventManager
    ) {
        $this->datahelper = $dataHelper;
        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
        $this->stringHelper = $stringHelper;
        $this->customerRegistry = $customerRegistry;
        $this->smsProfileTemplates = $smsProfileTemplates;
        $this->smsProfileOtp = $smsProfileOtp;
        $this->jsonHelper = $jsonHelper;
        $this->storeManager = $storeManager;
        $this->configShare = $configShare;
        $this->customerCollection = $customerCollection;
        $this->customerRepository = $customerRepository;
        $this->customerFactory = $customerFactory;
        $this->eventManager = $eventManager;
        $this->addressRepository = $addressRepository;
        $this->mathRandom = $mathRandom;
        $this->encryptor = $encryptor;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->cartRepository = $cartRepository;
        $this->timezone = $timezone;
        $this->paymentMethodManagement = $paymentMethodManagement;
        $this->dateTimeFactory = $dateTimeFactory ?: ObjectManager::getInstance()->get(DateTimeFactory::class);
        $this->accountConfirmation = $accountConfirmation ?: ObjectManager::getInstance()
            ->get(AccountConfirmation::class);
    }

    public function validateNumber($mobile)
    {
        if (strlen((string)$mobile) < $this->datahelper->getSmsProfilePhoneMinLength()) {
           throw new NoSuchEntityException(__('Please enter more than or equal to '.$this->datahelper->getSmsProfilePhoneMinLength().' digits'));
        } else if(strlen((string)$mobile) > $this->datahelper->getSmsProfilePhoneMaxLength()) {
           throw new NoSuchEntityException(__('Please enter less than or equal to '.$this->datahelper->getSmsProfilePhoneMaxLength().' digits'));
        }
    }

    public function VerifyOtpForToken($mobile, $otp)
    {
       $result = [];
       $minutes = $this->datahelper->getSmsProfileOTPExpiry();
       $now = $this->timezone->date(null, null, false)->format('Y-m-d H:i:s');
       $now2 = $this->timezone->date(null, null, false)->modify('-' . $minutes . 'minute')->format('Y-m-d H:i:s');
       $smsProfileOtp = $this->smsProfileOtp->create()->getCollection();
       $smsProfileOtp->addFieldToFilter('customer_mobile',$mobile);
       $smsProfileOtp->addFieldToFilter('created_at', array('from' => $now2, 'to' => $now));
       $smsProfileOtp->addFieldToFilter('created_at', array('gteq' => $now2, 'lteq' => $now));
       if ($smsProfileOtp->getSize()) {
            $data = $smsProfileOtp->getLastItem();
            if ($data->getOtpCode() == $otp) {
                $data->delete();
                $result['message'] = __('Verified')->getText();
            } else {
                $result['message'] = __('Not Verified')->getText();
            }
            return $result['message'];
        } else {
            throw new NoSuchEntityException(__('Record not found with given mobile and otp.'));
        }
    }

    public function getCustomerEmail($mobile)
    {
        if(!empty($mobile)) {
            $email ='';
            $customerCollection = $this->customerCollection->create();
            $customerCollection->addAttributeToSelect('*')
                               ->addAttributeToFilter('customer_mobile', $mobile)
                               ->load();
            foreach ($customerCollection as $customer) {
                    $email = $customer->getEmail();
            }
            if ($email == '') { 
                throw new NoSuchEntityException(__('Account with this number doesn\'t exist '));
            } else {
                return $email;
            }
        } else {
            throw new NoSuchEntityException(__('Please enter mobile. '));
        }
    }

    public function authenticateCustomerByMail($email, $websiteId) 
    {
        try {
            $customer = $this->customerRepository->get($email, $websiteId);
        } catch (NoSuchEntityException $e) {
            throw new NoSuchEntityException(__('Invalid login or password.'));
        }
        return $customer;
    }

    public function authenticateCustomer($email, $password, $websiteId)
    {
        try {
            $customer = $this->customerRepository->get($email, $websiteId);
        } catch (NoSuchEntityException $e) {
            throw new NoSuchEntityException(__('Invalid login or password.'));
        }

        $customerId = $customer->getId();
        if ($this->getAuthentication()->isLocked($customerId)) {
            throw new UserLockedException(__('The account is locked.'));
        }
        try {
            $this->getAuthentication()->authenticate($customerId, $password);
        } catch (InvalidEmailOrPasswordException $e) {
            throw new InvalidEmailOrPasswordException(__('Invalid login or password.'));
        }

        if ($customer->getConfirmation() && $this->isConfirmationRequired($customer)) {
            throw new EmailNotConfirmedException(__('This account is not confirmed.'));
        }

        $customerModel = $this->customerFactory->create()->updateData($customer);
        $this->eventManager->dispatch(
            'customer_customer_authenticated',
            ['model' => $customerModel, 'password' => $password]
        );

        $this->eventManager->dispatch('customer_data_object_login', ['customer' => $customer]);

        return $customer;
    }

    /**
     * Get authentication
     *
     * @return AuthenticationInterface
     */
    private function getAuthentication()
    {
        if (!($this->authentication instanceof AuthenticationInterface)) {
            return \Magento\Framework\App\ObjectManager::getInstance()->get(
                \Magento\Customer\Model\AuthenticationInterface::class
            );
        } else {
            return $this->authentication;
        }
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function createAccountWithPasswordHash(CustomerInterface $customer, $hash, $redirectUrl = '')
    {
        if ($customer->getId()) {
            $customer = $this->customerRepository->get($customer->getEmail());
            $websiteId = $customer->getWebsiteId();

            if ($this->isCustomerInStore($websiteId, $customer->getStoreId())) {
                throw new InputException(__('This customer already exists in this store.'));
            }
            // Existing password hash will be used from secured customer data registry when saving customer
        }

        // Make sure we have a storeId to associate this customer with.
        if (!$customer->getStoreId()) {
            if ($customer->getWebsiteId()) {
                $storeId = $this->storeManager->getWebsite($customer->getWebsiteId())->getDefaultStore()->getId();
            } else {
                $storeId = $this->storeManager->getStore()->getId();
            }
            $customer->setStoreId($storeId);
        }

        // Associate website_id with customer
        if (!$customer->getWebsiteId()) {
            $websiteId = $this->storeManager->getStore($customer->getStoreId())->getWebsiteId();
            $customer->setWebsiteId($websiteId);
        }

        // Update 'created_in' value with actual store name
        if ($customer->getId() === null) {
            $storeName = $this->storeManager->getStore($customer->getStoreId())->getName();
            $customer->setCreatedIn($storeName);
        }

        $customerAddresses = $customer->getAddresses() ?: [];
        $customer->setAddresses(null);
        try {
            // If customer exists existing hash will be used by Repository
            $customer = $this->customerRepository->save($customer, $hash);
        } catch (AlreadyExistsException $e) {
            throw new InputMismatchException(
                __('A customer with the same email already exists in an associated website.')
            );
        } catch (LocalizedException $e) {
            throw $e;
        }
        try {
            foreach ($customerAddresses as $address) {
                if ($address->getId()) {
                    $newAddress = clone $address;
                    $newAddress->setId(null);
                    $newAddress->setCustomerId($customer->getId());
                    $this->addressRepository->save($newAddress);
                } else {
                    $address->setCustomerId($customer->getId());
                    $this->addressRepository->save($address);
                }
            }
            $this->customerRegistry->remove($customer->getId());
        } catch (InputException $e) {
            $this->customerRepository->delete($customer);
            throw $e;
        }
        $customer = $this->customerRepository->getById($customer->getId());
        $newLinkToken = $this->mathRandom->getUniqueHash();
        $this->changeResetPasswordLinkToken($customer, $newLinkToken);
        $this->sendEmailConfirmation($customer, $redirectUrl);

        return $customer;
    }

    /**
     * {@inheritDoc}
     */
    public function isCustomerInStore($customerWebsiteId, $storeId)
    {
        $ids = [];
        if ((bool)$this->configShare->isWebsiteScope()) {
            $ids = $this->storeManager->getWebsite($customerWebsiteId)->getStoreIds();
        } else {
            foreach ($this->storeManager->getStores() as $store) {
                $ids[] = $store->getId();
            }
        }

        return in_array($storeId, $ids);
    }

    /**
     * Change reset password link token
     *
     * Stores new reset password link token
     *
     * @param CustomerInterface $customer
     * @param string $passwordLinkToken
     * @return bool
     * @throws InputException
     */
    public function changeResetPasswordLinkToken($customer, $passwordLinkToken)
    {
        if (!is_string($passwordLinkToken) || empty($passwordLinkToken)) {
            throw new InputException(
                __(
                    'Invalid value of "%value" provided for the %fieldName field.',
                    ['value' => $passwordLinkToken, 'fieldName' => 'password reset token']
                )
            );
        }
        if (is_string($passwordLinkToken) && !empty($passwordLinkToken)) {
            $customerSecure = $this->customerRegistry->retrieveSecureData($customer->getId());
            $customerSecure->setRpToken($passwordLinkToken);
            $customerSecure->setRpTokenCreatedAt(
                $this->dateTimeFactory->create()->format(DateTime::DATETIME_PHP_FORMAT)
            );
            $this->customerRepository->save($customer);
        }
        return true;
    }

    /**
     * Send either confirmation or welcome email after an account creation
     *
     * @param CustomerInterface $customer
     * @param string $redirectUrl
     * @return void
     */
    protected function sendEmailConfirmation(CustomerInterface $customer, $redirectUrl)
    {
        try {
            $hash = $this->customerRegistry->retrieveSecureData($customer->getId())->getPasswordHash();
            $templateType = self::NEW_ACCOUNT_EMAIL_REGISTERED;
            if ($this->isConfirmationRequired($customer) && $hash != '') {
                $templateType = self::NEW_ACCOUNT_EMAIL_CONFIRMATION;
            } elseif ($hash == '') {
                $templateType = self::NEW_ACCOUNT_EMAIL_REGISTERED_NO_PASSWORD;
            }
            $this->getEmailNotification()->newAccount($customer, $templateType, $redirectUrl, $customer->getStoreId());
        } catch (MailException $e) {
            // If we are not able to send a new account email, this should be ignored
            $this->logger->critical($e);
        } catch (\UnexpectedValueException $e) {
            $this->logger->error($e);
        }
    }

    /**
     * Get email notification
     *
     * @return EmailNotificationInterface
     * @deprecated 100.1.0
     */
    public function getEmailNotification()
    {
        if (!($this->emailNotification instanceof EmailNotificationInterface)) {
            return \Magento\Framework\App\ObjectManager::getInstance()->get(
                EmailNotificationInterface::class
            );
        } else {
            return $this->emailNotification;
        }
    }

    /**
     * Check if accounts confirmation is required in config
     *
     * @param CustomerInterface $customer
     * @return bool
     * @deprecated
     * @see AccountConfirmation::isConfirmationRequired
     */
    private function isConfirmationRequired($customer)
    {
        return $this->accountConfirmation->isConfirmationRequired(
            $customer->getWebsiteId(),
            $customer->getId(),
            $customer->getEmail()
        );
    }

    /**
     * Create a hash for the given password
     *
     * @param string $password
     * @return string
     */
    public function createPasswordHash($password)
    {
        return $this->encryptor->getHash($password, true);
    }

    /**
     * Make sure that password complies with minimum security requirements.
     *
     * @param string $password
     * @return void
     * @throws InputException
     */
    public function checkPasswordStrength($password)
    {
        $length = $this->stringHelper->strlen($password);
        if ($length > self::MAX_PASSWORD_LENGTH) {
            throw new InputException(
                __(
                    'Please enter a password with at most %1 characters.',
                    self::MAX_PASSWORD_LENGTH
                )
            );
        }
        $configMinPasswordLength = $this->getMinPasswordLength();
        if ($length < $configMinPasswordLength) {
            throw new InputException(
                __(
                    'Please enter a password with at least %1 characters.',
                    $configMinPasswordLength
                )
            );
        }
        if ($this->stringHelper->strlen(trim($password)) != $length) {
            throw new InputException(__('The password can\'t begin or end with a space.'));
        }

        $requiredCharactersCheck = $this->makeRequiredCharactersCheck($password);
        if ($requiredCharactersCheck !== 0) {
            throw new InputException(
                __(
                    'Minimum of different classes of characters in password is %1.' .
                    ' Classes of characters: Lower Case, Upper Case, Digits, Special Characters.',
                    $requiredCharactersCheck
                )
            );
        }
    }

    /**
     * Retrieve minimum password length
     *
     * @return int
     */
    private function getMinPasswordLength()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_MINIMUM_PASSWORD_LENGTH);
    }

    /**
     * Check password for presence of required character sets
     *
     * @param string $password
     * @return int
     */
    private  function makeRequiredCharactersCheck($password)
    {
        $counter = 0;
        $requiredNumber = $this->scopeConfig->getValue(self::XML_PATH_REQUIRED_CHARACTER_CLASSES_NUMBER);
        $return = 0;

        if (preg_match('/[0-9]+/', $password)) {
            $counter++;
        }
        if (preg_match('/[A-Z]+/', $password)) {
            $counter++;
        }
        if (preg_match('/[a-z]+/', $password)) {
            $counter++;
        }
        if (preg_match('/[^a-zA-Z0-9]+/', $password)) {
            $counter++;
        }

        if ($counter < $requiredNumber) {
            $return = $requiredNumber;
        }

        return $return;
    }

    /**
     * {@inheritDoc}
     */
    public function savePaymentInformation(
        $cartId,
        $email,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    ) {
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        /** @var Quote $quote */
        $quote = $this->cartRepository->getActive($quoteIdMask->getQuoteId());

        if ($billingAddress) {
            $billingAddress->setEmail($email);
            $quote->removeAddress($quote->getBillingAddress()->getId());
            $quote->setBillingAddress($billingAddress);
            $quote->setDataChanges(true);
        } else {
            $quote->getBillingAddress()->setEmail($email);
        }
        $this->limitShippingCarrier($quote);

        $this->paymentMethodManagement->set($cartId, $paymentMethod);
        return true;
    }

     /**
     * Limits shipping rates request by carrier from shipping address.
     *
     * @param Quote $quote
     *
     * @return void
     * @see \Magento\Shipping\Model\Shipping::collectRates
     */
    private function limitShippingCarrier(Quote $quote)
    {
        $shippingAddress = $quote->getShippingAddress();
        if ($shippingAddress && $shippingAddress->getShippingMethod()) {
            $shippingDataArray = explode('_', $shippingAddress->getShippingMethod());
            $shippingCarrier = array_shift($shippingDataArray);
            $shippingAddress->setLimitCarrier($shippingCarrier);
        }
    }


    public function savePaymentInformationforUser(
        $cartId,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    )
    {
        if ($billingAddress) {
            /** @var \Magento\Quote\Api\CartRepositoryInterface $quoteRepository */
            $quoteRepository = $this->getCartRepository();
            /** @var \Magento\Quote\Model\Quote $quote */
            $quote = $quoteRepository->getActive($cartId);
            $quote->removeAddress($quote->getBillingAddress()->getId());
            $quote->setBillingAddress($billingAddress);
            $quote->setDataChanges(true);
            $shippingAddress = $quote->getShippingAddress();
            if ($shippingAddress && $shippingAddress->getShippingMethod()) {
                $shippingDataArray = explode('_', $shippingAddress->getShippingMethod());
                $shippingCarrier = array_shift($shippingDataArray);
                $shippingAddress->setLimitCarrier($shippingCarrier);
            }
        }
        $this->paymentMethodManagement->set($cartId, $paymentMethod);
        return true;
    }

    /**
     * Get Cart repository
     *
     * @return \Magento\Quote\Api\CartRepositoryInterface
     * @deprecated 100.2.0
     */
    private function getCartRepository()
    {
        if (!$this->cartRepository) {
            $this->cartRepository = \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Magento\Quote\Api\CartRepositoryInterface::class);
        }
        return $this->cartRepository;
    }

}