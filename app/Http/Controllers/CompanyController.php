<?php

namespace App\Http\Controllers;

use App\Http\Requests\Company\ChangeIsActiveCompanyRequest;
use App\Http\Requests\Company\ChangeIsEmailVerifiedCompanyRequest;
use App\Http\Requests\Company\CreateCompanyRequest;
use App\Http\Requests\Company\DeleteReportedCompanyCompanyRequest;
use App\Http\Requests\Company\DestroyCompanyRequest;
use App\Http\Requests\Company\EditCompanyRequest;
use App\Http\Requests\Company\GetCitiesCompanyRequest;
use App\Http\Requests\Company\GetFollowersCompanyRequest;
use App\Http\Requests\Company\GetStatesCompanyRequest;
use App\Http\Requests\Company\IndexCompanyRequest;
use App\Http\Requests\Company\MarkAsFeaturedCompanyRequest;
use App\Http\Requests\Company\MarkAsUnFeaturedCompanyRequest;
use App\Http\Requests\Company\ResendEmailVerificationCompanyRequest;
use App\Http\Requests\Company\ShowCompanyRequest;
use App\Http\Requests\Company\ShowReportedCompaniesCompanyRequest;
use App\Http\Requests\Company\ShowReportedCompanyNoteCompanyRequest;
use App\Http\Requests\Company\UpdateCompanyRequest;
use App\Http\Requests\Company\UpdateCompanyUpdateCompanyCompanyRequest;
use App\Models\Company;
use App\Models\Country;
use App\Models\FeaturedRecord;
use App\Models\FrontSetting;
use App\Models\Notification;
use App\Models\NotificationSetting;
use App\Models\ReportedToCompany;
use App\Models\State;
use App\Models\Transaction;
use App\Repositories\CompanyRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Laracasts\Flash\Flash;

class CompanyController extends AppBaseController
{
    /** @var CompanyRepository */
    private $companyRepository;

    public function __construct(CompanyRepository $companyRepo)
    {
        $this->companyRepository = $companyRepo;
    }

    /**
     * Display a listing of the Company.
     *
     * @return Factory|View
     *
     * @throws \Exception
     */
    public function index(IndexCompanyRequest $request): View
    {
        return view('companies.index');
    }

    /**
     * Show the form for creating a new Company.
     *
     * @return Factory|View
     */
    public function create(IndexCompanyRequest $request): View
    {
        $data = $this->companyRepository->prepareData();

        // Use new scopes for better performance and filtering
        $countries = Country::active()->alphabetical()->pluck('name', 'id');
        $states = State::active()->alphabetical()->pluck('name', 'id');

        return view('companies.create', compact('countries', 'states'))->with('data', $data);
    }

    /**
     * Store a newly created Company in storage.
     *
     * @return Redirector|RedirectResponse
     *
     * @throws \Throwable
     */
    public function store(CreateCompanyRequest $request): RedirectResponse
    {
        $input = $request->all();
        $input['is_active'] = (isset($input['is_active'])) ? 1 : 0;

        $company = $this->companyRepository->store($input);

        Flash::success(__('messages.flash.company_save'));

        return redirect(route('company.index'));
    }

    /**
     * Display the specified Company.
     *
     * @return Factory|View
     */
    public function show(Company $company, ShowCompanyRequest $request): View
    {
        return view('companies.show')->with('company', $company);
    }

    /**
     * Show the form for editing the specified Company.
     *
     * @return Factory|View
     */
    public function edit(Company $company, EditCompanyRequest $request): View
    {
        $user = $company->user;
        $user->phone = preparePhoneNumber($user->phone, $user->region_code);
        $data = $this->companyRepository->prepareData();

        // Use new scopes for better performance and filtering
        $countries = Country::active()->alphabetical()->pluck('name', 'id');
        $states = State::active()->alphabetical()->pluck('name', 'id');
        $state = $cities = null;
        if (isset($user->country_id)) {
            $state = getStates($user->country_id);
        }
        if (isset($user->state_id)) {
            $cities = getCities($user->state_id);
        }

        return view('companies.edit', compact('data', 'company', 'cities', 'state', 'user', 'countries', 'states'));
    }

