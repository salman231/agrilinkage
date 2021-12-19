<?php
/**
 * Sunbrains
 * @category  Sunbrains
 * @package   Sunbrains_SMSProfile
 * @author    Sunbrains
 */

namespace Sunbrains\SMSProfile\Block\Account\Dashboard;

class Info extends \Magento\Customer\Block\Account\Dashboard\Info
{

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
     * @param \Magento\Customer\Helper\View $helperView
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $session,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        \Magento\Customer\Helper\View $helperView,
        array $data = []
    ) {
        $this->session = $session;
        parent::__construct($context, $currentCustomer, $subscriberFactory, $helperView,  $data);
    }

    public function getCustomerMobile()
    {
        if ($this->session->isLoggedIn()) {
            if ($this->session->getCustomer()->getCustomerMobile()) {
                return $this->session->getCustomer()->getCustomerMobile();
            }
            return '';    
        }
        return '';
    }
}
