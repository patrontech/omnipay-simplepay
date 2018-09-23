<?
namespace Omnipay\SimplePay\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;

/**
 * SimplePay Response
 *
 * Response to IRN (Refund) request
 *
 * @see \Omnipay\SimplePay\Gateway
 */
class RefundResponse extends AbstractResponse {

    const OFFSET_REF  = 0;
    const OFFSET_VAL  = 1;
    const OFFSET_TEXT = 2;
    const OFFSET_DATE = 3;
    const OFFSET_HASH = 4;
    public function __construct(RequestInterface $request, $data) {
        parent::__construct($request, $data);
    }

    protected function verifyHash() {
        $hash = $this->getRequest()->getHashString($this->data, array(self::OFFSET_HASH));
        return $hash === $this->data[self::OFFSET_HASH];
    }

    public function isSuccessful() {
        return $this->verifyHash() && $this->data[self::OFFSET_TEXT] == "OK";
    }

    public function getTransactionReference() {
        return $this->data[self::OFFSET_REF];
    }

}
