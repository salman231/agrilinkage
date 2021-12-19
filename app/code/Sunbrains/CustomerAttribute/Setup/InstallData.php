<?php
/**
 * *
 *  * Sunbrains
 *  *
 *  * @category    Sunbrains
 *  * @package     Sunbrains_Loginhistory
 *  * @copyright   Copyright (c) Sunbrains
 *  *
 *
 */

namespace Sunbrains\CustomerAttribute\Setup;

use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Entity\Attribute\Set as AttributeSet;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{

    /**
     * @var CustomerSetupFactory
     */
    protected $customerSetupFactory;

    /**
     * @var AttributeSetFactory
     */
    private $attributeSetFactory;

    /**
     * @param CustomerSetupFactory $customerSetupFactory
     * @param AttributeSetFactory $attributeSetFactory
     */
    public function __construct(
        CustomerSetupFactory $customerSetupFactory,
        AttributeSetFactory $attributeSetFactory
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
    }


    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

        $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();

        /** @var $attributeSet AttributeSet */
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        $customerSetup->addAttribute(Customer::ENTITY, 'parish', [
            'type' => 'varchar',
            'label' => 'Parish',
            'input' => 'select',
            'source' => 'Sunbrains\CustomerAttribute\Model\Customer\Attribute\Source\Paris',
            'required' => false,
            'visible' => true,
            'user_defined' => true,
            'position' => 990,
            'system' => 0,
            'is_used_in_grid' => true,
            'is_visible_in_grid' => true,
            'option'  =>
                ['values' =>
                    ['Trelawny' => 'Trelawny',
                     'Saint Catherine' => 'Saint Catherine',
                     'Saint James' => 'Saint James',
                     'Westmoreland' => 'Westmoreland',
                     'Saint Thomas' => 'Saint Thomas',
                     'Portland' => 'Portland',
                     'Saint Elizabeth' => 'Saint Elizabeth',
                     'Saint Mary' => 'Saint Mary',
                     'Saint Andrew' => 'Saint Andrew',
                     'Clarendon' => 'Clarendon',
                     'Hanover' => 'Hanover',
                     'Manchester' => 'Manchester',
                     'Saint Ann' => 'Saint Ann'
                    ]
                ],

            ]);

        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'parish')
            ->addData([
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'used_in_forms' => ['customer_account_create','adminhtml_customer'],//you can use other forms also
            ]);

        $attribute->save();

        $customerSetup->addAttribute(Customer::ENTITY, 'broker', [
            'type' => 'varchar',
            'label' => 'AgriLinkages Broker',
            'input' => 'select',
            'source' => 'Sunbrains\CustomerAttribute\Model\Customer\Attribute\Source\Broker',
            'required' => false,
            'visible' => true,
            'user_defined' => true,
            'position' => 991,
            'system' => 0,
            'is_used_in_grid' => true,
            'is_visible_in_grid'=> true,
            'option'         => ['values' =>
                ['Cornelia Parker' => 'Cornelia Parker',
                 'Sudane Nugent' => 'Sudane Nugent',
                 'Phillip Myrie' => 'Phillip Myrie',
                 'Oshane Thompson' => 'Oshane Thompson'
                ]
            ],
        ]);

        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'broker')
            ->addData([
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'used_in_forms' => ['customer_account_create','adminhtml_customer'],//you can use other forms also
            ]);

        $attribute->save();

    }
}