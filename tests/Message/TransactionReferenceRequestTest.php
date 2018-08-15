<?php

namespace Omnipay\SimplePay\Message;

use Omnipay\SimplePay\Gateway;
use Omnipay\Tests\TestCase;

class TransactionReferenceRequestTest extends TestCase
{
    public function setUp()
    {
        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());
    }

    public function testGetData()
    {
        $request = new TransactionReferenceRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize(array(
            'transactionReference' => 'abc123',
        ));
        $data = $request->getData();
        $this->assertSame('abc123', $data['transactionReference']);
    }

    public function testSuccess()
    {
        $options['transactionReference'] = 'pass123';
        $response = $this->gateway->void($options)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertNotEmpty($response->getTransactionReference());
        $this->assertSame('Success', $response->getMessage());
    }

    public function testFollowupFailure()
    {
        // transactionReferences containing 'fail' fail
        $options['transactionReference'] = 'fail123';
        $response = $this->gateway->void($options)->send();

        $this->assertInstanceOf('\Omnipay\SimplePay\Message\Response', $response);
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertNotEmpty($response->getTransactionReference());
        $this->assertSame('Failure', $response->getMessage());
    }

}
