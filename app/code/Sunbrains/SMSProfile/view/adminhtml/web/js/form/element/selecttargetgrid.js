/**
 * Sunbrains
 * @category  Sunbrains
 * @package   Sunbrains_SMSProfile
 * @author    Sunbrains
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
            var selectgrid = uiRegistry.get('index = custom');
            var samplcsv = uiRegistry.get('index = download');
            
            if (selectgrid.visibleValue == value) {
                selectgrid.show();
            } else {
                selectgrid.hide();
            }
            
            if (samplcsv.visibleValue == value) {
                samplcsv.show();
            } else {
                samplcsv.hide();
            }

            $('.customer_container').hide();
            $('.customer_group_container').hide();
            
            switch(value) {
                case 'customer':
                    $('.customer_container').show();
                    $('.csv').hide();
                    break;
                case 'customer_group':
                    $('.customer_group_container').show();
                    $('.csv').hide();
                    break;
                case 'custom':
                    $('.customer_container').hide();
                    $('.customer_group_container').hide();
                    $('.csv').hide();
                    break;
                case 'csv':
                    $('.csv').show();
                    break;
                default: 
                    $('.csv').hide();
                    $('.customer_container').hide();
                    $('.customer_group_container').hide();
            }
        }
    });
});
