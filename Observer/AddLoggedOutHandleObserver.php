<?php

namespace Magezil\AjaxLogin\Observer;

use Magento\Customer\Model\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class AddLoggedOutHandleObserver
 *
 * @category Magento
 * @package  Magezil_AjaxLogin
 * @author   Lucas Teixeira dos Santos Santana <santanaluc94@gmail.com>
 * @license  OSL-3.0
 * @license  AFL-3.0
 * @link     http://github.com/santanaluc94
 */
class AddLoggedOutHandleObserver implements ObserverInterface
{
    private $customerSession;

    public function __construct(
        Session $customerSession
    ) {
        $this->customerSession = $customerSession;
    }

    public function execute(Observer $observer): void
    {
        $layout = $observer->getEvent()->getLayout();

        if (!$this->customerSession->isLoggedIn()) {
            $layout->getUpdate()->addHandle('ajaxlogin_customer_logged_out');
        }
    }
}
