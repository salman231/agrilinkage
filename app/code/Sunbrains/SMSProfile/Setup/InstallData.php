<?php
/**
 * Sunbrains
 * @category  Sunbrains
 * @package   Sunbrains_SMSProfile
 * @author    Sunbrains
 */
 
namespace Sunbrains\SMSProfile\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Sunbrains\SMSProfile\Model\SMSProfileTemplatesFactory;

class InstallData implements InstallDataInterface
{

    /**
     * @var CustomerSetupFactory
     */
    private $customerSetupFactory;

     /**
     * @var SMSProfileTemplatesFactory
     */
    private $smsProfileTemplates;
     
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
        SMSProfileTemplatesFactory $smsProfileTemplates,
        AttributeSetFactory $attributeSetFactory
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->smsProfileTemplates = $smsProfileTemplates;
        $this->attributeSetFactory = $attributeSetFactory;
    }  

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        /** Add customer Eav Attribute Start */
        $this->AddCustomerAttribute($setup);
        /** Add customer Eav Attribute End */

        /** Add Templates in sunbrains_smstemplates Table  Start */
        $this->AddSmsTemplates();
        /** Add Templates in sunbrains_smstemplates Table  End */

        $setup->endSetup();
    }  

    public function AddCustomerAttribute($setup)
    {
        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
         
        $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();
         
        /** @var $attributeSet AttributeSet */
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);
         
        $customerSetup->addAttribute(Customer::ENTITY, 'customer_mobile', [
            'type' => 'varchar',
            'label' => 'Customer Mobile',
            'input' => 'text',
            'backend' => \Sunbrains\SMSProfile\Model\Attribute\Backend\Mobile::class,
            'required' => false,
            'visible' => true,
            'user_defined' => true,
            'position' =>999,
            'system' => 0,
            'unique' => true,
        ]);
         
        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'customer_mobile')
        ->addData([
            'attribute_set_id' => $attributeSetId,
            'attribute_group_id' => $attributeGroupId,
            'is_system' => 0,
            'is_used_in_grid' => true,
            'is_visible_in_grid' => true,
            'is_filterable_in_grid' => true,
            'is_searchable_in_grid' => true,
            'used_in_forms' => ['adminhtml_customer','customer_account_create','customer_account_edit','checkout_register','adminhtml_checkout'],
        ]);
         
        $attribute->save();
    }

    public function AddSmsTemplates()
    {
        $smsProfileTemplate = array();

        $smsProfileTemplate[0] = [
            'template_name' =>'Customer Signup otp sms For All Store View',
            'template_content' => 'Your otp for signup is {otpCode} .Please do not share it with others.',
            'event_type' =>'customer_signup_otp',
            'store_id' =>0,
        ];

        $smsProfileTemplate[1] = [
            'template_name' =>'Customer login otp sms For All Store View',
            'template_content' => 'Your otp for login is {otpCode} .Please do not share it with others.',
            'event_type' =>'customer_login_otp',
            'store_id' =>0,
        ];

        $smsProfileTemplate[2] = [
            'template_name' =>'Customer account update otp sms For All Store View',
            'template_content' => 'Your otp for update account is {otpCode} .Please do not share it with others.',
            'event_type' =>'customer_account_edit_otp',
            'store_id' =>0,
        ];
        
        $smsProfileTemplate[3] = [
            'template_name' =>'OTP For Cod Payment For All Store View',
            'template_content' => 'Your otp for cod payment  is {otpCode} .Please do not share it with others.',
            'event_type' =>'cod_otp',
            'store_id' =>0,
        ];

        $smsProfileTemplate[4] = [
            'template_name' =>'OTP For Cod Payment For All Store View',
            'template_content' => 'Your otp for cod payment  is {otpCode} .Please do not share it with others.',
            'event_type' =>'cod_otp',
            'store_id' =>0,
        ];

        $smsProfileTemplate[4] = [
            'template_name' =>'OTP For Forgot Password With All Store View',
            'template_content' => 'Your otp for forgot password is {otpCode} .Please do not share it with others.',
            'event_type' =>'forgot_password_otp',
            'store_id' =>0,
        ];

        /**
         * Insert default crosslinks
         */
        foreach ($smsProfileTemplate as $data) {
            $this->createSmsProfileTemplates()->setData($data)->save();
        }
    }

    /**
     * Create smsTemplate
     *
     * @return smsTemplate
     */
    public function createSmsProfileTemplates()
    {
        return $this->smsProfileTemplates->create();
    }
 }
