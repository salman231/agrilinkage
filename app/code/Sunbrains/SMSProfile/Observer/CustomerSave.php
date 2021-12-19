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

class CustomerSave implements ObserverInterface
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
         
         if($post['signupOtpValidation'] != 1  && $this->helper->getModuleStatus() && $post['customer_mobile'] != ''){
            $message  =__('Please verify OTP.');
            $this->messageManager->addError($message); 
            $url = $this->urlModel->getUrl('*/*/create', ['_secure' => true]);
            $this->_responseFactory->create()->setRedirect($url)->sendResponse();
            $this->messageManager->addError($message);
            die;           
         } else {
            return $this;   
         }   
    }
}
