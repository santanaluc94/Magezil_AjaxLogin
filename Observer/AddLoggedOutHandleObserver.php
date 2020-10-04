<?php

namespace Magezil\AjaxLogin\Observer;

use Magento\Customer\Model\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class AddLoggedOutHandleObserver implements ObserverInterface
{
    /**
     * @var Session
     */
    private $customerSession;

    /**
     * Add customer handles observer constructor.
     *
     * @param Session $customerSession
     */
    public function __construct(
        Session $customerSession
    ) {
        $this->customerSession = $customerSession;
    }

    /**
     * Add a custom handle responsible for adding the trigger-ajax-login class
     *
     * @param Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer): void
    {
        $layout = $observer->getEvent()->getLayout();

        if (!$this->customerSession->isLoggedIn()) {
            $layout->getUpdate()->addHandle('ajaxlogin_customer_logged_out');
        }
    }
}
