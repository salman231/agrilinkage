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
 
namespace Sunbrains\SMSNotification\Setup;
 
use Magento\Framework\Setup\UninstallInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
 
class Uninstall implements UninstallInterface
{
    const TBL_SMSLOG = 'sunbrains_smslog';
    const TBL_SMSTEMPLATES = 'sunbrains_smstemplates';

    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $tableToDrop = [
            self::TBL_SMSLOG,
            self::TBL_SMSTEMPLATES
        ];
        
        $this->_dropTable($setup, $tableToDrop);
 
        $setup->endSetup();
    }

    protected function _dropTable($setup, $tableName)
    {
        $connection = $setup->getConnection();
        
        foreach ($tableName as $tableName) {
            $connection->dropTable($connection->getTableName($tableName));
        }
    }
}
