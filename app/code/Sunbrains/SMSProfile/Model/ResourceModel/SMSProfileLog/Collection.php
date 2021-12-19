<?php
/**
 * Sunbrains
 * @category  Sunbrains
 * @package   Sunbrains_SMSProfile
 * @author    Sunbrains
 */

namespace Sunbrains\SMSProfile\Model\ResourceModel\SMSProfileLog;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'entity_id';
    protected $_eventPrefix = 'smsprofilelog_collection';
    protected $_eventObject = 'smsprofilelog_collection';

    protected function _construct()
    {
        $this->_init(
            '\Sunbrains\SMSProfile\Model\SMSProfileLog',
            '\Sunbrains\SMSProfile\Model\ResourceModel\SMSProfileLog'
        );
    }

    protected function _initSelect()
    {
        parent::_initSelect();
    }

    /**
     * Update Data for given condition for collection
     *
     * @param int|string $limit
     * @param int|string $offset
     * @return array
     */
    public function setTableRecords($condition, $columnData)
    {
        return $this->getConnection()->update(
            $this->getTable('sunbrains_smsprofilelog'),
            $columnData,
            $where = $condition
        );
    }
}
