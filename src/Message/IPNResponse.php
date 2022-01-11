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
        $match = $hash === $this->data['HASH'];
        /**
         * The docs do not explicitly state this, but it seems like they are decoding html entities
         * before calculating the hash. This results in signature mismatch when the payload contains
         * encoded html entities. Only trying this if the original match fails here to keep it backward compatible.
         */
        if (!$match && is_array($data)) {
            $modifiedArray = $this->data;
            array_walk_recursive($modifiedArray, function(&$item) {
                if (is_string($item)) {
                    $item = html_entity_decode($item);
                }
            });
            $match = $this->getRequest()->getHashString($modifiedArray) === $this->data['HASH'];
        }
        return $match;
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

    public function getMessage() {
        if (!$this->verifyHash()) {
            return 'signature mismatch';
        }
        return $this->data['ORDERSTATUS'];
    }
}
