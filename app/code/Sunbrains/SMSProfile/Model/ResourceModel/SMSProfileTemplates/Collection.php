<?php
/**
 * Sunbrains
 * @category  Sunbrains
 * @package   Sunbrains_SMSProfile
 * @author    Sunbrains
 */

namespace Sunbrains\SMSProfile\Model\ResourceModel\SMSProfileTemplates;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'entity_id';
    protected $_eventPrefix = 'smsprofiletemplates_collection';
    protected $_eventObject = 'smsprofiletemplates_collection';

    protected function _construct()
    {
        $this->_init(
            '\Sunbrains\SMSProfile\Model\SMSProfileTemplates',
            '\Sunbrains\SMSProfile\Model\ResourceModel\SMSProfileTemplates'
        );
    }

    protected function _initSelect()
    {
        parent::_initSelect();
    }
}
