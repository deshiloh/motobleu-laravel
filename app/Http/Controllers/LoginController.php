<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\ResetPasswordRequest;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Sentry\Client;
use function Sentry\captureMessage;

class LoginController extends Controller
{

    /**
     * @param LoginRequest $request
     * @return RedirectResponse
     */
    public function authenticate(LoginRequest $request)
    {
        $credentials = $request->validated();
        $remember = $request->boolean('remember_me');

        if (Auth::attempt($credentials, $remember)) {

            if (!Auth::user()->is_actif) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors([
                    'email' => 'Ce compte est désactivé'
                ]);
            }

            $request->session()->regenerate();

            if (Auth::user()->email === 'contact@motobleu-paris.com') {
                return redirect()->intended(route('admin.reservations.index'));
            }

            return (Auth::user()->hasRole('super admin')) ?
                redirect()->intended(route('admin.homepage')) :
                redirect()->intended(route('front.home'));
        }

        return back()->withErrors([
            'email' => trans('auth.failed'),
        ])->onlyInput('email');
    }

    /**
     * Log the user out of the application.
     *
     * @param Request $request
     * @return Application|RedirectResponse|Redirector
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Form request forgot password
     * @return Application|Factory|View
     */
    public function forgotPasswordEdit()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send reset password request
     * @param Request $request
     * @return RedirectResponse
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email']
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    /**
     * Display Reset password form
     * @return Application|Factory|View
     */
    public function resetPasswordEdit(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->input('email')
        ]);
    }

    /**
     * Reset password update action
     * @param ResetPasswordRequest $request
     * @return RedirectResponse
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('front.home')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }
}
