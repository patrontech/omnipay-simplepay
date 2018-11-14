<?
namespace Omnipay\SimplePay\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;

/**
 * SimplePay Response
 *
 * Response to IOS Request
 *
 * @see \Omnipay\SimplePay\Gateway
 */
class IOSResponse extends AbstractResponse {

    public function __construct(RequestInterface $request, $data) {
        parent::__construct($request, $data);
    }

    protected function verifyHash() {
        $hash = $this->getRequest()->getHashString($this->data);
        return $hash === $this->data['HASH'];
    }

    public function isSuccessful() {
        return $this->verifyHash() && $this->data["ORDER_STATUS"] == "COMPLETE";
    }

    public function isPending() {
        return $this->verifyHash() && (
            $this->data["ORDER_STATUS"] == "INIT" ||
            $this->data["ORDER_STATUS"] == "PAYMENT_AUTHORIZED" ||
            $this->data["ORDER_STATUS"] == "CARD_AUTHORIZED" ||
            $this->data["ORDER_STATUS"] == "IN_PROGRESS" ||
            $this->data["ORDER_STATUS"] == "WAITING_PAYMENT" ||
            $this->data["ORDER_STATUS"] == "INFRAUD"
        );
    }

    public function isCancelled() {
        $this->verifyHash() && $this->data["ORDER_STATUS"] == "TIMEOUT";
    }

    public function getTransactionReference() {
        return $this->data['REFNO'];
    }

    public function getTransactionId() {
        return $this->data['REFNOEXT'];
    }

    public function getMessage() {
        if (!$this->verifyHash()) {
            return 'signature mismatch';
        }
        return $this->data['ORDER_STATUS'];
    }
}
