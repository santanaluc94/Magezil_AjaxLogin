<?php

namespace CustomModules\AjaxLogin\Observer;

use Magento\Customer\Model\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

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
