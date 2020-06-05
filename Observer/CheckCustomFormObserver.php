<?php

namespace CustomModules\AjaxLogin\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Action\Action;
use Magento\Captcha\Helper\Data;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Captcha\Observer\CaptchaStringResolver;

class CheckCustomFormObserver implements ObserverInterface
{
    /**
     * Data
     *
     * @var Data
     */
    protected $helper;

    /**
     * Action Flag
     *
     * @var ActionFlag
     */
    protected $actionFlag;

    /**
     * Manager Interface
     *
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * Redirect Interface
     *
     * @var RedirectInterface
     */
    protected $redirect;

    /**
     * Captcha String Resolver
     *
     * @var CaptchaStringResolver
     */
    protected $captchaStringResolver;

    /**
     * Data Persistor Interface
     *
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @param Data $helper
     * @param ActionFlag $actionFlag
     * @param ManagerInterface $messageManager
     * @param RedirectInterface $redirect
     * @param CaptchaStringResolver $captchaStringResolver
     */
    public function __construct(
        Data $helper,
        ActionFlag $actionFlag,
        ManagerInterface $messageManager,
        RedirectInterface $redirect,
        CaptchaStringResolver $captchaStringResolver
    ) {
        $this->helper = $helper;
        $this->actionFlag = $actionFlag;
        $this->messageManager = $messageManager;
        $this->redirect = $redirect;
        $this->captchaStringResolver = $captchaStringResolver;
    }

    /**
     * Check CAPTCHA on Custom Form
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer): void
    {
        $formId = 'custom_form';
        $captcha = $this->helper->getCaptcha($formId);

        if ($captcha->isRequired()) {
            /** @var Action $controller */
            $controller = $observer->getControllerAction();

            if (!$captcha->isCorrect($this->captchaStringResolver->resolve($controller->getRequest(), $formId))) {
                $this->messageManager->addError(__('Incorrect CAPTCHA.'));
                $this->getDataPersistor()->set(
                    $formId, $controller->getRequest()->getPostValue()
                );
                $this->actionFlag->set('', Action::FLAG_NO_DISPATCH, true);
                $this->redirect->redirect(
                    $controller->getResponse(), 'ajaxlogin/ajax/login'
                );
            }
        }
    }

    /**
     * Get Data Persistor
     *
     * @return DataPersistorInterface
     */
    private function getDataPersistor()
    {
        if ($this->dataPersistor === null) {
            $this->dataPersistor = ObjectManager::getInstance()
                ->get(DataPersistorInterface::class);
        }

        return $this->dataPersistor;
    }
}