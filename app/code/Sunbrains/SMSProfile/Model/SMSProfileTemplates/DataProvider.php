<?php
/**
 * Sunbrains
 * @category  Sunbrains
 * @package   Sunbrains_SMSProfile
 * @author    Sunbrains
 */

namespace Sunbrains\SMSProfile\Model\SMSProfileTemplates;

use Sunbrains\SMSProfile\Model\ResourceModel\SMSProfileTemplates\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;

/**
 * Class DataProvider
 */
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var CollectionFactory
     */
    protected $collection;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $pageCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $pageCollectionFactory,
        DataPersistorInterface $dataPersistor,
        \Magento\Framework\App\RequestInterface $request,
        array $meta = [],
        array $data = []
    ) {
        $this->_request = $request;
        $this->collection = $pageCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->meta = $this->prepareMeta($this->meta);
    }

    /**
     * Prepares Meta
     *
     * @param array $meta
     * @return array
     */
    public function prepareMeta(array $meta)
    {
        return $meta;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $itemId = $this->_request->getParam('entity_id');
        if (!empty($itemId)) {
            $items = $this->collection->getItems();
            /** @var $page \Sunbrains\SEOPro\Model\Page */
            foreach ($items as $smsprofiletemplates) {
                $this->loadedData[$smsprofiletemplates->getId()] = $smsprofiletemplates->getData();
            }
        }    

        $data = $this->dataPersistor->get('smsprofiletemplate');
        if (!empty($data)) {
            $smsprofiletemplates = $this->collection->getNewEmptyItem();
            $smsprofiletemplates->setData($data->getData());
            $this->loadedData[$smsprofiletemplates->getId()] = $smsprofiletemplates->getData();
            $this->dataPersistor->clear('smsprofiletemplate');
        }
        return $this->loadedData;
    }
}
