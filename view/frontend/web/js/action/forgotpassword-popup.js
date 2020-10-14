define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'mage/translate',
    'mage/mage',
    'jquery/ui',
    'uiComponent'
], function ($, modal, $t) {
    'use strict';
    console.log('Forgot password actived');

    var optionsForgot = {
        type: 'popup',
        responsive: true,
        innerScroll: true,
        buttons: false,
    };

    var popupForgotPassword = modal(optionsForgot, $('#customer_forgotpassword_popup'));

    // Show the forgot password form in a popup when clicking on the sign in text
    $('body').on('click', '#forgotpassword_popup', function () {
        // $('#customer_register_popup').modal('closeModal');
        $('#customer_login_popup').modal('closeModal');
        $('#customer_forgotpassword_popup').modal('openModal');
    });

    $(document).ready(function () {
        $('#forgotpassword_form').submit(function (event) {
            event.preventDefault();

            let actionUrlForm = $('#forgotpassword_popup_form').attr('action');

            $.ajax({
                url: actionUrlForm,
                type: 'POST',
                dataType: 'json',
                data: $(event.target).serializeArray(),
                showLoader: true,
                success: function (response) {
                    $('.messages').html('');
                    if (response.errors) {
                        $('<div class="message message-error error message-popup"><div><span>' + response.message + '</span></div></div>').appendTo($('.messages'));
                    } else {
                        $('<div class="message message-success success message-popup"><div><span>' + response.message + '</span></div></div>').appendTo($('.messages'));
                    }

                    $('.messages').show();
                    setTimeout(function () {
                        if (!response.errors) {
                            $('#customer-login-popup').modal('closeModal');
                            location.reload();
                        }
                    }, 500);
                },
                error: function (response) {
                    $('body').loader('hide');
                    $('.messages').html('');
                    $('<div class="message message-error error message-popup"><div><span>' + response.message + '</span></div></div>').appendTo($('.messages'));
                    $('.messages').show();
                }
            });
        });
    });
});
