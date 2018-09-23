<?php
namespace Omnipay\SimplePay\Message;

use Omnipay\Common\Message\ResponseInterface;

/**
 * SimplePay IPN Request
 * this completes the payment process
 */
class IPNRequest extends AbstractRequest {
    public function getData() {
        $this->validate('postArgs');
        return $this->getPostArgs();
    }

    public function setPostArgs($data) {
        $this->setParameter('postArgs', $data);
    }

    public function getPostArgs() {
        return $this->getParameter('postArgs');
    }

    public function sendData($data) {
        return $this->response = new IPNResponse($this, $data);
    }
}
