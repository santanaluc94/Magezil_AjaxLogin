<?php

namespace Magezil\AjaxLogin\Controller\Ajax;

use Magento\Customer\Controller\AbstractAccount;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Exception\EmailNotConfirmedException;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\Result\JsonFactory;

/**
 * Class Login
 *
 * @category Magento
 * @package  Magezil_AjaxLogin
 * @author   Lucas Teixeira dos Santos Santana <santanaluc94@gmail.com>
 * @license  OSL-3.0
 * @license  AFL-3.0
 * @link     http://github.com/santanaluc94
 */
class Login extends AbstractAccount implements HttpPostActionInterface
{
    protected $customerSession;
    protected $customerAccountManagement;
    protected $customerHelperData;
    protected $rawFactory;
    protected $resultJsonFactory;

    public function __construct(
        Context $context,
        Session $customerSession,
        AccountManagementInterface $customerAccountManagement,
        CustomerUrl $customerHelperData,
        RawFactory $rawFactory,
        JsonFactory $resultJsonFactory
    ) {
        $this->session = $customerSession;
        $this->customerAccountManagement = $customerAccountManagement;
        $this->customerUrl = $customerHelperData;
        $this->resultRawFactory = $rawFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $httpBadRequestCode = 400;
        $resultRaw = $this->resultRawFactory->create();

        try {
            $credentials = $this->getRequest()->getParams();
        } catch (\Exception $e) {
            return $resultRaw->setHttpResponseCode($httpBadRequestCode);
        }

        if (!$credentials || $this->getRequest()->getMethod() !== 'POST' || !$this->getRequest()->isXmlHttpRequest()) {
            return $resultRaw->setHttpResponseCode($httpBadRequestCode);
        }

        if ($this->getRequest()->isPost()) {
            if (!empty($credentials['username']) && !empty($credentials['password'])) {
                try {
                    $email = $credentials['username'];

                    $customer = $this->customerAccountManagement->authenticate(
                        $email,
                        $credentials['password']
                    );

                    $this->session->setCustomerDataAsLoggedIn($customer);
                    $response = [
                        'errors' => false,
                        'message' => __('Login successful.')
                    ];
                } catch (EmailNotConfirmedException $e) {
                    $value = $this->customerUrl->getEmailConfirmationUrl($email);
                    $message = __(
                        'This account is not confirmed.
                        <a href="%1">Click here</a> to resend confirmation email.',
                        $value
                    );
                    $response = [
                        'errors' => true,
                        'message' => $message,
                    ];
                } catch (AuthenticationException $e) {
                    $response = [
                        'errors' => true,
                        'message' => __(
                            'The account sign-in was incorrect or your account is disabled temporarily.
                            Please wait and try again later.'
                        )
                    ];
                } catch (LocalizedException $e) {
                    $response = [
                        'errors' => true,
                        'message' => $e->getMessage(),
                    ];
                } finally {
                    if (isset($message)) {
                        $this->messageManager->addErrorMessage($message);
                        $this->session->setUsername($email);
                    }
                }
            } else {
                $this->messageManager->addErrorMessage(
                    __('A login and a password are required.')
                );
            }
        }

        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($response);
    }
}
