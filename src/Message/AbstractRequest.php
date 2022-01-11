<?php

namespace Omnipay\SimplePay\Message;

abstract class AbstractRequest  extends \Omnipay\Common\Message\AbstractRequest {
    const ENDPOINT_SANDBOX = "https://sandbox.simplepay.hu/payment/";
    const ENDPOINT_LIVE    = "https://secure.simplepay.hu/payment/";

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

    public function getEndpoint() {
        return $this->getTestMode() ? self::ENDPOINT_SANDBOX : self::ENDPOINT_LIVE;
    }

    public function getUri() {
        return '';
    }

    public function setPaymentType($value) {
        return $this->setParameter('paymentType', $value);
    }

    public function getPaymentType() {
        return $this->getParameter('paymentType');
    }

    public function setTimeoutUrl($value) {
        return $this->setParameter('timeoutUrl', $value);
    }

    public function getTimeoutUrl() {
        return $this->getParameter('timeoutUrl');
    }

    public function setReturnUrl($value) {
        return $this->setParameter('returnUrl', $value);
    }

    public function getReturnUrl() {
        return $this->getParameter('returnUrl');
    }

    public function getHttpMethod() {
        return 'POST';
    }

    public function getData() {
        return [];
    }

    public function setHeaders($data) {
       return  $this->setParameter('headers', $data);
    }

    public function getHeaders() {
        return $this->getParameter('headers');
    }

    public function getHashString($data,$skip=array('HASH')) {
        $hash_source = '';
        foreach ($data as $key => $item) {
            if (!in_array($key, $skip)) {
                if (is_array($item)) {
                    foreach($item as $i) {
                        $hash_source .= strlen(''.stripslashes($i)) . $i;
                    }
                } else {
                    $hash_source .= strlen(''.stripslashes($item)) . $item;
                }
            }
        }

        $hash = hash_hmac('md5', $hash_source, trim($this->getParameter('apiKey')));

        return $hash;
    }

    // http_build_query puts indices in the query and simplepay says no.
    // protected function buildPostQuery($data, $parent_key=0, $recur=0) {
    //     $return = [];
    //     if (!is_array($data)) return $data;
    //     foreach ($data as $key=>$val){
    //         if ($recur) {
    //             $key = $parent_key."[]";
    //         } elseif (is_int($key)) {
    //             $key = $parent_key.$key;
    //         }
    //         if (is_array($val) || is_object($val)) {
    //             $return[] = $this->buildPostQuery($val,$key,1);
    //             continue;
    //         }
    //         $return[] = urlencode($key)."=".urlencode($val);
    //     }
    //     return implode("&",$return);
    // }

    public function sendPost($data) {
        $headers = array_merge($this->getHeaders() ?: [], ['Content-Type' => 'application/x-www-form-urlencoded']);
        $data = http_build_query($data);
        $httpResponse = $this->httpClient->request('POST', $this->getEndpoint(),
            $headers,
            $data
        );
        $resultstring = $httpResponse->getBody();
        return (array)simplexml_load_string($resultstring);
    }

    // public function sendData($data) {
    //     $q = $data ? $this->buildPostQuery($data, '', '&') : "";
    //     $body = preg_replace('/(%5B)\d+(%5D=)/i', '$1$2', $q);
    //     $headers = [];
    //     $httpResponse = $this->httpClient->request($this->getHttpMethod(), $this->getEndpoint(), $headers, $body);
    //     // tel($httpResponse->getStatusCode());
    //     return $this->response = new Response($this, $httpResponse->getBody()->getContents(), $headers);
    // }
}
