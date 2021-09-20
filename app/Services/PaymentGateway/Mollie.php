<?php

namespace Services\PaymentGateway;

class Mollie
{

    CONST GATEWAY_NAME = 'Mollie';

    private $transaction_data;

    /** @var \Omnipay\Mollie\Gateway */
    private $gateway;

    public function __construct($gateway)
    {
        $this->gateway = $gateway;
        $this->options = [];
    }

    private function createTransactionData($order_total, $order_email, $event)
    {

        $returnUrl = route('showEventCheckoutPaymentReturn', [
            'event_id' => $event->id,
            'is_payment_successful' => 1,
        ]);

        $this->transaction_data = [
            'amount' => $order_total,
            'currency' => $event->currency->code,
            'description' => 'Order for customer: ' . $order_email,
            'receipt_email' => $order_email,
            'returnUrl' => $returnUrl,
            'confirm' => true
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

    public function extractRequestParameters($request) {}

    public function completeTransaction($data) {
        $transaction = $this->gateway->completePurchase($data);
        $response = $transaction->send();

        return $response;
    }

    /** @param \Omnipay\Mollie\Message\Response\FetchTransactionResponse $response */
    public function getAdditionalData($response)
    {
        $additionalData['transactionReference'] = $response->getTransactionReference();
        return $additionalData;
    }

    public function storeAdditionalData()
    {
        return true;
    }

    /** 
     * @param \App\Models\Order $order
     */
    public function refundTransaction($order, $refund_amount, $refund_application_fee)
    {
        $request = $this->gateway->refund([
            'transactionReference' => $order->transaction_id,
            'currency'             => $order->event->currency->code,
            'amount'               => $refund_amount,
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