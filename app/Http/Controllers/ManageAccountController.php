<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountPaymentGateway;
use App\Models\Currency;
use App\Models\PaymentGateway;
use App\Models\Timezone;
use App\Models\User;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Services\PaymentGateway\Dummy;
use Services\PaymentGateway\Stripe;
use Services\PaymentGateway\StripeSCA;
use Utils;

class ManageAccountController extends MyBaseController
{
    /**
     * Show the account modal
     *
     * @param  Request  $request
     * @return mixed
     */
    public function showEditAccount(Request $request)
    {
        $data = [
            'account'                    => Account::find(Auth::user()->account_id),
            'timezones'                  => Timezone::pluck('location', 'id'),
            'currencies'                 => Currency::pluck('title', 'id'),
            'payment_gateways'           => PaymentGateway::getAllWithDefaultSet(),
            'default_payment_gateway_id' => PaymentGateway::getDefaultPaymentGatewayId(),
            'account_payment_gateways'   => AccountPaymentGateway::scope()->get(),
            'version_info'               => $this->getVersionInfo(),
        ];

        return view('ManageAccount.Modals.EditAccount', $data);
    }

    public function getVersionInfo()
    {
        $installedVersion = null;
        $latestVersion = null;

        try {
            $http_client = new Client();
            $response = $http_client->get('https://raw.githubusercontent.com/Attendize/Attendize/master/VERSION');
            $latestVersion = Utils::parse_version((string) $response->getBody());
            $installedVersion = file_get_contents(base_path('VERSION'));
        } catch (\Exception $exception) {
            \Log::warn('Error retrieving the latest Attendize version. ManageAccountController.getVersionInf() try/catch');
            \Log::warn($exception);

            return false;
        }

        if ($installedVersion && $latestVersion) {
            return [
                'latest'      => $latestVersion,
                'installed'   => $installedVersion,
                'is_outdated' => (version_compare($installedVersion, $latestVersion) === -1) ? true : false,
            ];
        }

        return false;
    }

    /**
     * Edit an account
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function postEditAccount(Request $request)
    {
        $account = Account::find(Auth::user()->account_id);

        if (! $account->validate($request->all())) {
            return response()->json([
                'status'   => 'error',
                'messages' => $account->errors(),
            ]);
        }

        $account->first_name = $request->input('first_name');
        $account->last_name = $request->input('last_name');
        $account->email = $request->input('email');
        $account->timezone_id = $request->input('timezone_id');
        $account->currency_id = $request->input('currency_id');
        $account->save();

        return response()->json([
            'status'  => 'success',
            'id'      => $account->id,
            'message' => trans('Controllers.account_successfully_updated'),
        ]);
    }

    /**
     * Save account payment information
     *
     * @param  Request  $request
     * @return mixed
     */
    public function postEditAccountPayment(Request $request)
    {
        $account = Account::find(Auth::user()->account_id);
        $gateway_id = $request->get('payment_gateway');

        $payment_gateway = PaymentGateway::where('id', '=', $gateway_id)->first();

        $config = [];

        switch ($payment_gateway->name) {
            case Stripe::GATEWAY_NAME:
                $config = $request->get('stripe');
                break;
            case StripeSCA::GATEWAY_NAME:
                $config = $request->get('stripe_sca');
                break;
            case Dummy::GATEWAY_NAME:
                break;

        }

        PaymentGateway::query()->update(['default' => 0]);

        $payment_gateway->default = 1;
        $payment_gateway->save();

        $account_payment_gateway = AccountPaymentGateway::firstOrNew(
            [
                'payment_gateway_id' => $gateway_id,
                'account_id'         => $account->id,
            ]);

        $account_payment_gateway->config = $config;
        $account_payment_gateway->account_id = $account->id;
        $account_payment_gateway->payment_gateway_id = $gateway_id;
        $account_payment_gateway->save();

        $account->payment_gateway_id = $gateway_id;
        $account->save();

        return response()->json([
            'status'  => 'success',
            'id'      => $account_payment_gateway->id,
            'message' => trans('Controllers.payment_information_successfully_updated'),
        ]);
    }

    /**
     * Invite a user to the application
     *
     * @return JsonResponse
     */
    public function postInviteUser(Request $request)
    {
        $rules = [
            'email' => ['required', 'email', 'unique:users,email,NULL,id,account_id,'.Auth::user()->account_id],
        ];

        $messages = [
            'email.email'    => trans('Controllers.error.email.email'),
            'email.required' => trans('Controllers.error.email.required'),
            'email.unique'   => trans('Controllers.error.email.unique'),
        ];

        $validation = Validator::make($request->all(), $rules, $messages);

        if ($validation->fails()) {
            return response()->json([
                'status'   => 'error',
                'messages' => $validation->messages()->toArray(),
            ]);
        }

        $temp_password = Str::random(8);

        $user = new User();

        $user->email = $request->input('email');
        $user->password = Hash::make($temp_password);
        $user->account_id = Auth::user()->account_id;

        $user->save();

        $data = [
            'user'          => $user,
            'temp_password' => $temp_password,
            'inviter'       => Auth::user(),
        ];

        Mail::send(Lang::locale().'.Emails.inviteUser', $data, static function (Message $message) use ($data) {
            $message->to($data['user']->email)
                ->subject(trans('Email.invite_user', [
                    'name' => $data['inviter']->first_name.' '.$data['inviter']->last_name,
                    'app'  => config('attendize.app_name'),
                ]));
        });

        return response()->json([
            'status'  => 'success',
            'message' => trans('Controllers.success_name_has_received_instruction', ['name' => $user->email]),
        ]);
    }
}
