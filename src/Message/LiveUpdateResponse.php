<?php
namespace Omnipay\SimplePay\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\Common\Message\RequestInterface;

/**
 * SimplePay Response
 *
 * This is the response class for all SimplePay requests.
 *
 * @see \Omnipay\SimplePay\Gateway
 */
class LiveUpdateResponse extends AbstractResponse {

    public function __construct(RequestInterface $request, $data) {
        parent::__construct($request, $data);
    }

    public function isSuccessful() {
        return false;
    }

    public function isRedirect() {
        return true;
    }

    public function getRedirectUrl() {
        return $this->getRequest()->getEndpoint();
    }

    public function getRedirectMethod() {
        return 'POST';
    }

    public function getRedirectData() {
        return $this->data;
    }
}
