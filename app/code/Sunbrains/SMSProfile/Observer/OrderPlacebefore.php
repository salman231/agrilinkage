<?php
/**
 * Sunbrains
 * @category  Sunbrains
 * @package   Sunbrains_SMSProfile
 * @author    Sunbrains
 */

namespace Sunbrains\SMSProfile\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\UrlFactory;
use Magento\Framework\Exception\CouldNotSaveException;

class OrderPlacebefore implements ObserverInterface
{
   
    private $messageManager;

    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\ResponseFactory $responseFactory,
        UrlFactory $urlFactory,
        \Sunbrains\SMSProfile\Helper\Data $helper
    ) {
        $this->request = $request;
        $this->messageManager = $messageManager;
        $this->helper = $helper;
        $this->urlModel = $urlFactory->create();
        $this->_responseFactory = $responseFactory;
       
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
         $post =  $this->request->getPost();
         $order = $observer->getOrder();
         $codOtp = 1;
         $address = $order->getShippingAddress();
         $tel = $address->getTelephone();
         $payment = $order->getPayment();
         $title = $payment->getMethod();
         $paymentAdditionalInformation = $payment->getAdditionalInformation();
         if(isset($paymentAdditionalInformation ['codotp'])) {
            $codOtp = $paymentAdditionalInformation ['codotp'];
         }
         if($codOtp!= 1 && $this->helper->getOtpForCOD() && $this->helper->getModuleStatus() && $tel != '' && $title == 'cashondelivery'){
                 throw new CouldNotSaveException(__('Please verify OTP.'));
                return false;
         } else {
            return $this;   
         }  
    }
}
