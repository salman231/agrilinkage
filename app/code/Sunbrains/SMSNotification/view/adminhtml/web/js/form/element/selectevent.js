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

define([
    'underscore',
    'jquery',
    'uiRegistry',
    'Magento_Ui/js/form/element/select'
], function (_, $, uiRegistry, select) {
    'use strict';
    return select.extend({

        initialize: function () {
           this._super();

           this.showHideFields(this.value(), 'initialize');
            
           return this;
        },

        onUpdate: function (value) {
            this.showHideFields(value, 'update');

            return this._super();
        },

        showHideFields: function(value, action) {
            
            $('div[data-index=template_content]').children('.admin__field-note').html('123');
            switch(value) {
                case 'customer_contact':
                    $('div[data-index=template_content]').find('.admin__field-note span').html('Enter your default message. You can use {name} for Name, and {comment} for Comments.');
                    break;
                case "admin_new_customer": 
                    $('div[data-index=template_content]').find('.admin__field-note span').html('Enter your default message. You can use {name} for Name,{username} for User Name or Email,{store} for store name and {url} for login url.');
                    break;
                case "customer_shipment_tracking": 
                    $('div[data-index=template_content]').find('.admin__field-note span').html('Enter your default message. You can use {order_id} for Order Number, {trackingtitle} for Tracking title and {tracknumber} for Track Number.');
                    break; 
                case "admin_customer_contact":
                    $('div[data-index=template_content]').find('.admin__field-note span').html('Enter your default message. You can use {name} for Name, and {comment} for Comments.');
                    break;    
                default:
                    $('div[data-index=template_content]').find('.admin__field-note span').html('Enter your default message. You can use {firstname} for Firstname, {lastname} for Lastname,{order_id} for Order Number, {total} for Total Amount and {orderitem} for Order Items.');
                    break;
            }
        }
    });
});