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
        // return false;
    });
})
