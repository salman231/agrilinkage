<?php
/**
 * Sunbrains
 * @category  Sunbrains
 * @package   Sunbrains_SMSProfile
 * @author    Sunbrains
 */

namespace Sunbrains\SMSProfile\Model;

use Sunbrains\SMSProfile\Api\SMSProfileTemplatesRepositoryInterface;
use Sunbrains\SMSProfile\Api\Data\SMSProfileTemplatesInterface;
use Sunbrains\SMSProfile\Model\ResourceModel\SMSProfileTemplates\CollectionFactory;
use Sunbrains\SMSProfile\Model\ResourceModel\SMSProfileTemplates\Collection;
use Sunbrains\SMSProfile\Model\SMSProfileTemplatesFactory;
use Sunbrains\SMSProfile\Model\SMSProfileTemplates;
use Sunbrains\SMSProfile\Model\ResourceModel\SMSProfileTemplates as ResourceModelSmsProfileTemaplate;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;

class SMSProfileTemplatesRepository implements SMSProfileTemplatesRepositoryInterface
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var SMSProfileTemplatesFactory
     */
    private $smsProfileTemplateFactory;

    /**
     * @var \Sunbrains\SMSProfile\Model\ResourceModel\smsProfileTemplate
     */
    private $resourceModel;

    /**
     * SMSTemplatesRepository constructor.
     * @param SMSProfileTemplatesFactory $smsProfileTemplate
     * @param CollectionFactory $collectionFactory
     * @param ResourceModelSmsProfileTemaplate $resourceModel
     */
    
    public function __construct(
        ResourceModelSmsProfileTemaplate $resourceModel,
        SMSProfileTemplatesFactory $smsProfileTemplate,
        CollectionFactory $collectionFactory
    ) {
        $this->resourceModel = $resourceModel;
        $this->smsProfileTemplateFactory = $smsProfileTemplate;
        $this->collectionFactory = $collectionFactory;        
    }

    public function getById($id)
    {
        $smsProfileTemplate = $this->smsProfileTemplateFactory->create();
        $this->resourceModel->load($smsProfileTemplate, $id);
        if (!$smsProfileTemplate->getId()) {
            throw new NoSuchEntityException(__('smsProfileTemplate with id "%1" does not exist.', $id));
        }
        return $smsProfileTemplate;
    }

    public function save(SMSProfileTemplatesInterface $smsProfileTemplate)
    {
        try {
            $this->resourceModel->save($smsProfileTemplate);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($e->getMessage()));
        }

        return $smsProfileTemplate;
    }

    public function delete(SMSProfileTemplatesInterface $smsProfileTemplate)
    {
        try {
            $this->resourceModel->delete($smsProfileTemplate);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($e->getMessage()));
        }

        return true;
    }

    public function getByEventType($eventType, $storeId)
    {
        $smsProfileTemplate = $this->collectionFactory->create();
        $smsProfileTemplate->addFieldToFilter('store_id', $storeId);
        $smsProfileTemplate->addFieldToFilter('event_type', $eventType);
        $smsProfileTemplate->load();
        if ($smsProfileTemplate->getSize() == 0) {
            $smsProfileTemplate = $this->collectionFactory->create();
            $smsProfileTemplate->addFieldToFilter('store_id', 0);
            $smsProfileTemplate->addFieldToFilter('event_type', $eventType);
            $smsProfileTemplate->load();
             if ($smsProfileTemplate->getSize() == 0) {
                return __('SmsProfileTemplate with '.$eventType.' does not exist.')->getText();
             }
        }     
        foreach ($smsProfileTemplate as  $smsProfileTemplate) {
             if (!$smsProfileTemplate->getId()) {
                return __('smsProfileTemplate with event type "%1" does not exist.', $id)->getText();
             }
        }
       return $smsProfileTemplate;
    }
}
