<?php
/**
 * Sunbrains
 * @category  Sunbrains
 * @package   Sunbrains_SMSProfile
 * @author    Sunbrains
 */

namespace Sunbrains\SMSProfile\Setup;
 
use Magento\Framework\Setup\UninstallInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Eav\Setup\EavSetupFactory;
 
class Uninstall implements UninstallInterface
{
    const TBL_SMSPROFILELOG = 'sunbrains_smsprofilelog';
    const TBL_SMSPROFILETEMPLATES = 'sunbrains_smsprofiletemplates';
    const TBL_SMSPROFILEOTP = 'sunbrains_smsprofileotp';

    private $eavSetupFactory;

    public function __construct(EavSetupFactory $eavSetupFactory)
    {
         $this->eavSetupFactory = $eavSetupFactory;
    }


    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $tableToDrop = [
            self::TBL_SMSPROFILELOG,
            self::TBL_SMSPROFILETEMPLATES,
            self::TBL_SMSPROFILEOTP,
        ];
        $this->_dropTable($setup, $tableToDrop); 
        $eavSetup = $this->eavSetupFactory->create();
        $eavSetup->removeAttribute(\Magento\Customer\Model\Customer::ENTITY, 'customer_mobile');
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
