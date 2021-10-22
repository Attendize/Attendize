<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Redirect;
use Services\Captcha\Factory;
use View;

class UserLoginController extends Controller
{
    protected $captchaService;

    public function __construct()
    {
        $captchaConfig = config('attendize.captcha');
        if ($captchaConfig['captcha_is_on']) {
            $this->captchaService = Factory::create($captchaConfig);
        }

        $this->middleware('guest');
    }

    /**
     * Shows login form.
     *
     * @param  Request  $request
     *
     * @return mixed
     */
    public function showLogin(Request $request)
    {
        /*
         * If there's an ajax request to the login page assume the person has been
         * logged out and redirect them to the login page
         */
        if ($request->ajax()) {
            return response()->json([
                'status'      => 'success',
                'redirectUrl' => route('login'),
            ]);
        }

        return View::make('Public.LoginAndRegister.Login');
    }

    /**
     * Handles the login request.
     *
     * @param  Request  $request
     *
     * @return mixed
     */
    public function postLogin(Request $request)
    {
        $email = $request->get('email');
        $password = $request->get('password');

        if (empty($email) || empty($password)) {
            return Redirect::back()
                ->with(['message' => trans('Controllers.fill_email_and_password'), 'failed' => true])
                ->withInput();
        }

        if (is_object($this->captchaService)) {
            if (! $this->captchaService->isHuman($request)) {
                return Redirect::back()
                    ->with(['message' => trans('Controllers.incorrect_captcha'), 'failed' => true])
                    ->withInput();
            }
        }

        if (Auth::attempt(['email' => $email, 'password' => $password], true) === false) {
            return Redirect::back()
                ->with(['message' => trans('Controllers.login_password_incorrect'), 'failed' => true])
                ->withInput();
        }

        return redirect()->intended(route('showSelectOrganiser'));
    }
}
