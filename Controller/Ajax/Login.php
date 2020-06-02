<?php

namespace CustomModules\AjaxLogin\Controller\Ajax;

use Magento\Customer\Controller\AbstractAccount;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Customer\Model\Account\Redirect as AccountRedirect;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Stdlib\Cookie\PhpCookieManager;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\EmailNotConfirmedException;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Framework\App\ObjectManager;

/**
 * Class Login
 *
 * @category Magento
 * @package  CustomModules_AjaxLogin
 * @author   Lucas Teixeira dos Santos Santana <santanaluc94@gmail.com>
 * @license  NO-LICENSE #
 * @link     http://github.com/santanaluc94
 */
class Login extends AbstractAccount implements
    CsrfAwareActionInterface,
    HttpPostActionInterface
{
    /**
     * Customer Session
     *
     * @var Session
     */
    protected $customerSession;

    /**
     * Account Management Interface
     *
     * @var AccountManagementInterface
     */
    protected $customerAccountManagement;

    /**
     * Customer Url
     *
     * @var CustomerUrl
     */
    protected $customerHelperData;

    /**
     * Account Redirect
     *
     * @var AccountRedirect
     */
    protected $accountRedirect;

    /**
     * Raw Factory
     *
     * @var RawFactory
     */
    protected $rawFactory;

    /**
     * Scope Config Interface
     *
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Cookie Metadata Factory
     *
     * @var CookieMetadataFactory
     */
    protected $cookieMetadataFactory;

    /**
     * PHP Cokkie Manager
     *
     * @var PhpCookieManager
     */
    protected $cookieMetadataManager;

    /**
     * Login constructor.
     *
     * @param Context $context
     * @param Session $customerSession
     * @param AccountManagementInterface $customerAccountManagement
     * @param CustomerUrl $customerHelperData
     * @param AccountRedirect $accountRedirect
     * @param RawFactory $rawFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param PhpCookieManager $cookieManager
     * @param CookieMetadataFactory $cookieMetadataFactory
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        AccountManagementInterface $customerAccountManagement,
        CustomerUrl $customerHelperData,
        AccountRedirect $accountRedirect,
        RawFactory $rawFactory,
        ScopeConfigInterface $scopeConfig,
        PhpCookieManager $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory
    ) {
        $this->session = $customerSession;
        $this->customerAccountManagement = $customerAccountManagement;
        $this->customerUrl = $customerHelperData;
        $this->accountRedirect = $accountRedirect;
        $this->resultRawFactory = $rawFactory;
        $this->scopeConfig = $scopeConfig;
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        parent::__construct($context);
    }

    /**
     * Get scope config
     *
     * @return ScopeConfigInterface
     */
    protected function getScopeConfig()
    {
        if (!($this->scopeConfig instanceof ScopeConfigInterface)) {
            return ObjectManager::getInstance()
                ->get(ScopeConfigInterface::class);
        } else {
            return $this->scopeConfig;
        }
    }

    /**
     * Retrieve cookie manager
     *
     * @return PhpCookieManager
     */
    protected function getCookieManager()
    {
        if (!$this->cookieMetadataManager) {
            $this->cookieMetadataManager = ObjectManager::getInstance()
                ->get(PhpCookieManager::class);
        }
        return $this->cookieMetadataManager;
    }

    /**
     * Retrieve cookie metadata factory
     *
     * @return CookieMetadataFactory
     */
    protected function getCookieMetadataFactory()
    {
        if (!$this->cookieMetadataFactory) {
            $this->cookieMetadataFactory = ObjectManager::getInstance()
                ->get(CookieMetadataFactory::class);
        }
        return $this->cookieMetadataFactory;
    }

    /**
     * Create csrf Calidation exception.
     *
     * @param RequestInterface $request
     * @return null|InvalidRequestException
     */
    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('*/*/');

        return new InvalidRequestException(
            $resultRedirect,
            [new Phrase('Invalid Form Key. Please refresh the page.')]
        );
    }

    /**
     * Validate for csrf.
     *
     * @param RequestInterface $request
     * @return null|boolean
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return null;
    }

    /**
     * Login post action
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
        var_dump($this->getRequest()->isXmlHttpRequest());
        die('asd');

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

                    if ($this->getCookieManager()->getCookie('mage-cache-sessid')) {
                        $metadata = $this->getCookieMetadataFactory()
                            ->createCookieMetadata();
                        $metadata->setPath('/');
                        $this->getCookieManager()
                            ->deleteCookie('mage-cache-sessid', $metadata);
                    }

                    $redirectUrl = $this->accountRedirect->getRedirectCookie();

                    if (!$this->getScopeConfig()->getValue('customer/startup/redirect_dashboard') && $redirectUrl) {
                        $this->accountRedirect->clearRedirectCookie();

                        $resultRedirect = $this->resultRedirectFactory->create();
                        $resultRedirect->setUrl(
                            $this->_redirect->success($redirectUrl)
                        );
                        $this->messageManager->addSuccess(__('Login successful'));
                        return $resultRedirect;
                    }
                } catch (EmailNotConfirmedException $e) {
                    $value = $this->customerUrl->getEmailConfirmationUrl($email);
                    $message = __(
                        'This account is not confirmed.
                        <a href="%1">Click here</a> to resend confirmation email.',
                        $value
                    );
                } catch (AuthenticationException $e) {
                    $message = __(
                        'The account sign-in was incorrect or your account is disabled temporarily.
                        Please wait and try again later.'
                    );
                } catch (LocalizedException $e) {
                    $message = $e->getMessage();
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

        return $this->accountRedirect->getRedirect();
    }
}
