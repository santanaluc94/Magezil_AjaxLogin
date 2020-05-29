<?php

namespace CustomModules\AjaxLogin\Plugin;

use Magento\Customer\Model\Context;
use Magento\Framework\App\Http\Context as HttpContext;
use CustomModules\AjaxLogin\Helper\Data;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Block\Account\AuthorizationLink;

class ModifySignInHrefPlugin
{
    /**
     * Customer session
     *
     * @var HttpContext
     */
    protected $httpContext;

    /**
     * Custom Helper Data
     *
     * @var Data
     */
    protected $helper;

    /**
     * Store Manager Interface
     *
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Modify Sign In Href Plugin constructor.
     *
     * @param HttpContext $httpContext
     * @param Data $helper
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        HttpContext $httpContext,
        Data $helper,
        StoreManagerInterface $storeManager
    ) {
        $this->httpContext = $httpContext;
        $this->helper = $helper;
        $this->storeManager = $storeManager;
    }

    /**
     * Set link url to header button 'Sign in'
     *
     * @param AuthorizationLink $subject
     * @param $result
     * @return string
     */
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

    /**
     * Check customer is logged in
     *
     * @return bool
     */
    public function isLoggedIn(): bool
    {
        return $this->httpContext->getValue(Context::CONTEXT_AUTH);
    }
}
