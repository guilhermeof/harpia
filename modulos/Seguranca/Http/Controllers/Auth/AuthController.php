<?php

namespace Modulos\Seguranca\Http\Controllers\Auth;

use Illuminate\Contracts\Auth\Guard;
use Modulos\Seguranca\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use App\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    protected $auth;

    protected $loginView = 'Seguranca::auth.login';

    /**
     * Create a new authentication controller instance.
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
        $this->middleware($this->guestMiddleware(), ['except' => ['logout']]);
    }

    /**
     * Handle a login request to the application.
     *
     * @param $request
     *
     * @return \Illuminate\Http\Response
     */
    public function postLogin(LoginRequest $request)
    {
        $credentials = $this->getCredentials($request);

        if ($this->auth->attempt($credentials, $request->has('remember'))) {
            return redirect()->intended('/admin');
        }

        return redirect('/login')
            ->withInput($request->only('usr_usuario', 'remember'))
            ->withErrors([
                'Usuário e/ou senha inválidos.',
            ]);
    }

    /** Get the needed authorization credentials from the request.
     *
     * @param Request $request
     *
     * @return array
     */
    protected function getCredentials(Request $request)
    {
        return [
            'usr_usuario' => $request->input('usr_usuario'),
            'password' => $request->input('usr_senha'),
            'usr_ativo' => 1,
        ];
    }
}