<?php
/**
 * Sunbrains
 * Copyright (C) 2019 Sunbrains <info@sunbrains.com>
 *
 * @category Sunbrains
 * @package Sunbrains_SMSNotification
 * @copyright Copyright (c) 2019 Mage Delight (http://www.sunbrains.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Sunbrains <info@sunbrains.com>
 */
 
namespace Sunbrains\SMSNotification\Model;

use Sunbrains\SMSNotification\Api\Data\SMSTemplatesInterface;
use Magento\Framework\Model\AbstractModel;

class SMSTemplates extends AbstractModel implements SMSTemplatesInterface
{
    
    const CACHE_TAG = 'smsTemplates';

    protected $_cacheTag = 'smsTemplates';
    
    protected $_eventPrefix = 'smsTemplates';
    
    protected function _construct()
    {
        $this->_init('Sunbrains\SMSNotification\Model\ResourceModel\SMSTemplates');
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
    public function setTemplateContent($templateContent)
    {
        return $this->setData('template_content ', $templateContent);
    }

    /**
     * @return string
     */
    public function getTemplateContent()
    {
        return $this->getData('template_content ');
    }

    /**
     * @param string $eventType
     * @return $this
     */
    public function setEventType($eventType)
    {
        return $this->setData('event_type ', $eventType);
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
