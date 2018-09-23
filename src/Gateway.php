<?php
namespace Omnipay\SimplePay;

use Omnipay\Common\AbstractGateway;

/**
 * SimplePay Gateway
 *
 */
class Gateway extends AbstractGateway {
    public function getName() {
        return 'SimplePay';
    }

    public function getDefaultParameters() {
        return array(
            'apiKey' => '',
            'merchantId' => '',
            'testMode' => false,
        );
    }

    public function getApiKey() {
        return $this->getParameter('apiKey');
    }

    public function setApiKey($value) {
        return $this->setParameter('apiKey', $value);
    }

    public function getMerchantId() {
        return $this->getParameter('merchantId');
    }

    public function setMerchantId($value) {
        return $this->setParameter('merchantId', $value);
    }

    public function purchase(array $parameters = array()) {
        return $this->createRequest('\Omnipay\SimplePay\Message\LiveUpdateRequest', $parameters);
    }

    public function complete(array $parameters = array()) {
        return $this->createRequest('\Omnipay\SimplePay\Message\IPNRequest', $parameters);
    }

    public function backref(array $parameters = array()) {
         return $this->createRequest('\Omnipay\SimplePay\Message\BackRefRequest', $parameters);
    }

    public function query(array $parameters = array()) {
        return $this->createRequest('\Omnipay\SimplePay\Message\IOSRequest', $parameters);
    }

    public function refund(array $parameters = array()) {
        return $this->createRequest('\Omnipay\SimplePay\Message\RefundRequest', $parameters);
    }

}
