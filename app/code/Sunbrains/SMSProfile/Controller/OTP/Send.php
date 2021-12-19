<?php
/**
 * Sunbrains
 * @category  Sunbrains
 * @package   Sunbrains_SMSProfile
 * @author    Sunbrains
 */
 
namespace Sunbrains\SMSProfile\Controller\OTP;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Sunbrains\SMSProfile\Helper\Data as HelperData;
use Sunbrains\SMSProfile\Model\SMSProfileOtpFactory;
use Sunbrains\SMSProfile\Model\SMSProfileService;
use Sunbrains\SMSProfile\Api\SMSProfileTemplatesRepositoryInterface;
use Magento\Framework\Controller\Result\JsonFactory as ResultJsonFactory;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Sunbrains\SMSProfile\Model\ResourceModel\SMSProfileOtp\CollectionFactory as SmsOtpCollectionFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class Send extends Action
{
    /**  @var HelperData */
    private $datahelper;

     /** @var SMSProfileService */
    private $smsProfileService;

    /** @var SMSProfileTemplatesRepositoryInterface */
    private $smsProfileTemplates;

    /**
    * sms profile otp  factory
    *
    * @var SMSProfileOtpFactory
    */
    private $smsProfileOtp;

    /** @var ResultJsonFactory */
    private $resultJsonFactory;

    /**  @var TimezoneInterface */
    private $timezone;

    /**
     * @var SmsOtpCollectionFactory
     */
    private $collection;

    /**
     * @param Context $context    
     * @param SMSProfileService $smsProfileService
     * @param SMSProfileOtpFactory $smsProfileOtp
     * @param ResultJsonFactory $resultJsonFactory
     * @param SMSProfileTemplatesRepositoryInterface $smsProfileTemplates
     * @param HelperData $dataHelper
     */


    public function __construct(
        Context $context,
        TimezoneInterface $timezone,
        SMSProfileService $smsProfileService,
        SMSProfileOtpFactory $smsProfileOtp,
        SmsOtpCollectionFactory $collection,
        ResultJsonFactory $resultJsonFactory,
        SMSProfileTemplatesRepositoryInterface $smsProfileTemplates,
        CollectionFactory $customerCollection,
        HelperData $dataHelper
    ) {
        parent::__construct($context);
        $this->datahelper = $dataHelper;
        $this->collection = $collection;
        $this->smsProfileService = $smsProfileService;
        $this->smsProfileTemplates = $smsProfileTemplates;
        $this->smsProfileOtp = $smsProfileOtp;
        $this->timezone = $timezone;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->customerCollection = $customerCollection;
    }

    public function execute()
    {   
        $otp = $this->getGeneratedOTP();
        $minutes = $this->datahelper->getSmsProfileOTPExpiry();
        $now = $this->timezone->date(null, null, false)->format('Y-m-d H:i:s');
        $now2 = $this->timezone->date(null, null, false)->modify('-' . $minutes . 'minute')->format('Y-m-d H:i:s');
        $_smsProfileOtp = $this->collection->create();
        $_smsProfileOtp->addFieldToFilter('customer_mobile',$this->getToNumber());
        $_smsProfileOtp->addFieldToFilter('created_at', array('from' => $now2, 'to' => $now));
        $_smsProfileOtp->addFieldToFilter('created_at', array('gteq' => $now2, 'lteq' => $now));
        $_smsProfileOtp->setOrder('entity_id','DESC');
        $_smsProfileOtp->getFirstItem();
        
        if (in_array($this->getTransactionType(), ['customer_login_otp','forgot_password_otp'])){
            if($this->validateCustomerByPhone($this->getToNumber())) {
                $result = $this->resultJsonFactory->create();    
                $result->setData(['Success' => __('Account with this number doesn\'t exist')]);
                return $result;
            }
        }
        $sms = $this->smsProfileTemplates->getByEventType(
                $this->getTransactionType(), $this->datahelper->getCurrentStoreId()
            );
        if(is_string($sms)) {
            $result = $this->resultJsonFactory->create();    
            $result->setData(['Success' => __('Not able find SMS template')]);            
        } else {
            $data['otpCode'] = $otp;
            if($_smsProfileOtp->getSize() > 0) {
                $SmsOTP = $_smsProfileOtp->getLastItem();
                $data['otpCode'] = $SmsOTP->getOtpCode();    
            }
            
            $_message = $sms->getData('template_content');
            $message =$this->setSMSBody($_message,$data);
            $result = $this->resultJsonFactory->create();
            $this->smsProfileService->setToNumber($this->getToNumber());
            $this->smsProfileService->setMessageContent($message);
            $this->smsProfileService->setTransactionType($this->getTransactionType());
            $this->smsProfileService->setApiVersion($this->getApiVersion());
            try {
                $this->callSmsSending();
                if($_smsProfileOtp->getSize() == 0) {
                    $smsProfileOtp =  $this->smsProfileOtp->create();
                    $smsProfileOtp->setOtpCode($otp);
                    $smsProfileOtp->setCustomerMobile($this->getToNumber()); 
                    $smsProfileOtp->setResend($this->getIdResend()); 
                    $smsProfileOtp->setCreatedAt($now); 
                    $smsProfileOtp->setUpdatedAt($now); 
                    $smsProfileOtp->save(); 
                }
                $result->setData(['Success' => __('success')]);
            } catch (Exception $e) {
                $result->setData(['Success' => __('Not able to send SMS')]);
            }
        }    
        return $result;
    }

    public function setSMSBody($message, $data)
    {
        $keywords   = [
            '{otpCode}'
        ];
        $message = str_replace($keywords, $data, $message);
        return $message;
    }

    private function getApiVersion()
    {
        return  $this->datahelper->getSmsProfileApiGateWay();
    }

    /**  @return string */

    private function getTransactionType()
    {
        $_data =  $this->getRequest()->getPostValue();
        return $_data['eventType'];
    }

    private function callSmsSending()
    {
        if ($this->getApiVersion() == 'Twilio Api Service') {
            $this->smsProfileService->sendOTPSmsWithTwilio();
        } else {
            if ($this->datahelper->getApiReauestResponseXML() ) {
                $this->smsProfileService->sendSmsProfileOTPViaOtherServicesXML();
            } else {
                $this->smsProfileService->sendSmsProfileOTPViaOtherServices();
            }    
        }
    }

    /**  @return string */
    private function getGeneratedOTP()
    {
        return $this->datahelper->generateOTP();
    }

    /**  @return string */

    private function getToNumber()
    {
        $_data =  $this->getRequest()->getPostValue();
        return $_data['mobile'];
    }

    /**  @return string */

    private function getIdResend()
    {
        $_data =  $this->getRequest()->getPostValue();
        return $_data['resend'];
    }

    public function validateCustomerByPhone($phone)
    {   
        $customerCollection = $this->customerCollection->create();
        $customerCollection->addAttributeToSelect('*')
                           ->addAttributeToFilter('customer_mobile', $phone)
                           ->load();
        if($customerCollection->getSize() == 0) {
            return true;
        }
        return false;
    }
}
