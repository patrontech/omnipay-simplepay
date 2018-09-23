<?
namespace Omnipay\SimplePay\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;

/**
 * SimplePay Response
 *
 * Response to BackRef return from redirect
 *
 * @see \Omnipay\SimplePay\Gateway
 */
class BackRefResponse extends AbstractResponse {

    public function __construct(RequestInterface $request, $data) {
        parent::__construct($request, $data);
    }

    public function isSuccessful() {
        return false;
    }

    public function isPending() {
        // A return code indicating the transaction’s result.
        // If it begins with 000 or 001, the transaction is successful,
        // in case of other numbers it is unsuccessful, and the appropriate
        // message must be displayed on the page.
        return
            substr($this->data["RC"], 0, 3) == "000" ||
            substr($this->data["RC"], 0, 3) == "001";
    }

    public function getTransactionReference() {
        return $this->data['payrefno'];
    }

    public function getMessage() {
        return $this->data["RT"];
    }

    public function getCode() {
        return $this->data["RC"];
    }
}
