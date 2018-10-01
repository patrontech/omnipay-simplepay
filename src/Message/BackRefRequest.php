<?php
namespace Omnipay\SimplePay\Message;

use Omnipay\Common\Message\ResponseInterface;
use Omnipay\Common\Exception\InvalidRequestException;

/**
 * SimplePay IPN Request
 * this completes the payment process
 */
class BackRefRequest extends AbstractRequest {

    protected function verifyHash() {
        $url = $this->getUrl();
        $url = substr($url, 0, -38); //the last 38 characters are the CTRL param
        $hash_source = strlen($url) . $url;
        $hash = hash_hmac('md5', $hash_source, trim($this->getParameter('apiKey')));
        $urlArgs = $this->getParameter('urlArgs');
        $ctrl = isset($urlArgs['ctrl']) ? $urlArgs['ctrl'] : '';
        if ($hash !== $ctrl) {
            throw new InvalidRequestException('signature mismatch');
        }
    }

    public function getData() {
        $this->validate('urlArgs', 'url');
        $this->verifyHash();
        return $this->getUrlArgs();
    }

    public function setUrlArgs($data) {
        $this->setParameter('urlArgs', $data);
    }

    public function getUrlArgs() {
        return $this->getParameter('urlArgs');
    }

    public function setUrl($url) {
        return $this->setParameter('url',$url);
    }

    public function getUrl() {
        return $this->getParameter('url');
    }

    public function sendData($data) {
        return $this->response = new BackRefResponse($this, $data);
    }
}
