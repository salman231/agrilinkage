/**
 * Sunbrains
 * @category  Sunbrains
 * @package   Sunbrains_SMSProfile
 * @author    Sunbrains
 */
 
require(
    [
        'jquery',
        'Magento_Ui/js/lib/validation/validator'
    ],function ($,validator) {
    "use strict";

        validator.addRule('min_tel_digit_length',
            
            function (value, params) {
                    return _.isUndefined(value) || value.length === 0 || value.length >= +params;
                },
                $.mage.__('Please enter more than or equal to {0} digits.')
            
        );
        
        validator.addRule('max_tel_digit_length',
            
            function (value, params) {
                    return !_.isUndefined(value) && value.length <= +params;
                },
                $.mage.__('Please enter less than or equal to {0} digits.')
        );
        
        return validator;
});