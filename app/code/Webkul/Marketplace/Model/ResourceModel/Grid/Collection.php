<?php

namespace Webkul\Marketplace\Model\ResourceModel\Grid;
use Magento\Customer\Model\ResourceModel\Grid\Collection as customerCollection;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface as Logger;
use Webkul\Marketplace\Model\ResourceModel\Seller\CollectionFactory as collectionFactory;

class Collection extends customerCollection
{

    public function __construct(EntityFactory $entityFactory, Logger $logger, FetchStrategy $fetchStrategy, EventManager $eventManager, $mainTable = 'customer_grid_flat', $resourceModel = \Magento\Customer\Model\ResourceModel\Customer::class,collectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }

    protected function _initSelect()
    {
        parent::_initSelect();

        $this->getSellerIds();
        if($this->getSellerIds()){
            $this->addFieldToFilter('main_table.entity_id', array('nin' => $this->getSellerIds()));
        }

        return $this;
    }

    public function getSellerIds(){

        $collection = $this->collectionFactory->create();
        $sellerIds = [];
        foreach ($collection->getData() as $seller)
        {
            $sellerIds[] = $seller['seller_id'];
        }

        return $sellerIds;
    }
}