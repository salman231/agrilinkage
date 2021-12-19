<?php

namespace Coderzon\ProductExpiery\Cron;

class Expiery
{

        /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    protected $productCollectionFactory;
    
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->productCollectionFactory = $productCollectionFactory;
    }

    
	public function execute()
	{
		$enabled = $this->scopeConfig->getValue('coderzon_productexpiery/general/enabled');
		if(!$enabled){
			return false;
		}
		
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$objDate = $objectManager->create('Magento\Framework\Stdlib\DateTime\DateTime');
		$now = $objDate->timestamp();
		$start = date('Y-m-d' . ' 00:00:00', $now);
		$end = date('Y-m-d' . ' 23:59:59', $now);
		$productCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
		$collection = $productCollection->create()->addAttributeToSelect('*');
		$collection->addFieldToFilter('product_expiry_date', array('from' => $start, 'to' => $end));
		$productAction = $objectManager->get('Magento\Catalog\Model\Product\Action');
		
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/productcron.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
		
		foreach ($collection as $product) {
			$productAction->getResource()->updateAttributes(
                    [
                        $product->getId(),
                    ],
                    [
                        'status' => \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED,
                    ],
                    1
                );
			$logger->info('product sku '.$product->getSku());
		}
	}
}