    /**
     * @return Redirector|RedirectResponse
     *
     * @throws \Throwable
     */
    public function update(Company $company, UpdateCompanyRequest $request): RedirectResponse
    {
        $input = $request->all();
        $input['is_active'] = (isset($input['is_active'])) ? 1 : 0;

        $company = $this->companyRepository->update($input, $company);

        Flash::success(__('messages.flash.company_update'));

        return redirect(route('company.index'));
    }

    /**
     * Remove the specified Company from storage.
     *
     * @throws \Exception
     */
    public function destroy(Company $company, DestroyCompanyRequest $request): JsonResponse
    {
        if ($company->user->hasRole('Employer')) {
            $this->companyRepository->delete($company->id);
            $company->user->media()->delete();
            $company->user->delete();

            return $this->sendSuccess(__('messages.flash.company_delete'));
        }

        return $this->sendError(__('messages.common.seems_message'));
    }

    /**
     * @return mixed
     */
    public function changeIsActive(Company $company, ChangeIsActiveCompanyRequest $request)
    {
        $isActive = $company->user->is_active;
        $company->user->update(['is_active' => ! $isActive]);

        if ($company) {
            if (Auth::user()->hasRole('Admin')) {
                $company->last_change = Auth::user()->id;
                $company->save();
            }
        }

        return $this->sendSuccess(__('messages.flash.status_change'));
    }

    /**
     * @return mixed
     */
    public function getStates(GetStatesCompanyRequest $request)
    {
        $postal = $request->get('postal');

        $states = getStates($postal);

        return $this->sendResponse($states, __('messages.flash.retrieved'));
    }

    /**
     * @return mixed
     */
    public function getCities(GetCitiesCompanyRequest $request)
    {
        $state = $request->get('state');
        $cities = getCities($state);

        return $this->sendResponse($cities, __('messages.flash.retrieved'));
    }

    /**
     * Show the form for editing the specified Company.
     *
     * @return Factory|View
     */
    public function editCompany(Company $company, EditCompanyRequest $request): View
    {
        $user = $company->user;
        $user->phone = preparePhoneNumber($user->phone, $user->region_code);
        $data = $this->companyRepository->prepareData();

        // Use new scopes for better performance and filtering
        $countries = Country::active()->alphabetical()->pluck('name', 'id');
        $states = State::active()->alphabetical()->pluck('name', 'id');
        $state = $cities = null;
        if (isset($user->country_id)) {
            $state = getStates($user->country_id);
        }
        if (isset($user->state_id)) {
            $cities = getCities($user->state_id);
        }
        $isFeaturedEnable = FrontSetting::where('key', 'featured_companies_enable')->first()->value;
        $maxFeaturedJob = FrontSetting::where('key', 'featured_companies_quota')->first()->value;
        $totalFeaturedJob = Company::Has('activeFeatured')->count();
        $isFeaturedAvilabal = ($totalFeaturedJob >= $maxFeaturedJob) ? false : true;

        return view(
            'employer.companies.edit',
            compact('data', 'company', 'cities', 'states', 'user', 'isFeaturedEnable', 'isFeaturedAvilabal')
        );
    }

    /**
     * Update the specified Company in storage.
     *
     * @return Redirector|RedirectResponse
     */
    public function updateCompany(Company $company, UpdateCompanyUpdateCompanyCompanyRequest $request): RedirectResponse
    {
        $input = $request->all();

        $company = $this->companyRepository->update($input, $company);

        Flash::success(__('messages.flash.employer_update'));

        return redirect(route('company.edit.form', Auth::user()->owner_id));
    }

    /**
     * @param  Request  $request
     * @return Application|Factory|View
     *
     * @throws \Exception
     */
    public function showReportedCompanies(ShowReportedCompaniesCompanyRequest $request): View
    {
        return view('admin.reported_to_companies.index');
    }

    /**
     * @return mixed
     *
     * @throws \Exception
     */
    public function deleteReportedCompany(ReportedToCompany $reportedToCompany, DeleteReportedCompanyCompanyRequest $request)
    {
        $reportedToCompany->delete();

        return $this->sendSuccess(__('messages.flash.reported_company_delete'));
    }

