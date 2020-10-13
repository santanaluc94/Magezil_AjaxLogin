<?php

namespace CustomModules\AjaxLogin\Block\Account;

use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Serialize\Serializer\Json;
use CustomModules\AjaxLogin\Helper\Data;

class AuthenticationPopup extends \Magento\Customer\Block\Account\AuthenticationPopup
{
    /**
     * Helper Data
     *
     * @var Data
     */
    protected $helper;

    public function __construct(
        Context $context,
        Data $helper,
        array $data = [],
        Json $serializer = null
    ) {
        $this->helper = $helper;
        parent::__construct(
            $context,
            $data,
            $serializer
        );
    }

    /**
     * Get customer register url
     *
     * @return string
     */
    public function getCustomerRegisterUrlUrl(): string
    {
        if (!$this->helper->isEnabled()) {
            return $this->getUrl('customer/account/create');
        }

        return '#';
    }

    /**
     * Get customer forgot password url
     *
     * @return string
     */
    public function getCustomerForgotPasswordUrl(): string
    {
        if (!$this->helper->isEnabled()) {
            return $this->getUrl('customer/account/forgotpassword');
        }

        return '#';
    }
}
