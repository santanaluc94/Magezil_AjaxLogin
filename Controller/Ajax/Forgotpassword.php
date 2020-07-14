<?php

namespace CustomModules\AjaxLogin\Controller\Ajax;

use Magento\Customer\Controller\AbstractAccount;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\Result\JsonFactory;

/**
 * Class Forgotpassword
 *
 * @category Magento
 * @package  CustomModules_AjaxLogin
 * @author   Lucas Teixeira dos Santos Santana <santanaluc94@gmail.com>
 * @license  NO-LICENSE #
 * @link     http://github.com/santanaluc94
 */
class Forgotpassword extends AbstractAccount implements HttpPostActionInterface
{
    /**
     * Customer Session
     *
     * @var Session
     */
    protected $customerSession;

    /**
     * Raw Factory
     *
     * @var RawFactory
     */
    protected $rawFactory;

    /**
     * Json Factory
     *
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * Forgotpassword constructor.
     *
     * @param Context $context
     * @param Session $customerSession
     * @param RawFactory $rawFactory
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        RawFactory $rawFactory,
        JsonFactory $resultJsonFactory
    ) {
        $this->session = $customerSession;
        $this->resultRawFactory = $rawFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    /**
     * Forgotpassword post action
     *
     * @return Redirect
     */
    public function execute()
    {
        $httpBadRequestCode = 400;
        $resultRaw = $this->resultRawFactory->create();

        try {
            $credentials = $this->getRequest()->getParams();
        } catch (\Exception $e) {
            return $resultRaw->setHttpResponseCode($httpBadRequestCode);
        }


        $response = [
            'errors' => true,
            'message' => 'Entrou',
        ];
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($response);

        if (!$credentials || $this->getRequest()->getMethod() !== 'POST' || !$this->getRequest()->isXmlHttpRequest()) {
            return $resultRaw->setHttpResponseCode($httpBadRequestCode);
        }

        if ($this->getRequest()->isPost()) {
            if (!empty($credentials['username'])) {
                try {
                    $email = $credentials['username'];

                    $this->session->setCustomerDataAsLoggedIn($email);
                    $response = [
                        'errors' => false,
                        'message' => __('Login successful.')
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
                    __('An email is required.')
                );
            }
        }

        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($response);
    }
}
