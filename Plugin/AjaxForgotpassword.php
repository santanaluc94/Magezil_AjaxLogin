<?php

namespace CustomModules\AjaxLogin\Plugin;

use Magento\Captcha\Helper\Data as CaptchaHelper;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\Controller\Result\JsonFactory;

class AjaxForgotpassword
{
    const CAPTCHA_POPUP_FORGOT_PASSWORD_FORM = 'popup_forgot_password_form';

    public function __construct(
        CaptchaHelper $helper,
        SessionManagerInterface $sessionManager,
        JsonFactory $resultJsonFactory,
        \Magento\Captcha\Observer\CaptchaStringResolver $captchaStringResolver //
    ) {
        $this->helper = $helper;
        $this->sessionManager = $sessionManager;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->captchaStringResolver = $captchaStringResolver;
    }

    public function aroundExecute(
        \CustomModules\AjaxLogin\Controller\Ajax\Forgotpassword $subject,
        \Closure $proceed
    ) {
        $captchaModel = $this->helper->getCaptcha(self::CAPTCHA_POPUP_FORGOT_PASSWORD_FORM);
        $result = $this->resultJsonFactory->create();

        if (!$captchaModel->isCorrect($this->captchaStringResolver->resolve($subject->getRequest(), self::CAPTCHA_POPUP_FORGOT_PASSWORD_FORM))) {
            $this->sessionManager->setCustomerFormData($subject->getRequest()->getPostValue());
            $response = [
                'errors' => true,
                'message' => __('Incorrect CAPTCHA.')
            ];
            return $result->setData($response);
        }
        return $proceed();
    }
}
