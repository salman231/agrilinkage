/**
 * Sunbrains
 * @category  Sunbrains
 * @package   Sunbrains_SMSProfile
 * @author    Sunbrains
 */
require([
        'jquery',
        'mage/url',
        'ko',
        'mage/translate',
        'mage/mage',
        'jquery/validate'
    ],
    function($, url, ko) {

        var configValues = window.checkoutConfig;

        /**  code for custom validation start here */
        $.validator.addMethod(
            'profile-validate-length',
            function(v, elm) {
                var reMax = new RegExp(/^maximum-length-[0-9]+$/),
                    reMin = new RegExp(/^minimum-length-[0-9]+$/),
                    validator = this,
                    result = true,
                    length = 0;


                $.each(elm.className.split(' '), function(index, name) {
                    if (name.match(reMax) && result) {
                        length = name.split('-')[2];
                        result = v.length <= length;
                        validator.validateMessage =
                            $.mage.__('Please enter less than or equal to %1 digits.').replace('%1', length);
                    }

                    if (name.match(reMin) && result && !$.mage.isEmpty(v)) {
                        length = name.split('-')[2];
                        result = v.length >= length;
                        validator.validateMessage =
                            $.mage.__('Please enter more than or equal to %1 digits.').replace('%1', length);
                    }
                });

                return result;
            },
            function() {
                return this.validateMessage;
            });
        $.validator.addMethod(
            'profile-validate-mobile-mail',
            function(v, elm) {
                if (!$.isNumeric(v)) {
                    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
                    var validator = this,
                        result = true;
                    if (!regex.test(v)) {
                        result =  regex.test(v);
                        validator.validateMessage = $.mage.__('Please enter a valid email address (Ex: johndoe@domain.com).');
                    }
                    return result;
                } else {
                    var reMax = new RegExp(/^maximum-length-[0-9]+$/),
                        reMin = new RegExp(/^minimum-length-[0-9]+$/),
                        validator = this,
                        result = true,
                        length = 0;


                    $.each(elm.className.split(' '), function(index, name) {
                        if (name.match(reMax) && result) {
                            length = name.split('-')[2];
                            result = v.length <= length;
                            validator.validateMessage =
                                $.mage.__('Please enter less than or equal to %1 digits.').replace('%1', length);
                        }

                        if (name.match(reMin) && result && !$.mage.isEmpty(v)) {
                            length = name.split('-')[2];
                            result = v.length >= length;
                            validator.validateMessage =
                                $.mage.__('Please enter more than or equal to %1 digits.').replace('%1', length);
                        }
                    });

                    return result;
                }
            },
            function() {
                return this.validateMessage;
            });
        /**  code for custom validation ends here */
        $(document).ready(function() {

            /* code for otp at signup page start here */

            $('#customer_mobile').blur(function() {
                if ($(this).val() != '') {
                    $(document).find('.resendlink').hide();
                    if (!$(this).val()) {
                        //$(this).siblings('.send_otp').hide();
                        $('.send_otp').hide();
                        $('.customer-account-create .actions-toolbar button.action.submit.primary').attr("disabled", false);
                    } else {
                        //$(this).siblings('.Generate OTP').show();
                        $('.send_otp').show();
                        $('.Generate OTP').show();
                        $('.customer-account-create .actions-toolbar button.action.submit.primary').attr("disabled", true);
                    }
                }
            });

            $(".send_otp").on("click", function() {
                var sendotpurl = $('.sendUrl').val();
                var isResend = 0;
                if($(document).find(".smserror").length > 0 ) {
                    $(document).find(".smserror").html('');
                }
                if ($('#customer_mobile').validation('isValid')) {

                    $.ajax({
                        showLoader: true,
                        url: sendotpurl,
                        method: "POST",
                        data: {
                            mobile: $("#customer_mobile").val(),
                            eventType: 'customer_signup_otp',
                            resend: isResend,
                        },
                        dataType: "json"
                    }).done(function(response) {
                        if (response.Success === 'success') {
                            alert('OTP is generated successfully.');
                            if($(".resendlink").length == 0 && $(".field-name-otp").length == 0) {
                                $(".field-name-customer_mobile").after('<div class="resendlink"><a href="javascript:void(0);" class="resendotp" >Resend OTP</a></div><div class="field field-name-otp required"><label class="label" for="otp"><span>Please enter verification code here</span></label>  <div class="control"> <input id="otp" name="otp" value="" title="otp" class="input-text required-entry" data-validate="{required:true}" autocomplete="off" aria-required="true" type="text" novalidate="novalidate" style="width: 200px;"> <button style="display: block;margin-top: 10px;" class="verif_otp action primary" type="button">Verify OTP</button></div></div><span class="otp_text"></span>  ');
                            }
                        } else {
                            if($(".smserror").length == 0 ) {
                                $(".send_otp").after('<p class="smserror">'+response.Success+'</p>');
                            }
                            if($(document).find(".smserror").length > 0 ) {
                                $(document).find(".smserror").html(response.Success);
                            }
                        }

                    });
                }
            });


            $(document).on("click", ".verif_otp", function() {
                var sendotpurl = $('.verifyUrl').val();
                if ($('#otp').val() != '') {
                    $('.otp_text').html('');
                    $.ajax({
                        showLoader: true,
                        url: sendotpurl,
                        method: "POST",
                        data: {
                            otp: $("#otp").val(),
                            mobile: $("#customer_mobile").val()
                        },
                        dataType: "json"
                    }).done(function(response) {
                        if (response) {
                            $('.otp_text').html(response.message);
                            if (response.message == 'Verified') {
                                $('.otp_text').css('color','green');
                                $('.send_otp').hide();
                                $('.resendlink').hide();
                                $('.field-name-otp').hide();
                                $('.signupOtpValidation').val(1);
                                $('.customer-account-create .actions-toolbar button.action.submit.primary').attr("disabled", false);
                            } else {
                                $('.otp_text').css('color','red');
                            }
                        }
                    });
                } else {
                    alert('Please enter OTP');
                }
            });
            /* code for otp at signup page ends here */

            /* code for otp at login page  starts here*/
            $(document).on("blur", ".customer-account-login #email", function() {
                if ($(this).val() != '') {
                    $(document).find('.resendlink').hide();
                    if (!$.isNumeric($(this).val())) {
                        $(document).find('.send_otp_login').hide();
                        $(document).find('.password').show();
                        $('.actions-toolbar #send2').attr("disabled", false);
                    } else {
                        $(document).find('.send_otp_login').show();
                        if ($(document).find('.logintype').val() != 'login_both') {
                            $(document).find('.password').hide();
                        } else {
                            $(document).find('.password').show();
                        }
                        $('.actions-toolbar #send2').attr("disabled", true);
                    }
                }
            });
            $(document).on("blur", ".checkout-index-index #customer-email", function() {
                if ($(this).val() != '') {
                    $(document).find('.resendlink').hide();
                    if (!$.isNumeric($(this).val())) {
                        $(document).find('.send_otp_login').hide();
                        $(document).find('.customer-password-field').show();
                        $('.checkout-index-index .actions-toolbar .login').attr("disabled", false);
                    } else {
                        $(document).find('.send_otp_login').show();
                        if (window.checkoutConfig.otplogin != 'login_both') {
                            $(document).find('.customer-password-field').hide();
                        } else {
                            $(document).find('.customer-password-field').show();
                        }
                        $('.checkout-index-index .actions-toolbar .login').attr("disabled", true);
                    }
                }
            });

            $(document).on("blur", ".checkout-index-index #login-email", function() {
                if ($(this).val() != '') {
                    $(document).find('.resendlink').hide();
                    if (!$.isNumeric($(this).val())) {
                        $(document).find('.send_otp_login').hide();
                        $(document).find('.checkout-index-index .field-password').show();
                        $(document).find('.checkout-index-index .actions-toolbar .action-login').attr("disabled", false);
                    } else {
                        $(document).find('.send_otp_login').show();
                        if (window.checkoutConfig.otplogin != 'login_both') {
                            $(document).find('.checkout-index-index .field-password').hide();
                        } else {
                            $(document).find('.checkout-index-index .field-password').show();
                        }
                        $(document).find('.checkout-index-index .actions-toolbar .action-login').attr("disabled", true);
                    }
                }
            });
            $(document).on("click", ".send_otp_login", function() {
                var sendotpurllogin = $('.sendUrlLogin').val();
                var mobile = $("#email").val();
                if($(document).find(".smserror").length > 0 ) {
                    $(document).find(".smserror").html('');
                }

                if($('#email').length > 0) {
                    var isValidate = false;
                    if ($('.customer-account-login #email').validation('isValid')) {
                        isValidate = true;
                    }
                }

                if($('#email_address').length > 0 && $("#email_address").val() !='') {
                    var mobile = $("#email_address").val();
                    var isValidate = false;
                    if ($('.md-content #email').validation('isValid')) {
                        isValidate = true;
                    }
                }

                if ($('.checkout-index-index #customer-email').length > 0) {
                    sendotpurllogin = url.build('smsprofile/otp/send');
                    mobile = $("#customer-email").val();
                    var isValidate = true;
                    $('.checkout-index-index .actions-toolbar .login').attr("disabled", true);
                }
                if ($(document).find('.checkout-index-index #login-email').length > 0 && $(document).find(".checkout-index-index #login-email").val() !='') {
                    sendotpurllogin = url.build('smsprofile/otp/send');
                    mobile = $(document).find(".checkout-index-index #login-email").val();
                    var isValidate = true;
                    $(document).find('.checkout-index-index .actions-toolbar .action-login').attr("disabled", true);
                }
                var isResend = 0;
                if (isValidate) {
                    $.ajax({

                        showLoader: true,
                        url: sendotpurllogin,
                        method: "POST",
                        data: {
                            mobile: mobile,
                            eventType: 'customer_login_otp',
                            resend: isResend,
                        },
                        dataType: "json"
                    }).done(function(response) {
                        if (response.Success === 'success') {
                            alert('OTP is generated successfully.');
                            if($(".resendlink").length == 0 && $(".field-name-otp").length == 0) {
                                $(".send_otp_login").after('<div class="resendlink"><a href="javascript:void(0);" class="resendotp" >Resend OTP</a></div><div class="field field-name-otp required"><label class="label" for="otp"><span>Please enter verification code here</span></label>  <div class="control"> <input id="otp" name="otp" value="" title="otp" class="input-text required-entry" data-validate="{required:true}" autocomplete="off" aria-required="true" type="text" novalidate="novalidate" style="width: 200px;"> <button style="display: block;margin-top: 10px;" class="verif_otp_login action primary" type="button">Verify OTP</button></div></div><span class="otp_text"></span> ');
                            }
                        } else {
                            if($(".smserror").length == 0 ) {
                                $(".send_otp_login").after('<p class="smserror">'+response.Success+'</p>');
                            }
                            if($(document).find(".smserror").length > 0 ) {
                                $(document).find(".smserror").html(response.Success);
                            }
                        }

                    });
                }
            });

            $(document).on("click", ".verif_otp_login", function() {
                var sendotpurlLogin = $('.verifyUrlLogin').val();
                if ($('.fieldset #otp').val() != '' || $('.block-customer-login #otp').val() != '' ) {
                    $('.otp_text').html('');
                    var mobile = $("#email").val();
                    if($('#email_address').length > 0 && $("#email_address").val() !='') {
                        var mobile = $("#email_address").val();
                    }
                    var otp = $("#otp").val();
                    if ($('.checkout-index-index #customer-email').length > 0) {
                        sendotpurlLogin = url.build('smsprofile/otp/verify');
                        mobile = $(document).find(".checkout-index-index #customer-email").val();
                        otp = $(document).find('.fieldset #otp').val();
                    }
                    if ($(document).find('.checkout-index-index #login-email').length > 0 && $(document).find(".checkout-index-index #login-email").val() != '') {
                        sendotpurlLogin = url.build('smsprofile/otp/verify');
                        mobile = $(document).find(".checkout-index-index #login-email").val();
                        otp = $(document).find('.block-customer-login #otp').val();

                    }
                    $.ajax({
                        showLoader: true,
                        url: sendotpurlLogin,
                        method: "POST",
                        data: {
                            otp: otp,
                            mobile: mobile
                        },
                        dataType: "json"
                    }).done(function(response) {
                        if (response) {
                            $('.otp_text').html(response.message);
                            if (response.message == 'Verified') {
                                $('.otp_text').css('color','green');
                                $('.verifiedotp').val(1);
                                $('.send_otp_login').hide();
                                $('.resendlink').hide();
                                $('.field-name-otp').hide();
                                $('.actions-toolbar #send2').attr("disabled", false);
                                if ($('.checkout-index-index #customer-email').length > 0) {
                                    $('.checkout-index-index .actions-toolbar .login').attr("disabled", false);
                                }
                                if ($(document).find('.checkout-index-index #login-email').length > 0) {
                                    $(document).find('.checkout-index-index .actions-toolbar .action-login').attr("disabled", false);
                                }
                            } else {
                                $('.otp_text').css('color','red');
                                $('.verifiedotp').val(0);
                            }
                        }
                    });
                } else {
                    alert('Please enter OTP');
                }
            });
            /* code for otp at login page ends here*/

            /* code for otp at customer account edit page  starts here*/
            if ($('#customer_mobile_attr').length) {
                $(document).find('.send_otp_edit').hide();
            }

            $('#customer_mobile_attr').on('input', function() {
                $(document).find('.resendlink').hide();
                if (!$.isNumeric($(this).val())) {
                    $(document).find('.send_otp_edit').hide();
                    $('.actions-toolbar .primary .save').attr("disabled", false);
                } else {
                    $(document).find('.send_otp_edit').show();
                    $('.actions-toolbar .primary .save').attr("disabled", true);
                }
            });

            $(".send_otp_edit").on("click", function() {
                var sendotpurledit = $('.sendUrl_edit').val();
                var isResend = 0;
                if($(document).find(".smserror").length > 0 ) {
                    $(document).find(".smserror").html('');
                }
                if ($('#customer_mobile_attr').validation('isValid')) {
                    $.ajax({

                        showLoader: true,
                        url: sendotpurledit,
                        method: "POST",
                        data: {
                            mobile: $("#customer_mobile_attr").val(),
                            eventType: 'customer_account_edit_otp',
                            resend: isResend,
                        },
                        dataType: "json"
                    }).done(function(response) {
                        if (response.Success === 'success') {
                            alert('OTP is generated successfully.');
                            if($(".resendlink").length == 0 && $(".field-name-otp").length == 0) {
                                $(".send_otp_edit").after('<div class="resendlink" ><a href="javascript:void(0);" class="resendotp" >Resend OTP</a></div><div class="field field-name-otp required"><label class="label" for="otp"><span>Please enter verification code here</span></label>  <div class="control"> <input id="otp" name="otp" value="" title="otp" class="input-text required-entry" data-validate="{required:true}" autocomplete="off" aria-required="true" type="text" novalidate="novalidate" style="width: 200px;"> <button style="display: block;margin-top: 10px;" class="verif_otp_edit action primary" type="button">Verify OTP</button> </div></div><span class="otp_text"></span> ');
                            }
                        } else {
                            if($(".smserror").length == 0 ) {
                                $(".send_otp_edit").after('<p class="smserror">'+response.Success+'</p>');
                            }
                            if($(document).find(".smserror").length > 0 ) {
                                $(document).find(".smserror").html(response.Success);
                            }
                        }

                    });
                }
            });

            $(document).on("click", ".verif_otp_edit", function() {
                var sendotpurledit = $('.verifyUrl_edit').val();
                if ($('#otp').val() != '') {
                    $('.otp_text').html('');
                    $.ajax({
                        showLoader: true,
                        url: sendotpurledit,
                        method: "POST",
                        data: {
                            otp: $("#otp").val(),
                            mobile: $("#customer_mobile_attr").val()
                        },
                        dataType: "json"
                    }).done(function(response) {
                        if (response) {
                            $('.otp_text').html(response.message);
                            if (response.message == 'Verified') {
                                $('.otp_text').css('color','green');
                                $('.send_otp_edit').hide();
                                $('.resendlink').hide();
                                $('.field-name-otp').hide();

                                $('.actions-toolbar .primary .save').attr("disabled", false);
                            } else {
                                $('.otp_text').css('color','red');
                            }
                        }
                    });

                } else {
                    alert('Please enter OTP');
                }
            });
            /* code for otp at customer account edit page ends here*/

            /* code for otp at customer account edit page  EE starts here*/
            $('.customer-account-edit #customer_mobile').after('<div class="profile-notice-phone"> <span>'+$(".note").val()+'</span> </div> <button class="send_otp_edit_ee action primary" style="margin-top: 15px; display:none" type="button">Generate OTP</button');
            $('.customer-account-edit #customer_mobile').on('input', function() {
                $(document).find('.resendlink').hide();
                if (!$.isNumeric($(this).val())) {
                    $(document).find('.send_otp_edit_ee').hide();
                    $('.actions-toolbar .primary .save').attr("disabled", false);
                } else {
                    $(document).find('.send_otp_edit_ee').show();
                    $('.actions-toolbar .primary .save').attr("disabled", true);
                }
            });

            $(".send_otp_edit_ee").on("click", function() {
                var sendotpurledit = $('.sendUrl_edit').val();
                var isResend = 0;
                if($(document).find(".smserror").length > 0 ) {
                    $(document).find(".smserror").html('');
                }
                $.ajax({

                    showLoader: true,
                    url: sendotpurledit,
                    method: "POST",
                    data: {
                        mobile: $("#customer_mobile").val(),
                        eventType: 'customer_account_edit_otp',
                        resend: isResend,
                    },
                    dataType: "json"
                }).done(function(response) {
                    if (response.Success === 'success') {
                        alert('OTP is generated successfully.');
                        if($(".resendlink").length == 0 && $(".field-name-otp").length == 0) {
                            $(".send_otp_edit_ee").after('<div class="resendlink" ><a href="javascript:void(0);" class="resendotp" >Resend OTP</a></div><div class="field field-name-otp required"><label class="label" for="otp"><span>Please enter verification code here</span></label>  <div class="control"> <input id="otp" name="otp" value="" title="otp" class="input-text required-entry" data-validate="{required:true}" autocomplete="off" aria-required="true" type="text" novalidate="novalidate" style="width: 200px;"> <button style="display: block;margin-top: 10px;" class="verif_otp_edit_ee action primary" type="button">Verify OTP</button> </div></div><span class="otp_text"></span> ');
                        }
                    } else {
                        if($(".smserror").length == 0 ) {
                            $(".send_otp_edit_ee").after('<p class="smserror">'+response.Success+'</p>');
                        }
                        if($(document).find(".smserror").length > 0 ) {
                            $(document).find(".smserror").html(response.Success);
                        }
                    }

                });

            });

            $(document).on("click", ".verif_otp_edit_ee", function() {
                var sendotpurledit = $('.verifyUrl_edit').val();
                if ($('#otp').val() != '') {
                    $('.otp_text').html('');
                    $.ajax({
                        showLoader: true,
                        url: sendotpurledit,
                        method: "POST",
                        data: {
                            otp: $("#otp").val(),
                            mobile: $(".customer-account-edit #customer_mobile").val()
                        },
                        dataType: "json"
                    }).done(function(response) {
                        if (response) {
                            $('.otp_text').html(response.message);
                            if (response.message == 'Verified') {
                                $('.otp_text').css('color','green');
                                $('.send_otp_edit_ee').hide();
                                $('.resendlink').hide();
                                $('.field-name-otp').hide();

                                $('.actions-toolbar .primary .save').attr("disabled", false);
                            } else {
                                $('.otp_text').css('color','red');
                            }
                        }
                    });

                } else {
                    alert('Please enter OTP');
                }
            });


            /* code for otp at customer account edit page EE ends here*/

            /* code for otp at customer account forgotpassword page  starts here*/
            if ($('#email_address').length) {
                $(document).find('.send_otp_password').hide();
            }

            $('#email_address').blur(function() {
                if ($(this).val() != '') {
                    $(document).find('.resendlink').hide();
                    if (!$.isNumeric($(this).val())) {
                        $(document).find('.send_otp_password').hide();
                        $('.customer-account-forgotpassword .actions-toolbar .primary .submit').attr("disabled", false);
                    } else {
                        $(document).find('.send_otp_password').show();
                        $(document).find('.send_otp_login').show();
                        $('.customer-account-forgotpassword .actions-toolbar .primary .submit').attr("disabled", true);
                    }
                }
            });

            $(".send_otp_password").on("click", function() {
                var sendotpurledit = $('.sendUrlforget').val();
                var isResend = 0;
                if($(document).find(".smserror").length > 0 ) {
                    $(document).find(".smserror").html('');
                }
                if($('#email_address').validation('isValid') ) {
                    $.ajax({

                        showLoader: true,
                        url: sendotpurledit,
                        method: "POST",
                        data: {
                            mobile: $("#email_address").val(),
                            eventType: 'forgot_password_otp',
                            resend: isResend,
                        },
                        dataType: "json"
                    }).done(function(response) {
                        if (response.Success === 'success') {
                            alert('OTP is generated successfully.');
                            if($(".resendlink").length == 0 && $(".field-name-otp").length == 0) {
                                $(".send_otp_password").after('<div class="resendlink" ><a href="javascript:void(0);" class="resendotp" >Resend OTP</a></div><div class="field field-name-otp required"><label class="label" for="otp"><span>Please enter verification code here</span></label>  <div class="control"> <input id="otp" name="otp" value="" title="otp" class="input-text required-entry" data-validate="{required:true}" autocomplete="off" aria-required="true" type="text" novalidate="novalidate" style="width: 200px;"> <button style="display: block;margin-top: 10px;" class="verif_otp_password action primary"  type="button">Verify OTP</button></div></div><span class="otp_text"></span>  ');
                            }
                        } else {
                            if($(".smserror").length == 0 ) {
                                $(".send_otp_password").after('<p class="smserror">'+response.Success+'</p>');
                            }
                            if($(document).find(".smserror").length > 0 ) {
                                $(document).find(".smserror").html(response.Success);
                            }
                        }

                    });
                }
            });

            $(document).on("click", ".verif_otp_password", function() {
                var sendotpurledit = $('.verifyUrlforget').val();
                if ($('#otp').val() != '') {
                    $('.otp_text').html('');
                    $.ajax({
                        showLoader: true,
                        url: sendotpurledit,
                        method: "POST",
                        data: {
                            otp: $("#otp").val(),
                            mobile: $("#email_address").val()
                        },
                        dataType: "json"
                    }).done(function(response) {
                        if (response) {
                            $('.otp_text').html(response.message);
                            if (response.message == 'Verified') {
                                $('.otp_text').css('color','green');
                                $('.send_otp_password').hide();
                                $('.resendlink').hide();
                                $('.field-name-otp').hide();
                                $('.forgetOtpValidation').val(1);
                                $('.customer-account-forgotpassword .actions-toolbar .primary .submit').attr("disabled", false);
                            } else {
                                $('.otp_text').css('color','red');
                            }
                        }
                    });
                } else {
                    alert('Please enter OTP');
                }
            });
            /* code for otp at customer account forgotpassword page ends here*/

            /* code for resend otp start here*/

            $(document).on("click",".resendotp", function(){
                var buttonDisable = $(document).find('.actions-toolbar button.action.submit.primary').is(":disabled");
                if ($('.customer-account-login #email').length > 0) {
                    buttonDisable = $(document).find('.actions-toolbar #send2').is(":disabled");
                } else if ($('.checkout-index-index #customer-email').length > 0) {
                    buttonDisable = $(document).find('.checkout-index-index .actions-toolbar .login').is(":disabled");
                } else if ($('#customer_mobile_attr').length > 0) {
                    buttonDisable = $(document).find('.actions-toolbar .primary .save').is(":disabled");
                } else if ($(document).find('.checkout-index-index #login-email').length > 0) {
                    buttonDisable = $(document).find('.checkout-index-index .actions-toolbar .action-login').is(":disabled");
                }
                var sendotpurl = $('.sendUrl').val();
                var event = 'customer_signup_otp';
                var mobile = $("#customer_mobile").val();
                var isResend =1;
                if ($('.customer-account-login #email').length > 0) {
                    sendotpurl = $('.sendUrlLogin').val();
                    mobile = $("#email").val();
                    event = 'customer_login_otp';
                } else if ($('.checkout-index-index #customer-email').length > 0) {
                    sendotpurl = url.build('smsprofile/otp/send');
                    mobile =  $("#customer-email").val();
                    event = 'customer_login_otp';
                } else if ($(document).find('.checkout-index-index #login-email').length > 0) {
                    sendotpurllogin = url.build('smsprofile/otp/send');
                    mobile = $("#login-email").val();
                    event = 'customer_login_otp';
                } else if ($('#customer_mobile_attr').length > 0) {
                    sendotpurl = $('.sendUrl_edit').val();
                    mobile = $("#customer_mobile_attr").val();
                    event = 'customer_account_edit_otp';
                } else if ($('.customer-account-edit #customer_mobile').length > 0) {
                    sendotpurl = $('.sendUrl_edit').val();
                    mobile = $("customer-account-edit #customer_mobile").val();
                    event = 'customer_account_edit_otp';
                } else if ($('#email_address').length > 0) {
                    sendotpurl = $('.sendUrlforget').val();
                    mobile = $("#email_address").val();
                    event = 'forgot_password_otp';
                }
                console.log(buttonDisable);
                if (buttonDisable) {
                    $.ajax({
                        showLoader: true,
                        url: sendotpurl,
                        method: "POST",
                        data: {
                            mobile : mobile,
                            eventType : event,
                            resend : isResend,
                        },
                        dataType: "json"
                    }).done(function( response ){
                        if(response.Success === 'success' ){
                            alert('OTP resent successfully');
                        }
                        else {
                            alert('Not able to send SMS');
                        }
                    });
                }
            });
        });
    }
);