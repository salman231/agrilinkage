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

namespace Sunbrains\SMSNotification\Model\ResourceModel\SMSTemplates;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'entity_id';
    protected $_eventPrefix = 'smstemplates_collection';
    protected $_eventObject = 'smstemplates_collection';

    protected function _construct()
    {
        $this->_init(
            '\Sunbrains\SMSNotification\Model\SMSTemplates',
            '\Sunbrains\SMSNotification\Model\ResourceModel\SMSTemplates'
        );
    }

    protected function _initSelect()
    {
        parent::_initSelect();
    }
}
