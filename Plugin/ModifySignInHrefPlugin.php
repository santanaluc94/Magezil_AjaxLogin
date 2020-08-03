<?php

namespace CustomModules\AjaxLogin\Plugin;

use Magento\Customer\Model\Context;
use Magento\Framework\App\Http\Context as HttpContext;
use CustomModules\AjaxLogin\Helper\Data;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Block\Account\AuthorizationLink;

class ModifySignInHrefPlugin
{
    private $httpContext;
    private $helper;
    private $storeManager;

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

    private function isLoggedIn(): bool
    {
        return $this->httpContext->getValue(Context::CONTEXT_AUTH);
    }
}
