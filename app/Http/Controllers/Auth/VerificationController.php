<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::ADMIN_HOME;

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        //        $this->middleware('auth');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    /**
     * @return string
     */
    public function redirectTo(RedirectToVerificationRequest $request)
    {
        $user = User::find($request->route('id'));
        $user->update(['is_verified' => 1]);

        if (null != getLoggedInUser()) {
            if (Auth::user()->hasRole('Admin')) {
                return RouteServiceProvider::ADMIN_HOME;
            }

            if (Auth::user()->hasRole('Employer')) {
                return RouteServiceProvider::EMPLOYER_HOME;
            }

            if (Auth::user()->hasRole('Candidate')) {
                return RouteServiceProvider::CANDIDATE_HOME;
            }
        } else {
            $userRole = $user->roles()->first()->name;
            if ('Candidate' == $userRole) {
                \Flash::success(__('messages.flash.success_verify'));

                return route('front.candidate.login');
            }
            \Flash::success(__('messages.flash.success_verify'));

            return route('front.employee.login');
        }
    }

    /**
     * Mark the authenticated user's email address as verified.
     *
     * @return Redirector|RedirectResponse
     *
     * @throws AuthorizationException
     */
    public function verify(VerifyVerificationRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = User::find($request->id);

        if ($request->route('id') != $user->getKey()) {
            throw new AuthorizationException();
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return redirect($this->redirectTo($request))->with('verified', true);
    }
}
