<?php
/**
 * Sunbrains
 * @category  Sunbrains
 * @package   Sunbrains_SMSProfile
 * @author    Sunbrains
 */

namespace Sunbrains\SMSProfile\Block\Account\Edit;

class Form extends \Magento\Framework\View\Element\Template
{
    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $session
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\ProductMetadataInterface $productmetadata,
        \Magento\Customer\Model\Session $session
    ) {
        $this->session = $session;
        $this->productmetadata = $productmetadata;
        parent::__construct($context);
    }

    public function getMobile()
    {
        if ($this->session->isLoggedIn()) {
            if ($this->session->getCustomer()->getCustomerMobile()) {
                return $this->session->getCustomer()->getCustomerMobile();
            }
            return '';    
        }
        return '';
    } 

    public function getMagentoEdition()
    {
        $magentoVersion = $this->productmetadata;
        return $magentoVersion->getEdition();
    }  
}
