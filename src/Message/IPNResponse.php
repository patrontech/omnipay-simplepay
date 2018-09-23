<?
namespace Omnipay\SimplePay\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;

/**
 * SimplePay Response
 *
 * Response to Instant Payment Notification
 *
 * @see \Omnipay\SimplePay\Gateway
 */
class IPNResponse extends AbstractResponse {

    public function __construct(RequestInterface $request, $data) {
        parent::__construct($request, $data);
    }

    protected function verifyHash() {
        $hash = $this->getRequest()->getHashString($this->data);
        return $hash === $this->data['HASH'];
    }

    public function isSuccessful() {
        return $this->verifyHash() && $this->data["ORDERSTATUS"] == "COMPLETE";
    }

    public function isPending() {
        return $this->verifyHash() && (
            $this->data["ORDERSTATUS"] == "PAYMENT_AUTHORIZED" ||
            $this->data["ORDERSTATUS"] == "IN_PROGRESS" ||
            $this->data["ORDERSTATUS"] == "WAITING_PAYMENT"
        );
    }

    public function isCancelled() {
        $this->verifyHash() && $this->data["ORDERSTATUS"] == "TIMEOUT";
    }

    public function getTransactionReference() {
        return $this->data['REFNO'];
    }

    public function getTransactionId() {
        return $this->data['REFNOEXT'];
    }
}
