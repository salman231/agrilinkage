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

require([
    'jquery',
    'mage/translate',
    'jquery/validate'],
    function($){
        $.validator.addMethod(
            'custom-validate-length', function (v, elm) {
               var reMax = new RegExp(/^maximum-length-[0-9]+$/),
                    reMin = new RegExp(/^minimum-length-[0-9]+$/),
                    validator = this,
                    result = true,
                    length = 0;


                $.each(elm.className.split(' '), function (index, name) {
                    if (name.match(reMax) && result) {
                        length = name.split('-')[2];
                        result = v.length <= length;
                        validator.validateMessage =
                            $.mage.__('Please enter less than or equal to %1 digit.').replace('%1', length);
                    }

                    if (name.match(reMin) && result && !$.mage.isEmpty(v)) {
                        length = name.split('-')[2];
                        result = v.length >= length;
                        validator.validateMessage =
                            $.mage.__('Please enter more than or equal to %1 digit.').replace('%1', length);
                    }
                });

                return result;
            }, function () {
                return this.validateMessage;
            });
    }
);