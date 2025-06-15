<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\AppBaseController;
use App\Models\NewsLetter;
use App\Providers\RouteServiceProvider;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WebController extends AppBaseController
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepository = $userRepo;
    }

    /**
     * @return Factory|RedirectResponse|View
     */
    public function index()
    {
        if (\Auth::user() && \Auth::user()->hasRole('Admin')) {
            return \Redirect::to(RouteServiceProvider::ADMIN_HOME);
        }

        if (\Auth::user() && \Auth::user()->hasRole(['Employer'])) {
            return \Redirect::to(RouteServiceProvider::EMPLOYER_HOME);
        }

        return view('auth.login');
    }

    /**
     * @return mixed
     */
    public function newsLetter(CreateNewsLetterNewsLetterWebRequest $request)
    {
        $input = $request->all();

        NewsLetter::create($input);

        return $this->sendSuccess(__('messages.flash.subscribed'));
    }
}
