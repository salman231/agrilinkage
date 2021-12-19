<?php
namespace WebApi\Software\Block;
use Magento\Framework\View\Element\Template;

class InfoApi extends Template
{
   public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
        \WebApi\Software\Helper\Data $helper,
		array $data = []
    ) {
        $this->dataHelper = $helper;
		parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getAllCommodityRetailPrices()
    {
        return $this->dataHelper->getAllCommodityRetailPrices();
    }
	
	public function isModuleEnabled(){
		return $this->dataHelper->isModuleEnabled();
	}
}
