<?php
namespace Omnipay\SimplePay\Message;

use Omnipay\Common\Message\ResponseInterface;

/**
 * SimplePay Live Update Request
 * this starts the payment process
 */
class LiveUpdateRequest extends AbstractRequest {
    public function getEndpoint() {
        return parent::getEndpoint() . 'order/lu.php';
    }

    public function getHashString($data, $skip=array('HASH')) {
        $hash_source = '';

        $fields = array(
            'MERCHANT'        => true,
            'ORDER_REF'       => true,
            'ORDER_DATE'      => true,
            'ORDER_PNAME'     => true,
            'ORDER_PCODE'     => true,
            'ORDER_PINFO'     => false,
            'ORDER_PRICE'     => true,
            'ORDER_QTY'       => true,
            'ORDER_VAT'       => true,
            'ORDER_SHIPPING'  => false,
            'PRICES_CURRENCY' => true,
            'DISCOUNT'        => false,
            'PAY_METHOD'      => false,
        );
        foreach($fields as $field => $required) {
            if (!isset($data[$field])) {
                if ($required)
                    $hash_source .= 0;
            } elseif (is_array($data[$field])) {
                foreach($data[$field] as $item) {
                    $hash_source .= mb_strlen(''.$item, 'UTF-8') . $item;
                }
            } else {
                $hash_source .= mb_strlen(''.$data[$field], 'UTF-8') . $data[$field];
            }
        }

        $hash = hash_hmac('md5', $hash_source, trim($this->getParameter('apiKey')));

        return $hash;
    }

    public function setTransactionId($id) {
        $this->setParameter('transactionId', $id);
    }

    public function getTransactionId() {
        $this->getParameter('transactionId');
    }

    public function getData() {
        $this->validate('card', 'merchantId', 'transactionId', 'amount', 'currency', 'paymentType', 'timeoutUrl', 'returnUrl');
        $card = $this->getCard();
        $data = array(
            //order
            "MERCHANT"             => $this->getMerchantId(),
            "ORDER_REF"            => $this->getParameter('transactionId'),
            "ORDER_DATE"           => gmdate('Y-m-d H:i:s'),
            "ORDER_PNAME"          => [],
            "ORDER_PCODE"          => [],
            "ORDER_PINFO"          => [],
            "ORDER_PRICE"          => [],
            "ORDER_QTY"            => [],
            "ORDER_VAT"            => [],
            "PRICES_CURRENCY"      => $this->getParameter('currency'),
            "ORDER_SHIPPING"       => 0,
            "DISCOUNT"             => 0,
            "PAY_METHOD"           => $this->getParameter('paymentType'),
            "LANGUAGE"             => $this->getParameter('language') ?: "HU",
            "ORDER_TIMEOUT"        => $this->getParameter('timeout')  ?: 300,
            "TIMEOUT_URL"          => $this->getParameter('timeoutUrl'),
            "BACK_REF"             => $this->getParameter('returnUrl'),
            // "LU_ENABLE_TOKEN" => '',
            // "LU_TOKEN_TYPE" => '',

            //billing
            "BILL_FNAME"           => $card->getFirstName(),
            "BILL_LNAME"           => $card->getLastName(),
            // "BILL_COMPANY" => '',
            // "BILL_FISCALCODE" => '',
            "BILL_EMAIL"           => $card->getEmail(),
            "BILL_PHONE"           => $card->getBillingPhone(),
            // "BILL_FAX" => '',
            "BILL_ADDRESS"         => $card->getBillingAddress1(),
            "BILL_ADDRESS2"        => $card->getBillingAddress2(),
            "BILL_ZIPCODE"         => $card->getBillingPostcode(),
            "BILL_CITY"            => $card->getBillingCity(),
            "BILL_STATE"           => $card->getBillingState(),
            "BILL_COUNTRYCODE"     => $card->getBillingCountry(),

            //delivery
            "DELIVERY_FNAME"       => $card->getFirstName(),
            "DELIVERY_LNAME"       => $card->getLastName(),
            // "DELIVERY_COMPANY" => '',
            "DELIVERY_EMAIL"       => $card->getEmail(),
            "DELIVERY_PHONE"       => $card->getShippingPhone()    ?: $card->getBillingPhone(),
            "DELIVERY_ADDRESS"     => $card->getShippingAddress1() ?: $card->getBillingAddress1(),
            "DELIVERY_ADDRESS2"    => $card->getShippingAddress2() ?: $card->getBillingAddress2(),
            "DELIVERY_ZIPCODE"     => $card->getShippingPostcode() ?: $card->getBillingPostCode(),
            "DELIVERY_CITY"        => $card->getShippingCity()     ?: $card->getBillingCity(),
            "DELIVERY_STATE"       => $card->getShippingState()    ?: $card->getBillingState(),
            "DELIVERY_COUNTRYCODE" => $card->getShippingCountry()  ?: $card->getBillingCountry(),
        );

        $items = $this->getItems() ?: [[
            'name'  => 'n/a',
            'code'  => 'n/a',
            'info'  => 'n/a',
            'price' => $this->getParameter('amount'),
            'qty'   => 1,
            'vat'   => 0,
        ]];

        foreach($items as $item) {
            $data["ORDER_PNAME"][] = $item['name'];
            $data["ORDER_PCODE"][] = $item['code'];
            $data["ORDER_PINFO"][] = $item['info'];
            $data["ORDER_PRICE"][] = $item['price'];
            $data["ORDER_QTY"][]   = $item['qty'];
            $data["ORDER_VAT"][]   = $item['vat'];
        }

        // optional
        if ($shipping = $this->getParameter('shipping')) {
            $data['ORDER_SHIPPING'] = $shipping;
        }
        if ($discount = $this->getParameter('discount')) {
            $data['DISCOUNT'] = $discount;
        }

        $data['ORDER_HASH'] = $this->getHashString($data);

        return $data;
    }

    public function sendData($data) {
        return $this->response = new LiveUpdateResponse($this, $data);
    }
}
