define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'Magento_Customer/js/customer-data',
    'mage/storage',
    'mage/translate',
    'mage/mage',
    'jquery/ui',
    'uiComponent'
], function ($, modal, customerData, storage, $t) {
    'use strict';

    var options = {
        type: 'popup',
        responsive: true,
        innerScroll: true,
        buttons: false,
    };

    var popupLogin = modal(options, $('#customer-login-popup'));

    /**
     * Show the login form in a popup when clicking on the sign in text
     */
    $('body').on('click', '.ajax-login-popup, ' + '#customer-create-account-popup', function () {
        $('#customer-create-account-popup').modal('closeModal');
        $('#customer-forgotpassword-popup').modal('closeModal');
        $('#customer-login-popup').modal('openModal');
    });

    /**
     * Ajax do login
     */
    $(document).ready(function () {
        $('#ajaxlogin-form').submit(function (e) {
            e.preventDefault();
            let actionUrlForm = $('#ajaxlogin-form').attr('action');
            $.ajax({
                url: actionUrlForm,
                type: 'POST',
                dataType: 'json',
                data: $(e.target).serializeArray(),
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
})
