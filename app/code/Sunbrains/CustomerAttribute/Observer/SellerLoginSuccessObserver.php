<?php

namespace Sunbrains\CustomerAttribute\Observer;

use Magento\Framework\Event\ObserverInterface;

class SellerLoginSuccessObserver implements ObserverInterface
{
    /**
     * @var \Webkul\Marketplace\Helper\Data
     */
    protected $_helper;


    /**
     * @param \Webkul\Marketplace\Helper\Data $helper
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Model\Url $customerUrl
     * @param \Magento\Framework\UrlInterface $urlBuilder
     */
    public function __construct(
        \Webkul\Marketplace\Helper\Data $helper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\Url $customerUrl,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory,
        //\Sunbrains\Loginhistory\Helper\Data $loginHistoryhelper,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->_helper = $helper;
        $this->_session = $customerSession;
        $this->_customerUrl = $customerUrl;
        $this->_urlBuilder = $urlBuilder;
        $this->resultRedirectFactory = $resultRedirectFactory;
        //$this->loginHistoryhelper = $loginHistoryhelper;
        $this->messageManager = $messageManager;

    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $customer = $observer->getEvent()->getCustomer();
        $seller = $this->_helper->getSellerStatus($customer->getId());

        if ($seller) {
            $url = $this->_urlBuilder->getUrl("marketplace/account/dashboard");
            $this->_session->setBeforeAuthUrl($url);
            $this->_session->setAfterAuthUrl($url);
            //return $this;
        }

        return $this;
    }
}