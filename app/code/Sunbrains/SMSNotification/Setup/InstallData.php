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

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Sunbrains\SMSNotification\Model\SMSTemplatesFactory;

class InstallData implements InstallDataInterface
{
    /**
     * @var CustomerSetupFactory
     */
    private $customerSetupFactory;

    /**
     * @var SMSTemplatesFactory
     */
    private $smsTemplates;
     
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
        SMSTemplatesFactory $smsTemplates,
        AttributeSetFactory $attributeSetFactory
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->smsTemplates = $smsTemplates;
        $this->attributeSetFactory = $attributeSetFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {

        $setup->startSetup();
        
        /** Add Templates in sunbrains_smstemplates Table  Start */
        $this->addSmsTemplates();
        /** Add Templates in sunbrains_smstemplates Table  End */

        $setup->endSetup();
    }
 
    public function addSmsTemplates()
    {
        $smsTemplate = array();
        $smsTemplate[0] = [
            'template_name' =>'Customer New Order Templates For All Store View',
             'template_content' => 'Dear {firstname} {lastname}, Thank you for your order.You have placed order with id {order_id} and amounting {total}. Your order items are {orderitem} . You will receive your order within 7-8 business working day.',
            'event_type' =>'customer_neworder',
            'store_id' =>0,
        ];

        $smsTemplate[1] = [
            'template_name' =>'Customer Invoice Proceed Template For All Store View',
            'template_content' => 'Dear {firstname} {lastname}, Thank you for your payment. We have received payment of your order {order_id} and amount is {total}. Your order items are {orderitem} .',
            'event_type' =>'customer_invoice',
            'store_id' =>0,
        ];

        $smsTemplate[2] = [
            'template_name' =>'Customer Creditmemo  Processed Template For All Store View',
            'template_content' => 'Dear {firstname} {lastname},Your refund request for  Order {order_id} has been accepted.',
            'event_type' =>'customer_creditmemo',
            'store_id' =>0,
        ];

        $smsTemplate[3] = [
            'template_name' =>'Customer Shipment Create Template For All Store View',
            'template_content' => 'Dear {firstname} {lastname},Your Order {order_id} has been shipped. you will receive order by today or tomorrow. Your oreder items are {orderitem} .',
            'event_type' =>'customer_shipment',
            'store_id' =>0,
        ];

        $smsTemplate[4] = [
            'template_name' =>'Customer Order Cancel Template For All Store View',
            'template_content' => 'Dear {firstname} {lastname},Your Order {order_id} has been cancelled due to item is not available.',
            'event_type' =>'customer_order_cancel',
            'store_id' =>0,
        ];

        $smsTemplate[5] = [
            'template_name' =>'Customer Contact Template For All Store View',
            'template_content' => 'Dear {name} ,Thank you for contacting us. You have Contact us for "{comment}". we will respond you soon.',
            'event_type' =>'customer_contact',
            'store_id' =>0,
        ];

        $smsTemplate[6] = [
            'template_name' =>'Admin Neworder Template For All Store View',
            'template_content' => 'Dear Admin,New Order {order_id} with amount of {total} placed in your {store}',
            'event_type' =>'admin_new_order',
            'store_id' =>0,
        ];

        $smsTemplate[7] = [
            'template_name' =>'Admin New Customer Register Template For All Store View',
            'template_content' => 'Dear Admin,New Customer is registered in your {store}',
            'event_type' =>'admin_new_customer',
            'store_id' =>0,
        ];

        $smsTemplate[8] = [
            'template_name' =>'Admin Customer Contact Template For All Store View',
            'template_content' => 'Dear Admin, Customer {name}  has contacted with  {comment}.  ',
            'event_type' =>'admin_customer_contact',
            'store_id' =>0,
        ];

        $smsTemplate[9] = [
            'template_name' =>'Shipment Tracking For All Store View',
            'template_content' => 'Dear, we have shipped your Order {order_id}. we have shipped your order from {trackingtitle}.your tracking number is {tracknumber}. ',
            'event_type' =>'customer_shipment_tracking',
            'store_id' =>0,
        ];

        /**
         * Insert default crosslinks
         */
        foreach ($smsTemplate as $data) {
            $this->createSmsTemplates()->setData($data)->save();
        }
    }

    /**
     * Create smsTemplate
     *
     * @return smsTemplate
     */
    public function createSmsTemplates()
    {
        return $this->smsTemplates->create();
    }
}
