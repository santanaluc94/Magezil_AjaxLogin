<?php

namespace Magezil\AjaxLogin\Block\Account;

use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Serialize\Serializer\Json;
use Magezil\AjaxLogin\Helper\Data;

/**
 * Class AuthenticationPopup
 *
 * @category Magento
 * @package  Magezil_AjaxLogin
 * @author   Lucas Teixeira dos Santos Santana <santanaluc94@gmail.com>
 * @license  OSL-3.0
 * @license  AFL-3.0
 * @link     http://github.com/santanaluc94
 */
class AuthenticationPopup extends \Magento\Customer\Block\Account\AuthenticationPopup
{
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

    public function getCustomerRegisterUrlUrl(): string
    {
        if (!$this->helper->isEnabled()) {
            return $this->getUrl('customer/account/create');
        }

        return '#';
    }

    public function getCustomerForgotPasswordUrl(): string
    {
        if (!$this->helper->isEnabled()) {
            return $this->getUrl('customer/account/forgotpassword');
        }

        return '#';
    }
}
