<?php

namespace Services\PaymentGateway;

class Dummy
{
    const GATEWAY_NAME = 'Dummy';

    private $transaction_data;

    private $gateway;

    public function __construct($gateway)
    {
        $this->gateway = $gateway;
        $this->options = [];
    }

    private function createTransactionData($order_total, $order_email, $event)
    {
        $token = uniqid();
        $this->transaction_data = [
            'amount' => $order_total,
            'currency' => $event->currency->code,
            'description' => 'Order for customer: '.$order_email,
            'card' => config('attendize.fake_card_data'),
            'token' => $token,
            'receipt_email' => $order_email,
        ];

        return $this->transaction_data;
    }

    public function startTransaction($order_total, $order_email, $event)
    {
        $this->createTransactionData($order_total, $order_email, $event);
        $transaction = $this->gateway->purchase($this->transaction_data);
        $response = $transaction->send();

        return $response;
    }

    public function getTransactionData()
    {
        return $this->transaction_data;
    }

    public function extractRequestParameters($request)
    {
    }

    public function completeTransaction($data)
    {
    }

    public function getAdditionalData()
    {
    }

    public function storeAdditionalData()
    {
        return false;
    }

    public function refundTransaction($order, $refund_amount, $refund_application_fee)
    {
        $request = $this->gateway->refund([
            'transactionReference' => $order->transaction_id,
            'amount'               => $refund_amount,
            'refundApplicationFee' => $refund_application_fee,
        ]);

        $response = $request->send();

        if ($response->isSuccessful()) {
            $refundResponse['successful'] = true;
        } else {
            $refundResponse['successful'] = false;
            $refundResponse['error_message'] = $response->getMessage();
        }

        return $refundResponse;
    }
}
