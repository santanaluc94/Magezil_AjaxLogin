<?php

namespace CustomModules\AjaxLogin\Controller\Ajax;

use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\Result\Json;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Security\Model\Config;
use Magento\Customer\Model\AccountManagement;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Customer\Controller\AbstractAccount;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;

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
    protected $customerSession;
    protected $rawFactory;
    protected $resultJsonFactory;
    protected $customerRepository;
    protected $storeManager;
    protected $customerAccountManagement;
    protected $configPassword;

    public function __construct(
        Context $context,
        Session $customerSession,
        RawFactory $rawFactory,
        JsonFactory $resultJsonFactory,
        CustomerRepositoryInterface $customerRepository,
        StoreManagerInterface $storeManager,
        AccountManagementInterface $customerAccountManagement,
        Config $configPassword
    ) {
        $this->session = $customerSession;
        $this->resultRawFactory = $rawFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->customerRepository = $customerRepository;
        $this->storeManager = $storeManager;
        $this->customerAccountManagement = $customerAccountManagement;
        $this->configPassword = $configPassword;
        parent::__construct($context);
    }

    public function execute(): Json
    {
        $httpBadRequestCode = 400;
        $resultRaw = $this->resultRawFactory->create();
        $websiteId = $this->storeManager->getStore()->getWebsiteId();

        try {
            $credentials = $this->getRequest()->getParams();
        } catch (\Exception $e) {
            return $resultRaw->setHttpResponseCode($httpBadRequestCode);
        }

        if (!$credentials || $this->getRequest()->getMethod() !== 'POST' || !$this->getRequest()->isXmlHttpRequest()) {
            return $resultRaw->setHttpResponseCode($httpBadRequestCode);
        }

        if ($this->getRequest()->isPost()) {
            if (!empty($credentials['username'])) {
                try {
                    if (!filter_var($credentials['username'], FILTER_VALIDATE_EMAIL)) {
                        throw new LocalizedException(__('%1 is not a valid email.', $credentials['username']));
                    }

                    if ($this->customerRepository->get($credentials['username']) === null) {
                        throw new NoSuchEntityException();
                    }

                    $customerAttempts = $this->configPassword->getPasswordResetProtectionType();
                    $configAttempts = $this->configPassword->getMaxNumberPasswordResetRequests();

                    if ($customerAttempts === $configAttempts) {
                        throw new \Exception();
                    }

                    $customer = $this->customerRepository->get($credentials['username'], $websiteId);
                    $this->customerAccountManagement->initiatePasswordReset(
                        $customer->getEmail(),
                        AccountManagement::EMAIL_RESET,
                        $customer->getWebsiteId()
                    );

                    $response = [
                        'errors' => false,
                        'message' => __('The email %1 is not registered.', $credentials['username']),
                    ];
                } catch (NoSuchEntityException $exception) {
                    $response = [
                        'errors' => true,
                        'message' => __('The email %1 is not registered.', $credentials['username']),
                    ];
                } catch (\Exception $exception) {
                    $response = [
                        'errors' => true,
                        'message' => $exception->getMessage(),
                    ];
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
