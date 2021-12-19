/**
 * Sunbrains
 * @category  Sunbrains
 * @package   Sunbrains_SMSProfile
 * @author    Sunbrains
 */
require([
    'jquery',
    'mage/translate',
    'jquery/validate'],
    function($){
        $.validator.addMethod(
            'validate-comma-separated-emails-profile', function (emaillist) {
               emaillist = emaillist.trim();
                console.log(emaillist);
                if(emaillist.charAt(0) == ',' || emaillist.charAt(emaillist.length - 1) == ','){ return false; }
                var emails = emaillist.split(',');
                var invalidEmails = [];
                for (i = 0; i < emails.length; i++) { 
                    var v = emails[i].trim();
                    console.log(v);
                    if(!Validation.get('validate-email').test(v)) {
                        invalidEmails.push(v);
                    }
                }
                if(invalidEmails.length){ return false; }
                return true;
            }, $.mage.__('Please enter a valid comma seprated email address.'));
    }
);
