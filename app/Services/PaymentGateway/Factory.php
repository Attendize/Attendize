<?php

namespace Services\PaymentGateway;

use Omnipay\Omnipay;

/**
 * The intention of this factory is to create a service that is a wrapper around the relative Omnipay implementation
 * Each Gateway is a facade around the Omnipay implementation. Each Class can then handle the nuances. Additionally
 * having a factory should make it easier to implement any Omnipay Gateway
 *
 * Class GatewayFactory
 */
class Factory
{
    /**
     * @param $name
     * @param $paymentGatewayConfig
     * @return Dummy|Stripe|StripeSCA
     * @throws \Exception
     */
    public function create($name, $paymentGatewayConfig)
    {
        switch ($name) {

            case Dummy::GATEWAY_NAME:
                {

                    $gateway = Omnipay::create($name);
                    $gateway->initialize();

                    return new Dummy($gateway, $paymentGatewayConfig);
                }

            case Stripe::GATEWAY_NAME:
                {

                    $gateway = Omnipay::create($name);
                    $gateway->initialize($paymentGatewayConfig);

                    return new Stripe($gateway, $paymentGatewayConfig);
                }

            case StripeSCA::GATEWAY_NAME:
                {

                    $gateway = Omnipay::create($name);
                    $gateway->initialize($paymentGatewayConfig);

                    return new StripeSCA($gateway, $paymentGatewayConfig);

                }

            default:
                {
                    throw new \Exception('Invalid gateway specified');
                }
        }
    }
}
