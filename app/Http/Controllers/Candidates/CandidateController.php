<?php

namespace App\Http\Controllers\Candidates;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Candidates\CandidateUpdateGeneralInformationUpdateGeneralInformationCandidateRequest;
use App\Http\Requests\Candidates\CandidateUpdateOnlineProfileUpdateOnlineProfileCandidateRequest;
use App\Http\Requests\Candidates\CandidateUpdateProfileUpdateProfileCandidateRequest;
use App\Http\Requests\Candidates\ChangePasswordChangePasswordCandidateRequest;
use App\Http\Requests\Candidates\ChoosePreferenceCandidateRequest;
use App\Http\Requests\Candidates\DeletedResumeCandidateRequest;
use App\Http\Requests\Candidates\DeleteFavouriteJobCandidateRequest;
use App\Http\Requests\Candidates\DestroyFavouriteCompanyCandidateRequest;
use App\Http\Requests\Candidates\EditCandidateProfileCandidateRequest;
use App\Http\Requests\Candidates\EditJobAlertCandidateRequest;
use App\Http\Requests\Candidates\EditProfileCandidateRequest;
use App\Http\Requests\Candidates\GetCVTemplateCandidateRequest;
use App\Http\Requests\Candidates\ShowAppliedJobsCandidateRequest;
use App\Http\Requests\Candidates\ShowCandidateAppliedJobCandidateRequest;
use App\Http\Requests\Candidates\ShowFavouriteCompaniesCandidateRequest;
use App\Http\Requests\Candidates\ShowFavouriteJobsCandidateRequest;
use App\Http\Requests\Candidates\ShowScheduleSlotBookCandidateRequest;
use App\Http\Requests\Candidates\UpdateCandidateProfileProfileUpdateCandidateRequest;
use App\Http\Requests\Candidates\UpdateJobAlertCandidateRequest;
use App\Http\Requests\Candidates\UploadResumeCandidateRequest;
use App\Models\CandidateEducation;
use App\Models\CandidateExperience;
use App\Models\FavouriteCompany;
use App\Models\FavouriteJob;
use App\Models\JobApplication;
use App\Models\JobApplicationSchedule;
use App\Models\RequiredDegreeLevel;
use App\Models\User;
use App\Repositories\Candidates\CandidateRepository;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CandidateController extends AppBaseController
{
    /** @var CandidateRepository */
    private $candidateRepository;

    /**
     * CandidateController constructor.
     */
    public function __construct(CandidateRepository $candidateRepo)
    {
        $this->candidateRepository = $candidateRepo;
    }

    /**
     * @return Factory|View
     *
     * @throws \Exception
     */
    public function editProfile(EditProfileCandidateRequest $request): View
    {
        /** @var User $user */
        $user = \Auth::user();

        $user->phone = preparePhoneNumber($user->phone, $user->region_code);
        $data = $this->candidateRepository->prepareData();
        $countries = getCountries();
        $states = $cities = null;
        if (!empty($user->country_id)) {
            $states = getStates($user->country_id);
        }
        if (!empty($user->state_id)) {
            $cities = getCities($user->state_id);
        }
        $candidateSkills = $user->candidateSkill()->pluck('skill_id')->toArray();
        $candidateLanguage = $user->candidateLanguage()->pluck('language_id')->toArray();
        $sectionName = (null === $request->section) ? 'general' : $request->section;
        $data['sectionName'] = $sectionName;
        if ('general' == $sectionName) {
            if (!empty($user->country_id)) {
                $states = getStates($user->country_id);
            }
            if (!empty($user->state_id)) {
                $cities = getCities($user->state_id);
            }
        }
        if ('resume' == $sectionName) {
        }

        if ('career-informations' == $sectionName || 'cv-builder' == $sectionName) {
            $data['candidateExperiences'] = CandidateExperience::where(
                'candidate_id',
                $user->owner_id
            )->orderByDesc('id')->get();
            foreach ($data['candidateExperiences'] as $experience) {
                $experience->country = getCountryName($experience->country_id);
            }
            $data['candidateEducations'] = CandidateEducation::with('degreeLevel')->where(
                'candidate_id',
                $user->owner_id
            )->orderByDesc('id')->get();
            foreach ($data['candidateEducations'] as $education) {
                $education->country = getCountryName($education->country_id);
            }
            $data['degreeLevels'] = RequiredDegreeLevel::pluck('name', 'id');
        }

        return view(
            "candidate.profile.{$sectionName}",
            compact('user', 'data', 'countries', 'states', 'cities', 'candidateSkills', 'candidateLanguage')
        );
    }

    /**
     * @throws \Exception
     */
    public function showFavouriteJobs(ShowFavouriteJobsCandidateRequest $request): View
    {
        return view('candidate.favourite_jobs.index');
    }

    public function deleteFavouriteJob(FavouriteJob $favouriteJob, DeleteFavouriteJobCandidateRequest $request): JsonResponse
    {
        $userId = getLoggedInUserId();
        $fevouriteJobId = FavouriteJob::whereUserId($userId)->pluck('id')->toArray();

        if (!in_array($favouriteJob->id, $fevouriteJobId)) {
            return $this->sendError(__('messages.common.seems_message'));
        }

        $favouriteJob->delete();

        return $this->sendSuccess(__('messages.flash.fav_job_remove'));
    }

    /**
     * @return Redirector|RedirectResponse
     *
     * @throws \Throwable
     */
    public function updateProfile(CandidateUpdateProfileUpdateProfileCandidateRequest $request): RedirectResponse
    {
        $this->candidateRepository->updateProfile($request->all());

        \Flash::success(__('messages.flash.candidate_profile'));

        return redirect(route('candidate.profile'));
    }

    /**
     * @throws \Throwable
     */
    public function updateGeneralInformation(CandidateUpdateGeneralInformationUpdateGeneralInformationCandidateRequest $request): JsonResponse
    {
        $user = $this->candidateRepository->updateGeneralInformation($request->all());
        $user['candidateSkill'] = $user->candidateSkill()->pluck('name')->toArray();

        return $this->sendResponse($user, __('messages.flash.candidate_profile'));
    }

    /**
     * @throws \Throwable
     */
    public function updateOnlineProfile(CandidateUpdateOnlineProfileUpdateOnlineProfileCandidateRequest $request): JsonResponse
    {
        $user = $this->candidateRepository->updateGeneralInformation($request->all());
        $user['onlineProfileLayout'] = view(
            'candidate.profile.career_informations.show_online_profile',
            compact('user')
        )->render();
        $user['editonlineProfileLayout'] = view(
            'candidate.profile.career_informations.edit_online_profile',
            compact('user')
        )->render();

        return $this->sendResponse($user, __('messages.flash.candidate_profile'));
    }

    /**
     * @return array|string
     *
     * @throws \Throwable
     */
    public function getCVTemplate(GetCVTemplateCandidateRequest $request)
    {
        $user = \Auth::user();
        $data['user'] = $user;
        $data['candidateExperiences'] = CandidateExperience::where(
            'candidate_id',
            $user->owner_id
        )->orderByDesc('id')->get();
        foreach ($data['candidateExperiences'] as $experience) {
            $experience->country = getCountryName($experience->country_id);
        }
        $data['candidateEducations'] = CandidateEducation::with('degreeLevel')->where(
            'candidate_id',
            $user->owner_id
        )->orderByDesc('id')->get();
        foreach ($data['candidateEducations'] as $education) {
            $education->country = getCountryName($education->country_id);
        }

        $data['user']->phone = empty($data['user']->phone) ? 'N/A' : $data['user']->phone;

        return view('candidate.profile.cv_template')->with($data)->render();
    }

    /**
     * @return mixed
     */
    public function uploadResume(UploadResumeCandidateRequest $request)
    {
        $input = $request->all();
        $this->candidateRepository->uploadResume($input);

        return $this->sendSuccess(__('messages.flash.resume_update'));
    }

    /**
     * Download resume file.
     */
    public function downloadResume(string $candidateId, string $resumeId): BinaryFileResponse
    {
        $candidate = \Auth::user()->candidate;

        if ($candidate->id != $candidateId) {
            \Flash::error(__('messages.common.unauthorized'));

            return redirect()->back();
        }

        $path = storage_path('app/public/'.$candidate->resume_path);

        if (!file_exists($path)) {
            \Flash::error(__('messages.common.file_not_found'));

            return redirect()->back();
        }

        return response()->download($path);
    }

    /**
     * @throws \Exception
     */
    public function showFavouriteCompanies(ShowFavouriteCompaniesCandidateRequest $request): View
    {
        return view('candidate.favourite_companies.index');
    }

    /**
     * @return Factory|View
     */
    public function editJobAlert(EditJobAlertCandidateRequest $request): View
    {
        $user = \Auth::user();

        return view('candidate.job_alert.edit', compact('user'));
    }

    /**
     * @return Redirector|RedirectResponse
     */
    public function updateJobAlert(UpdateJobAlertCandidateRequest $request): RedirectResponse
    {
        $this->candidateRepository->updateJobAlerts($request->all());
        \Flash::success(__('messages.flash.job_alert'));

        return redirect(route('candidate.job.alert'));
    }

    public function changePassword(ChangePasswordChangePasswordCandidateRequest $request): JsonResponse
    {
        $input = $request->all();

        try {
            $user = $this->candidateRepository->changePassword($input);

            return $this->sendSuccess(__('messages.flash.password_update'));
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 422);
        }
    }

    /**
     * Show the form for editing the specified User.
     */
    public function editCandidateProfile(EditCandidateProfileCandidateRequest $request): JsonResponse
    {
        $user = \Auth::user();

        return $this->sendResponse($user, 'Candidate retrieved successfully.');
    }

    public function profileUpdate(UpdateCandidateProfileProfileUpdateCandidateRequest $request): JsonResponse
    {
        $input = $request->all();

        try {
            $employer = $this->candidateRepository->profileUpdate($input);
            \Flash::success(__('messages.flash.candidate_profile'));

            return $this->sendResponse($employer, __('messages.flash.candidate_profile'));
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 422);
        }
    }

    /**
     * @throws \Exception
     */
    public function showCandidateAppliedJob(ShowCandidateAppliedJobCandidateRequest $request): View
    {
        return view('candidate.applied_jobs.index');
    }

    /**
     * @return mixed
     *
     * @throws \Exception
     */
    public function deletedResume(Media $media, DeletedResumeCandidateRequest $request)
    {
        $userId = getLoggedInUserId();
        $candidateResumeId = Media::where('model_id', $userId)->pluck('id')->toArray();

        if (!in_array($media->id, $candidateResumeId)) {
            return $this->sendError(__('messages.common.seems_message'));
        }

        $media->delete();

        return $this->sendSuccess(__('messages.flash.resume_delete'));
    }

    /**
     * @return mixed
     */
    public function showAppliedJobs(JobApplication $jobApplication, ShowAppliedJobsCandidateRequest $request)
    {
        $userId = getLoggedInUserId();
        $candidateJobApplicationId = JobApplication::where('candidate_id', $userId)->pluck('id')->toArray();

        if (!in_array($jobApplication->id, $candidateJobApplicationId)) {
            return view('errors.404');
        }

        $jobApplication->load(['job', 'job.company', 'job.jobShift', 'job.jobCategory', 'job.jobType']);

        return view('candidate.applied_jobs.show', compact('jobApplication'));
    }

    public function showScheduleSlotBook(JobApplication $jobApplication, ShowScheduleSlotBookCandidateRequest $request): JsonResponse
    {
        $userId = getLoggedInUserId();
        $candidateJobApplicationId = JobApplication::where('candidate_id', $userId)->pluck('id')->toArray();

        if (!in_array($jobApplication->id, $candidateJobApplicationId)) {
            return $this->sendError(__('messages.common.seems_message'));
        }

        $jobApplicationSchedules = JobApplicationSchedule::whereJobApplicationId($jobApplication->id)->where(
            'status',
            JobApplicationSchedule::PENDING
        )->get();
        $jobApplicationSchedules->load('stage');
        $data['jobApplicationSchedules'] = $jobApplicationSchedules;
        $data['jobApplicationId'] = $jobApplication->id;

        return $this->sendResponse($data, 'Schedule retrieved successfully.');
    }

    public function choosePreference(JobApplication $jobApplication, ChoosePreferenceCandidateRequest $request): JsonResponse
    {
        $request->validated();
        $scheduleId = $request->get('schedule_id');
        $slotNotes = $request->get('choose_slot_notes');
        if (!isset($request->rejectSlot)) {
            JobApplicationSchedule::whereId($scheduleId)->update(['status' => JobApplicationSchedule::STATUS_SEND, 'rejected_slot_notes' => $slotNotes]);
        } else {
            $jobApplicationSchedules = JobApplicationSchedule::whereJobApplicationId($jobApplication->id);
            $lastRecord = $jobApplicationSchedules->latest()->first();
            JobApplicationSchedule::where([
                ['job_application_id', $jobApplication->id],
                ['stage_id', $lastRecord->stage_id],
                ['batch', $lastRecord->batch],
                ['status', JobApplicationSchedule::STATUS_NOT_SEND],
            ])->update([
                'status' => JobApplicationSchedule::STATUS_REJECTED,
                'rejected_slot_notes' => $slotNotes,
            ]);
        }

        if (isset($request->rejectSlot)) {
            return $this->sendSuccess(__('messages.flash.slot_reject'));
        }

        return $this->sendSuccess(__('messages.flash.slot_choose'));
    }

    public function destroyFavouriteCompany($id, DestroyFavouriteCompanyCandidateRequest $request)
    {
        $userId = getLoggedInUserId();
        $favouriteCompanyId = FavouriteCompany::whereUserId($userId)->pluck('id')->toArray();

        if (!in_array($id, $favouriteCompanyId)) {
            return $this->sendError(__('messages.common.seems_message'));
        }

        $favouriteCompany = FavouriteCompany::findOrFail($id);
        $favouriteCompany->delete();

        return $this->sendSuccess(__('messages.flash.fav_company_remove'));
    }
}
