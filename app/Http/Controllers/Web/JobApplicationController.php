<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\ApplyJobJobApplicationRequest;
use App\Http\Requests\ShowApplyJobFormRequest;
use App\Mail\EmailToEmployer;
use App\Models\Candidate;
use App\Models\EmailTemplate;
use App\Models\Job;
use App\Models\Notification;
use App\Models\NotificationSetting;
use App\Repositories\JobApplicationRepository;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class JobApplicationController extends AppBaseController
{
    /** @var JobApplicationRepository */
    private $jobApplicationRepository;

    public function __construct(JobApplicationRepository $jobApplicationRepo)
    {
        $this->jobApplicationRepository = $jobApplicationRepo;
    }

    /**
     * @return Factory|View
     */
    public function showApplyJobForm(ShowApplyJobFormRequest $request, string $jobId)
    {
        $data = $this->jobApplicationRepository->showApplyJobForm($jobId);

        if (count($data['resumes']) <= 0) {
            return redirect()->back()->with('warning', __('messages.flash.there_are_no'));
        }

        return view('front_web_template.jobs.apply_job.apply_job')->with($data);
    }

    /**
     * @return mixed
     */
    public function applyJob(ApplyJobJobApplicationRequest $request)
    {
        $input = $request->all();

        $this->jobApplicationRepository->store($input);

        /** @var Job $job */
        $job = Job::with(['company.user', 'appliedJobs'])->findOrFail($input['job_id']);
        $employerId = $job->company->user->id;

        'draft' != $input['application_type'] ? 1 == NotificationSetting::where('key', 'JOB_APPLICATION_SUBMITTED')->first()->value
            ? addNotification([
                Notification::JOB_APPLICATION_SUBMITTED,
                $employerId,
                Notification::EMPLOYER,
                'Job Application submitted for '.$job->job_title,
            ]) : false : false;

        $candidateUniqueId = Candidate::whereUserId(getLoggedInUserId())->value('unique_id');
        $templateBody = EmailTemplate::whereTemplateName('Candidate Job Applied')->first();
        $keyVariable = [
            '{{employer_fullName}}', '{{candidate_name}}', '{{candidate_details_url}}', '{{job_title}}', '{{from_name}}',
        ];
        $value = [
            $job->company->user->full_name, getLoggedInUser()->full_name,
            asset('/candidate-details/'.$candidateUniqueId), $job->job_title, config('app.name'),
        ];
        $body = str_replace($keyVariable, $value, $templateBody->body);
        $data['body'] = $body;

        Mail::to($job->company->user->email)->send(new EmailToEmployer($data));

        return 'draft' == $input['application_type']
            ? $this->sendResponse($job->job_id, __('messages.flash.job_application_draft'))
            : $this->sendResponse($job->job_id, __('messages.flash.job_applied'));
    }
}
