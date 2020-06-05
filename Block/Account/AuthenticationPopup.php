<?php

namespace CustomModules\AjaxLogin\Block\Account;

use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Customer\Model\Form;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\ObjectManager;

/**
 * Authentication Popup Class
 *
 * @category Magento
 * @package  CustomModules_AjaxLogin
 * @author   Lucas Teixeira dos Santos Santana <santanaluc94@gmail.com>
 * @license  NO-LICENSE #
 * @link     http://github.com/santanaluc94
 */
class AuthenticationPopup extends \Magento\Customer\Block\Account\AuthenticationPopup
{
    /**
     * @var Json
     */
    protected $serializer;

    public function __construct(
        Context $context,
        array $data = [],
        Json $serializer = null
    ) {
        parent::__construct(
            $context,
            $data
        );
        $this->serializer = $serializer ?: ObjectManager::getInstance()
            ->get(Json::class);
    }

    /**
     * Is autocomplete enabled for storefront
     *
     * @return string
     */
    protected function isAutocompleteEnabled(): string
    {
        return $this->_scopeConfig->getValue(
            Form::XML_PATH_ENABLE_AUTOCOMPLETE,
            ScopeInterface::SCOPE_STORE
        ) ? 'on' : 'off';
    }

    /**
     * Get captcha
     */
    public function getFormAction()
    {
        return $this->getUrl('ajaxlogin/ajax/login', ['_secure' => true]);
    }
}
