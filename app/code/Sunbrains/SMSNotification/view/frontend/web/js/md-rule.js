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
 
require(
    [
        'jquery',
        'Magento_Ui/js/lib/validation/validator'
    ],function ($,validator) {
    "use strict";

        validator.addRule('min_digit_length',
            
            function (value, params) {
                    return _.isUndefined(value) || value.length === 0 || value.length >= +params;
                },
                $.mage.__('Please enter more than or equal to {0} digits.')
            
        );
        validator.addRule('max_digit_length',
            
            function (value, params) {
                    return !_.isUndefined(value) && value.length <= +params;
                },
                $.mage.__('Please enter less than or equal to {0} digits.')
        );
        return validator;
});