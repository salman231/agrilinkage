<?php
/**
 * Sunbrains
 * @category  Sunbrains
 * @package   Sunbrains_SMSProfile
 * @author    Sunbrains
 */

namespace Sunbrains\SMSProfile\Model\ResourceModel\SMSProfileOtp;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'entity_id';
    protected $_eventPrefix = 'smsprofileotp_collection';
    protected $_eventObject = 'smsprofileotp_collection';

    protected function _construct()
    {
        $this->_init(
            '\Sunbrains\SMSProfile\Model\SMSProfileOtp',
            '\Sunbrains\SMSProfile\Model\ResourceModel\SMSProfileOtp'
        );
    }

    protected function _initSelect()
    {
        parent::_initSelect();
    }
}
