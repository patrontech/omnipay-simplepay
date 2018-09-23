<?php
namespace Omnipay\SimplePay\Message;

use Omnipay\Common\Message\ResponseInterface;

/**
 * SimplePay IOS Request
 * this checks the status of a payment
 */
class IOSRequest extends AbstractRequest {

    public function getEndpoint() {
        return parent::getEndpoint() . 'order/ios.php';
    }
    public function getData() {
        $this->validate('reference');
        return array(
            'MERCHANT' => $this->getMerchantId(),
            'REFNOEXT' => $this->getReference(),
            'HASH' => $this->getHashString([$this->getMerchantId(),$this->getReference()],[])
        );
    }

    public function setReference($reference) {
        $this->setParameter('reference', $reference);
    }

    public function getReference() {
        return $this->getParameter('reference');
    }

    public function sendData($data) {
        $results = $this->sendPost($data);
        return $this->response = new IOSResponse($this, $results);
    }
}
