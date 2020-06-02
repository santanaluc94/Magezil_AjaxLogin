define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'Magento_Customer/js/customer-data',
    'mage/storage',
    'mage/translate',
    'mage/mage',
    'jquery/ui'
], function ($, modal, customerData, storage, $t) {
    'use strict';
    console.log('Ajax Login actived');

    var options = {
        type: 'popup',
        responsive: true,
        innerScroll: true,
        buttons: false,
    };

    var popup = modal(options, $('#customer-login-popup'));

    // Show the login form in a popup when clicking on the sign in text
    $('body').on('click', '.custom-ajax-login-popup, ' + '#customer-sign-in-popup', function () {
        $('#customer-register-popup').modal('closeModal');
        $('#customer-forgotpassword-popup').modal('closeModal');
        $('#customer-login-popup').modal('openModal');
    });

    $(document).ready(function () {
        $('#ajaxlogin-form').submit(function (e) {
            let customurl = "<?= $this->getUrl().'ajax_login/ajax/login'?>";
            $.ajax({
                url: customurl,
                type: 'POST',
                dataType: 'json',
                data: $(e.target).serializeArray(),
                showLoader: true,
                success: function (response) {
                    var self = this;
                    this.element.find('.messages').html('');
                    if (response.errors) {
                        $('<div class="message message-error error"><div>' + response.message + '</div></div>').appendTo(this.element.find('.messages'));
                    } else {
                        $('<div class="message message-success success"><div>' + response.message + '</div></div>').appendTo(this.element.find('.messages'));
                    }
                    this.element.find('.messages .message').show();
                    setTimeout(function () {
                        if (!response.errors) {
                            self.element.modal('closeModal');
                            window.location.href = locationHref;
                        }
                    }, 800);
                },
                error: function () {
                    this.element.find('.messages').html('');
                    this._displayMessages('message-error error', $t('An error occurred, please try again later.'));
                    this.element.find('.messages .message').show();
                }
            });
        });
    });
})