    /**
     * Display a listing of the Job.
     *
     * @param  Request  $request
     * @return Factory|View
     *
     * @throws \Exception
     */
    public function getFollowers(GetFollowersCompanyRequest $request): View
    {
        return view('employer.companies.followers');
    }

    /**
     * @return mixed
     */
    public function showReportedCompanyNote(ShowReportedCompanyNoteCompanyRequest $request)
    {
        $data = $this->companyRepository->getReportedToCompany($request->reportedToCompany);
        $data['date'] = \Carbon\Carbon::parse($data->created_at)->formatLocalized('%d %b, %Y');

        return $this->sendResponse($data, __('messages.flash.retrieved'));
    }

    /**
     * @param  mixed  $companyId
     * @return mixed
     */
    public function markAsFeatured($companyId, MarkAsFeaturedCompanyRequest $request)
    {
        try {
            $user = getLoggedInUser();
            $plan = $user->company->activeSubscription->plan;
            $featuredCompanyLimit = $plan->featured_company_limit;
            $currentFeaturedCompanies = Company::whereId($user->company->id)->whereIsFeatured(true)->count();
            if ($currentFeaturedCompanies >= $featuredCompanyLimit) {
                return $this->sendError(__('messages.flash.featured_company_limit_over'));
            }

            $company = Company::findOrFail($companyId);
            if ($company->id != $user->company->id) {
                return $this->sendError(__('messages.common.seems_message'));
            }

            DB::beginTransaction();

            $company->update(['is_featured' => 1]);
            $startDate = Carbon::now();
            $endDate = Carbon::now()->addDays(FrontSetting::where('key', 'featured_companies_days')->first()->value);
            $featuredRecord = FeaturedRecord::create([
                'owner_type' => Company::class,
                'owner_id' => $company->id,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);

            if (NotificationSetting::where('key', 'NEW_FEATURED_COMPANY_AVAILABLE')->first()->value == 1) {
                $users = getAdminNotificationUserIds();
                foreach ($users as $userId) {
                    addNotification([
                        Notification::NEW_FEATURED_COMPANY_AVAILABLE,
                        $userId,
                        Notification::ADMIN,
                        $company->name.' company is featured from '.$startDate.' to '.$endDate,
                    ]);
                }
            }

            $transaction = Transaction::create([
                'owner_type' => Company::class,
                'owner_id' => $company->id,
                'user_id' => $user->id,
                'amount' => FrontSetting::where('key', 'featured_companies_price')->first()->value,
                'type' => Transaction::FEATURED_COMPANY,
            ]);

            DB::commit();

            return $this->sendSuccess(__('messages.flash.company_featured'));
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->sendError($e->getMessage());
        }
    }

    /**
     * @param  mixed  $companyId
     * @return mixed
     */
    public function markAsUnFeatured($companyId, MarkAsUnFeaturedCompanyRequest $request)
    {
        $company = Company::findOrFail($companyId);
        if ($company->id != getLoggedInUser()->company->id) {
            return $this->sendError(__('messages.common.seems_message'));
        }

        $company->update(['is_featured' => 0]);
        $featuredRecord = FeaturedRecord::where('owner_type', Company::class)->where('owner_id', $company->id)->first();
        if (! empty($featuredRecord)) {
            $featuredRecord->delete();
        }

        return $this->sendSuccess(__('messages.flash.company_unfeatured'));
    }

    /**
     * @return mixed
     */
    public function changeIsEmailVerified(Company $company, ChangeIsEmailVerifiedCompanyRequest $request)
    {
        $company->user->update(['email_verified_at' => Carbon::now()]);

        return $this->sendSuccess(__('messages.flash.email_verified'));
    }

    /**
     * @return mixed
     */
    public function resendEmailVerification(Company $company, ResendEmailVerificationCompanyRequest $request)
    {
        $company->user->sendEmailVerificationNotification();

        return $this->sendSuccess(__('messages.flash.verification_email_sent'));
    }
}
