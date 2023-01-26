<?php

namespace App\Http\Controllers;


use App\Models\Account;
use App\Models\AccountPaymentGateway;
use App\Models\Currency;
use App\Models\PaymentGateway;
use App\Models\Timezone;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Services\PaymentGateway\Dummy;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Services\PaymentGateway\Stripe;
use Services\PaymentGateway\StripeSCA;
use Spatie\Permission\Exceptions\RoleDoesNotExist;
use Utils;
use Illuminate\Support\Facades\Lang;

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
            // Only super admin users gets to manage users so this call is safe
            'roles'                      => Role::all(),
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
            $latestVersion = Utils::parse_version((string)$response->getBody());
            $installedVersion = trim(file_get_contents(base_path('VERSION')));
        } catch (\Exception $exception) {
            \Log::warn("Error retrieving the latest Attendize version. ManageAccountController.getVersionInf() try/catch");
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

        if (!$account->validate($request->all())) {
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
            case Stripe::GATEWAY_NAME :
                $config = $request->get('stripe');
                break;
            case StripeSCA::GATEWAY_NAME :
                $config = $request->get('stripe_sca');
                break;
            case Dummy::GATEWAY_NAME :
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
            'organiser' => ['required'],
            'role' => ['required'],
            'email' => ['required', 'email', 'unique:users,email,NULL,id,account_id,' . Auth::user()->account_id],
        ];

        $messages = [
            'email.email'    => trans('Controllers.error.email.email'),
            'email.required' => trans('Controllers.error.email.required'),
            'email.unique'   => trans('Controllers.error.email.unique'),
            'organiser.required' => trans('Controllers.error.organiser.required'),
            'role.required' => trans('Controllers.error.role.required'),
        ];

        $validation = Validator::make($request->all(), $rules, $messages);

        if ($validation->fails()) {
            return response()->json([
                'status'   => 'error',
                'messages' => $validation->messages()->toArray(),
            ]);
        }

        $user = new User();

        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->email = $request->input('email');
        $user->account_id = Auth::user()->account_id;
        $user->organiser_id = $request->input('organiser');

        $temp_password = Str::random(8);
        $user->password = Hash::make($temp_password);

        $user->save();

        // Assigning role to user from selection. If for some reason the role errors out, we assign the default role.
        try {
            $assignedRole = Role::findById($request->input('role'));
        } catch (RoleDoesNotExist $e) {
            $assignedRole = Role::findByName('user');
        }

        $user->assignRole($assignedRole);
        $user->givePermissionTo($user->getAllPermissions());

        $this->sentInvitationEmailTo($user, $temp_password);

        return response()->json([
            'status'  => 'success',
            'message' => trans('Controllers.success_name_has_received_instruction', ['name' => $user->email]),
        ]);
    }

    /**
     * Update the user role
     *
     * @return JsonResponse
     */
    public function postUpdateUserRole(Request $request)
    {
        $rules = [
            'assigned_role' => ['required'],
            'user_id' => ['required'],
        ];

        $messages = [
            'user_id.required' => trans('Controllers.error.role.required'),
            'assigned_role.required' => trans('Controllers.error.role.required'),
        ];

        $validation = Validator::make($request->all(), $rules, $messages);

        if ($validation->fails()) {
            return response()->json([
                'status'   => 'error',
                'messages' => $validation->messages()->toArray(),
            ]);
        }

        /** @var \App\Models\User $user */
        $user = User::find($request->input('user_id'));
        $assignedRole = Role::findById($request->input('assigned_role'));
        $user->syncRoles($assignedRole);
        $user->syncPermissions($assignedRole->permissions()->get());

        return response()->json([
            'status'  => 'success',
            'message' => trans('Controllers.success_user_updated_role', ['name' => $user->email]),
        ]);
    }

    /**
     * Toggle the user can manage events
     *
     * @return JsonResponse
     */
    public function postToggleUserCanManageEvents(Request $request)
    {
        $rules = [
            'user_id' => ['required'],
        ];

        $messages = [
            'user_id.required' => trans('Controllers.error.role.required'),
        ];

        $validation = Validator::make($request->all(), $rules, $messages);

        if ($validation->fails()) {
            return response()->json([
                'status'   => 'error',
                'messages' => $validation->messages()->toArray(),
            ]);
        }

        /** @var \App\Models\User $user */
        $user = User::find($request->input('user_id'));

        // Toggle between the extended manage events permission
        $isChecked = $request->boolean('checked');
        $manageEvents = Permission::findByName('manage events', 'web');

        if ($isChecked && !$user->can('manage events')) {
            $user->givePermissionTo($manageEvents);
            $message = trans('Controllers.success_user_can_manage_events', ['name' => $user->email]);
        } elseif (!$isChecked) {
            $user->revokePermissionTo($manageEvents);
            $message = trans('Controllers.success_user_cannot_manage_events', ['name' => $user->email]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => $message,
        ]);
    }

    /**
     * Delete user
     *
     * @param Request $request
     * @param integer $userId
     *
     * @return JsonResponse
     */
    public function userDelete(Request $request, $userId)
    {
        /** @var \App\Models\User|null $user */
        $user = User::withTrashed()->find($userId);
        if (!$user) {
            return response()->json([
                'status'  => 'error',
                'message' => trans('Controllers.error_user_was_not_found'),
            ], 404);
        }

        if ($request->get('force', false)) {
            $user->forceDelete();
        } else {
            $user->delete();
        }

        $message = trans('Controllers.success_user_was_deleted', ['name' => $user->email]);
        return response()->json([
            'status'  => 'success',
            'message' => $message,
        ]);
    }

    /**
     * Restore deleted user
     *
     * @param Request $request
     * @param integer $userId
     *
     * @return JsonResponse
     */
    public function userRestore(Request $request, $userId)
    {
        /** @var \App\Models\User|null $user */
        $user = User::withTrashed()->find($userId);
        if (!$user) {
            return response()->json([
                'status'  => 'error',
                'message' => trans('Controllers.error_user_was_not_found'),
            ], 404);
        }

        $user->restore();

        $message = trans('Controllers.success_user_was_restored', ['name' => $user->email]);
        return response()->json([
            'status'  => 'success',
            'message' => $message,
        ]);
    }

    /**
     * @param Request $request
     * @param integer $userId
     *
     * @return JsonResponse
     */
    public function sendInvitationMessage(Request $request, $userId) {
        /** @var \App\Models\User|null $user */
        $user = User::find($userId);
        if (!$user) {
            return response()->json([
                'status'  => 'error',
                'message' => trans('Controllers.error_user_was_not_found'),
            ], 404);
        }

        $temp_password = Str::random(8);
        $user->password = Hash::make($temp_password);
        $user->save();

        $this->sentInvitationEmailTo($user, $temp_password);

        return response()->json([
            'status'  => 'success',
            'message' => trans('Controllers.success_name_has_received_instruction', ['name' => $user->email]),
        ]);
    }

    /**
     * Send an invitation email
     *
     * @param User $user
     * @param string $password
     */
    protected function sentInvitationEmailTo(User $user, string $password)
    {
        $data = [
            'user'          => $user,
            'temp_password' => $password,
            'inviter'       => Auth::user(),
        ];

        Mail::send('Emails.inviteUser', $data, static function ($message) use ($data) {
            $message->to($data['user']->email)
                ->subject(trans('Email.invite_user', [
                    'name' => $data['inviter']->first_name . ' ' . $data['inviter']->last_name,
                    'app'  => config('attendize.app_name')
                ]));
        });
    }
}
