<?php
/**
 * Sunbrains
 * @category  Sunbrains
 * @package   Sunbrains_SMSProfile
 * @author    Sunbrains
 */
 
namespace Sunbrains\SMSProfile\Model;

use Sunbrains\SMSProfile\Api\Data\SMSProfileTemplatesInterface;
use Magento\Framework\Model\AbstractModel;

class SMSProfileTemplates extends AbstractModel implements SMSProfileTemplatesInterface
{
    
    const CACHE_TAG = 'smsProfileTemplates';

    protected $_cacheTag = 'smsProfileTemplates';
    
    protected $_eventPrefix = 'smsProfileTemplates';
    
    protected function _construct()
    {
        $this->_init('Sunbrains\SMSProfile\Model\ResourceModel\SMSProfileTemplates');
    }
    
    public function getDefaultValues()
    {
        $values = [];
        return $values;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        return $this->setData('entity_id', $id);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->getData('entity_id');
    }

    /**
    * @param string $templateName
    * @return $this
    */
    public function setTemplateName($templateName)
    {
        return $this->setData('template_name', $templateName);
    }

    /**
    * @return string
    */
    public function getTemplateName()
    {
        return $this->getData('template_name');
    }

    /**
    * @param string $templateContent
    * @return $this
    */
    public function setTemplateContent ($templateContent)
    {
        return $this->setData('template_content', $templateContent);
    }

    /**
     * @return string
     */
    public function getTemplateContent()
    {
        return $this->getData('template_content');
    }


    /**
    * @param string $eventType
    * @return $this
    */
    public function setEventType($eventType)
    {
        return $this->setData('event_type', $eventType);
    }

    /**
    * @return string
    */
    public function getEventType()
    {
        return $this->getData('event_type');
    }

    /**
    * @param int $storeId
    * @return $this
    */
    public function setStoreId($storeId)
    {
        return $this->setData('store_id', $storeId);
    }

    /**
    * @return int
    */
    public function getStoreId()
    {
        return $this->getData('store_id');
    }
}
