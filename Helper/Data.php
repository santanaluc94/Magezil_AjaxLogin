<?php

namespace Magezil\AjaxLogin\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Data
 *
 * @category Magento
 * @package  Magezil_AjaxLogin
 * @author   Lucas Teixeira dos Santos Santana <santanaluc94@gmail.com>
 * @license  NO-LICENSE #
 * @link     http://github.com/santanaluc94
 */
class Data extends AbstractHelper
{
    const MODULE_ENABLE = 'magezil_ajax_login/general/enable';

    /**
     * Scope Config
     *
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Store Manager
     *
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Helper Data Constructor
     *
     * @param ScopeConfigInterface  $scopeConfig
     * @param StoreManagerInterface  $storeManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * Check module is enable
     *
     * @return boolean
     */
    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::MODULE_ENABLE,
            ScopeInterface::SCOPE_WEBSITE
        );
    }
}
