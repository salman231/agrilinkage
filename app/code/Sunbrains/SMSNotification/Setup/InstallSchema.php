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

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;

class InstallSchema implements InstallSchemaInterface
{
    /* @codingStandardsIgnoreStart */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        /* @codingStandardsIgnoreEnd */
        $installer = $setup;
        $installer->startSetup();

        // Get sunbrains_smslog table
        $tableName = $installer->getTable('sunbrains_smslog');
        // Check if the table already exists
        if ($installer->getConnection()->isTableExists($tableName) != true) {
            // Create sunbrains_smslog table
            $table = $installer->getConnection()
                ->newTable($tableName)
                ->addColumn(
                    'entity_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'Entity Id'
                )
                ->addColumn(
                    's_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'message SId'
                )
                ->addColumn(
                    'api_service',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Api Service'
                )
                ->addColumn(
                    'recipient_phone',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    ['nullable' => false],
                    'Recipient Phone Number'
                )
                ->addColumn(
                    'transaction_type',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => true, 'default' => null],
                    'Transaction Type'
                )
                ->addColumn(
                    'message_body',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    '64k',
                    ['nullable' => true, 'default' => null],
                    'Message Body'
                )
                ->addColumn(
                    'status',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => true, 'default' => null],
                    'Sms Status'
                )
                ->addColumn(
                    'is_error',
                    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false],
                    'Result Is Error'
                )
                ->addColumn(
                    'error_message',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => true, 'default' => null],
                    'Sms Error Message'
                )
                ->setComment('SMSNotification Log Table');
            $installer->getConnection()->createTable($table);

            $installer->getConnection()->addIndex(
                $installer->getTable('sunbrains_smslog'),
                $setup->getIdxName(
                    $installer->getTable('sunbrains_smslog'),
                    ['s_id ','api_service'],
                    AdapterInterface::INDEX_TYPE_FULLTEXT
                ),
                [
                    's_id',
                    'api_service',
                    'recipient_phone',
                    'transaction_type',
                    'message_body',
                    'status',
                    'error_message'
                ],
                AdapterInterface::INDEX_TYPE_FULLTEXT
            );
        }

        // Get sunbrains_smstemplates table
        $tableName = $installer->getTable('sunbrains_smstemplates');
        // Check if the table already exists
        if ($installer->getConnection()->isTableExists($tableName) != true) {
             // Create sunbrains_smstemplates table
            $table = $installer->getConnection()
                ->newTable($tableName)
                ->addColumn(
                    'entity_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'Entity Id'
                )
                ->addColumn(
                    'template_name',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Template Name'
                )
                ->addColumn(
                    'template_content',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    '64k',
                    ['nullable' => true, 'default' => null],
                    'Template Content'
                )
                ->addColumn(
                    'event_type',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => true, 'default' => null],
                    'Sms Event Type'
                )
                ->addColumn(
                    'store_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => true, 'default' => null],
                    'Store Id'
                )
                ->setComment('SMSNotification Templates Table');
                $installer->getConnection()->createTable($table);

                 $installer->getConnection()->addIndex(
                     $installer->getTable('sunbrains_smstemplates'),
                     $setup->getIdxName(
                         $installer->getTable('sunbrains_smstemplates'),
                         ['template_name ','template_content'],
                         AdapterInterface::INDEX_TYPE_FULLTEXT
                     ),
                     [
                        'template_name',
                        'template_content',
                        'event_type',
                     ],
                     AdapterInterface::INDEX_TYPE_FULLTEXT
                 );
        }
        
        $installer->endSetup();
    }
}
