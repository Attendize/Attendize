<?php

namespace Services\PaymentGateway;

class Mgurush
{

    CONST GATEWAY_NAME = 'Mgurush';

    private $transaction_data;

    private $gateway;

    private $extra_params = [];

    public function __construct($gateway)
    {
        $this->gateway = $gateway;
        $this->options = [];
    }

    private function createTransactionData($order_total, $order_email, $event)
    {
        $this->transaction_data = [
            'order_id' => $order_email,
            'tnxRefNumber' => $order_email,
            'callback' => ""
        ];

        return $this->transaction_data;
    }

    public function startTransaction($order_total, $order_email, $event)
    {

        $this->createTransactionData($order_total, $order_email, $event);
        $transaction = $this->gateway->purchase($this->getTransactionData());
        $response = $transaction->send();

        return $response;
    }

    public function getTransactionData()
    {
        return $this->transaction_data;
    }

    public function extractRequestParameters($request)
    {
        foreach ($this->extra_params as $param) {
            if (!empty($request->get($param))) {
                $this->options[$param] = $request->get($param);
            }
        }
    }

    public function completeTransaction($data) {}

    public function getAdditionalData(){}

    public function storeAdditionalData()
    {
        return false;
    }

    public function refundTransaction($order, $refund_amount, $refund_application_fee)
    {

        $request = $this->gateway->refund([
            'transactionReference' => $order->transaction_id,
            'amount' => $refund_amount,
            'refundApplicationFee' => $refund_application_fee
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
