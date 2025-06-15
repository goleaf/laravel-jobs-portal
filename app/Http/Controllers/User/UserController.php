<?php

namespace App\Http\Controllers\User;

use App\Http\Requests\User\AdminCreateUserRequest;
use App\Http\Requests\User\ChangePasswordUserRequest;
use App\Http\Requests\User\ChangeThemeModeUserRequest;
use App\Http\Requests\User\CreateAdminUserRequest;
use App\Http\Requests\User\ProfileUpdateUserRequest;
use App\Http\Requests\User\UpdateAdminUserRequest;
use App\Http\Requests\User\UpdateLanguageUserRequest;
use App\Models\Candidate;
use App\Models\Company;
use App\Models\Job;
use App\Models\Transaction;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;
use Laracasts\Flash\Flash;

/**
 * Class UserController.
 */
class UserController extends AppBaseController
{
    /** @var UserRepository */
    private $userRepository;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepository = $userRepo;
    }

    public function changePassword(ChangePasswordUserRequest $request): JsonResponse
    {
        $input = $request->all();

        try {
            $user = $this->userRepository->changePassword($input);

            return $this->sendSuccess(__('messages.flash.password_update'));
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 422);
        }
    }

    public function profileUpdate(ProfileUpdateUserRequest $request): JsonResponse
    {
        $input = $request->all();

        try {
            $user = $this->userRepository->profileUpdate($input);

            return $this->sendResponse($user, __('messages.flash.profile_update'));
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 422);
        }
    }

    /**
     * Show the form for editing the specified User.
     */
    public function editProfile(): JsonResponse
    {
        $user = Auth::user();

        return $this->sendResponse($user, 'User retrieved successfully.');
    }

    public function updateLanguage(UpdateLanguageUserRequest $request): JsonResponse
    {
        $language = $request->get('language');

        /** @var User $user */
        $user = getLoggedInUser();
        $user->update(['language' => $language]);
        if (!empty($language)) {
            Session::put('languageName', $language);
        }

        return $this->sendSuccess(__('messages.flash.language_update'));
    }

    public function changeThemeMode(ChangeThemeModeUserRequest $request): RedirectResponse
    {
        $user = User::find(getLoggedInUser()->id);

        if (User::LIGHT_MODE == $user->theme_mode) {
            $user['theme_mode'] = User::DARK_MODE;
        } else {
            $user['theme_mode'] = User::LIGHT_MODE;
        }

        $user->update();

        return redirect(URL::previous());
    }

    /**
     * Get dashboard statistics using new model scopes.
     */
    public function getDashboardStats(): JsonResponse
    {
        $stats = [
            'recent_users' => User::recent(7)->count(),
            'candidates' => User::candidates()->count(),
            'employers' => User::employers()->count(),
            'admins' => User::admins()->count(),
            'active_jobs' => Job::active()->count(),
            'featured_jobs' => Job::featured()->count(),
            'urgent_jobs' => Job::urgent()->count(),
            'remote_jobs' => Job::remote()->count(),
            'active_companies' => Company::active()->count(),
            'featured_companies' => Company::featured()->count(),
        ];

        return $this->sendResponse($stats, 'Dashboard statistics retrieved successfully.');
    }

    /**
     * @return Application|Factory|\Illuminate\Contracts\View\View
     */
    public function adminIndex(): View
    {
        return view('admins.index');
    }

    /**
     * @return Application|Factory|\Illuminate\Contracts\View\View
     */
    public function adminCreate(AdminCreateUserRequest $request): View
    {
        return view('admins.create');
    }

    /**
     * @return Application|Redirector|RedirectResponse
     */
    public function adminStore(CreateAdminUserRequest $request): RedirectResponse
    {
        $input = $request->all();

        $admin = $this->userRepository->adminStore($input);

        Flash::success(__('messages.flash.admin_save'));

        return redirect(route('admin.admin.index'));
    }

    /**
     * @return Application|Factory|\Illuminate\Contracts\View\View
     */
    public function adminEdit(User $user): View
    {
        if ($user->hasRole('Admin')) {
            $user->phone = preparePhoneNumber($user->phone, $user->region_code);
        } else {
            return view('errors.404');
        }

        return view('admins.edit', compact('user'));
    }

    /**
     * @return Application|Redirector|RedirectResponse
     */
    public function adminUpdate(User $user, UpdateAdminUserRequest $request): RedirectResponse
    {
        $input = $request->all();

        $this->userRepository->updateAdmin($user, $input);

        Flash::success(__('messages.flash.admin_update'));

        return redirect(route('admin.admin.index'));
    }

    public function adminDestroy(User $user): JsonResponse
    {
        $Models = [
            Candidate::class,
            Company::class,
            Job::class,
        ];
        $result = canDelete($Models, 'last_change', $user->id);
        if ($result) {
            return $this->sendError(__('messages.flash.admin_cant_delete'));
        }

        $result = canDelete([Transaction::class], 'approved_id', $user->id);
        if ($result) {
            return $this->sendError(__('messages.flash.admin_cant_delete'));
        }

        if ($user->hasRole('Admin')) {
            $user->delete();
        } else {
            return $this->sendError(__('messages.common.seems_message'));
        }

        return $this->sendSuccess(__('messages.flash.admin_delete'));
    }
}
