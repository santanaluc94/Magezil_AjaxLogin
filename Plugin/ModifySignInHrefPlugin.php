<?php

namespace Magezil\AjaxLogin\Plugin;

use Magento\Customer\Model\Context;
use Magento\Framework\App\Http\Context as HttpContext;
use Magezil\AjaxLogin\Helper\Data;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Block\Account\AuthorizationLink;

/**
 * Class ModifySignInHrefPlugin
 *
 * @category Magento
 * @package  Magezil_AjaxLogin
 * @author   Lucas Teixeira dos Santos Santana <santanaluc94@gmail.com>
 * @license  OSL-3.0
 * @license  AFL-3.0
 * @link     http://github.com/santanaluc94
 */
class ModifySignInHrefPlugin
{
    protected $httpContext;
    protected $helper;
    protected $storeManager;

    public function __construct(
        HttpContext $httpContext,
        Data $helper,
        StoreManagerInterface $storeManager
    ) {
        $this->httpContext = $httpContext;
        $this->helper = $helper;
        $this->storeManager = $storeManager;
    }

    public function afterGetHref(AuthorizationLink $subject, $result): string
    {
        if (!$this->helper->isEnabled()) {
            return $this->storeManager->getStore()->getUrl('customer/account/login');
        }

        if (!$this->isLoggedIn()) {
            $result = '#';
        }
        return $result;
    }

    public function isLoggedIn(): bool
    {
        return $this->httpContext->getValue(Context::CONTEXT_AUTH);
    }
}
