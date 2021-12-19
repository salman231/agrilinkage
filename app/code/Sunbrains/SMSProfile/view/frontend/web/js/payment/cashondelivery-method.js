/**
 * Sunbrains
 * @category  Sunbrains
 * @package   Sunbrains_SMSProfile
 * @author    Sunbrains
 */

/* @api */
define([
    'jquery',
    'Magento_Checkout/js/view/payment/default',
    'Magento_Checkout/js/model/quote'
], function ($,Component,quote) {
    'use strict';
    return Component.extend({
        defaults: {
            template: 'Sunbrains_SMSProfile/payment/cashondelivery'
        },
        /**
         * Init component
         */
        initialize: function () {
              var self = this;
              this._super();            

        },
        getData: function() {
             return {
                 'method': this.item.method,
                 'po_number': null,
                 'additional_data': {
                    "codotp": $(document).find("#cashondelivery_codotp").val()
                 }
             };
         },

        /**
         * Returns payment method instructions.
         *
         * @return {*}
         */
        getInstructions: function () {
            return window.checkoutConfig.payment.instructions[this.item.method];
        }
        
    });
    
});
