<?php
/**
 * Sunbrains
 * @category  Sunbrains
 * @package   Sunbrains_SMSProfile
 * @author    Sunbrains
 */
 
namespace Sunbrains\SMSProfile\Block\Adminhtml\SmsPromotional\Container;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;

class Customer extends Template
{
    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'smspromotional/container/customer.phtml';

    /**
     * @var \Magento\Catalog\Block\Adminhtml\Category\Tab\Product
     */
    protected $blockGrid;

    /**
     * AssignCustomer constructor.
     *
     * @param Context  $context
     * @param array    $data
     */
    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /*
     * Retrieve instance of grid block
     *
     * @return \Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBlockGrid()
    {

        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                'Sunbrains\SMSProfile\Block\Adminhtml\SmsPromotional\Container\Tab\Customer',
                'smspromotional.customer.grid'
            );
        }
        return $this->blockGrid;
    }

    /**
     * Return HTML of grid block
     *
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getBlockGrid()->toHtml();
    }
}
