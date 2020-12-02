<?php
namespace Omnipay\SimplePay\Message;

use Omnipay\Common\Message\ResponseInterface;

/**
 * SimplePay IRN (Refund) Request
 * this completes the payment process
 */
class RefundRequest extends AbstractRequest {

    public function getEndpoint() {
        return parent::getEndpoint() . 'order/irn.php';
    }

    public function getData() {
        $this->validate('amount', 'transactionReference', 'orderAmount');
        $data = array(
            'MERCHANT'       => $this->getMerchantId(),
            'ORDER_REF'      => $this->getTransactionReference(),
            'ORDER_AMOUNT'   => ceil($this->getOrderAmount()), // ceil was recommended by SimplePay support, their API started throwing errors if there were decimals
            'ORDER_CURRENCY' => $this->getCurrency(),
            'IRN_DATE'       => date('Y-m-d H:i:s'),
            'AMOUNT'         => $this->getAmount(),
        );
        $data['ORDER_HASH'] = $this->getHashString($data);
        return $data;
    }

    public function setOrderAmount($val) {
        $this->setParameter('orderAmount', $val);
    }

    public function getOrderAmount() {
        return $this->getParameter('orderAmount');
    }

    public function sendData($data) {
        $response = $this->sendPost($data);
        $response = reset($response);
        $results = explode('|', $response);
        return $this->response = new RefundResponse($this, $results);
    }
}
