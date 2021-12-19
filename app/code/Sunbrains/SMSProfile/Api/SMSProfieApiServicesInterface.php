<?php
/**
 * Sunbrains
 * @category  Sunbrains
 * @package   Sunbrains_SMSProfile
 * @author    Sunbrains
 */

namespace Sunbrains\SMSProfile\Api;

use Magento\Framework\Exception\NoSuchEntityException;

/**
* @api
*/
interface SMSProfieApiServicesInterface
{
    /**
     * Send Otp To Customer.
     *
     * @param int $resend
     * @param int $storeId
     * @param int $mobile
     * @param string $eventType
     * @return string OTP created
     * @throws NoSuchEntityException
     */
    public function SendOtpToCustomer($resend, $storeId, $mobile, $eventType);

    /**
     * Customer Login with OTP only.
     *
     * @param int $mobile
     * @param string $otp
     * @param int $websiteId
     * @return string
     * @throws NoSuchEntityException
     */
    public function createCustomerTokenWithOtp($mobile, $otp, $websiteId);

    /**
     * Customer Login with OTP With Password .
     *
     * @param int $mobile
     * @param string $otp
     * @param string $password
     * @param int $websiteId
     * @return string
     * @throws NoSuchEntityException
     */
    public function createCustomerTokenWithOtpPassword($mobile, $otp, $password, $websiteId);

    /**
     * Create customer account with otp. Perform necessary business operations like sending email.
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @param string $password
     * @param int $mobile
     * @param string $otp
     * @param string $redirectUrl
     * @return \Magento\Customer\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createAccountWithOtp(
        \Magento\Customer\Api\Data\CustomerInterface $customer,
        $password = null,
        $mobile,
        $otp,
        $redirectUrl = ''
    );
    

    /**
     * update customer account with otp. Perform necessary business operations.
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @param string $password
     * @param int $mobile
     * @param string $otp
     * @param int $websiteId
     * @param string $redirectUrl
     * @return \Magento\Customer\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updateAccountWithOtp(
        \Magento\Customer\Api\Data\CustomerInterface $customer,
        $password = null,
        $mobile,
        $otp,
        $websiteId,
        $redirectUrl = ''
    );

    /**
     * Send an email to the customer with a password reset link.
     *
     * @param int $mobile
     * @param string $otp
     * @param string $template
     * @param int $websiteId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function initiatePasswordResetWithOTP($mobile, $otp, $template, $websiteId);

    /**
     * Set payment information and place order for a specified cart With OTP.
     *
     * @param string $cartId
     * @param string $email
     * @param int $mobile
     * @param string $otp
     * @param \Magento\Quote\Api\Data\PaymentInterface $paymentMethod
     * @param \Magento\Quote\Api\Data\AddressInterface|null $billingAddress
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @return int Order ID.
     */
    public function savePaymentInformationAndPlaceOrderWithOtp(
        $cartId,
        $email,
        $mobile,
        $otp,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    );

    /**
     * Set payment information and place order for a specified cart with OTP for user.
     *
     * @param int $cartId
     * @param int $mobile
     * @param string $otp
     * @param \Magento\Quote\Api\Data\PaymentInterface $paymentMethod
     * @param \Magento\Quote\Api\Data\AddressInterface|null $billingAddress
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @return int Order ID.
     */
    public function savePaymentInformationAndPlaceOrderWithOtpForUser(
        $cartId,
        $mobile,
        $otp,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    );
  
}
