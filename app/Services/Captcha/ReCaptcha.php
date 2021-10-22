<?php

namespace Services\Captcha;

use Illuminate\Http\Request;

class ReCaptcha
{
    const CAPTCHA_NAME = 'recaptcha';

    /**
     * @var array
     */
    public $config;

    /**
     * @var string
     */
    public $recaptcha;

    /**
     * @var string
     */
    public $ip;

    /**
     * ReCaptcha constructor
     * @param $config
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * Determine if request was submitted by a human
     *
     * @param $request
     * @return bool
     */
    public function isHuman(Request $request)
    {
        $this->recaptcha = $request->get('g-recaptcha-response');
        $this->ip = $request->ip();
        if (! empty($this->config['captcha_secret'])) {
            try {
                $client = new \GuzzleHttp\Client();
                $response = $client->request('POST', 'https://www.recaptcha.net/recaptcha/api/siteverify', [
                    'form_params' => [
                        'secret' => $this->config['captcha_secret'],
                        'response' => $this->recaptcha,
                        'remoteip' => $this->ip,
                    ],
                ]);
                $responseData = json_decode($response->getBody());
                \Log::debug([$responseData]);

                return $responseData->success;
            } catch (\Exception $e) {
                \Log::debug([$e->getMessage()]);

                return false;
            }
        }
        \Log::debug('Captcha config missing');

        return false;
    }
}
