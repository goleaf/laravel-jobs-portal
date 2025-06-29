<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexJobRequest;
use App\Http\Requests\CreateJobRequest;
use App\Http\Requests\ShowJobRequest;
use App\Http\Requests\EditJobRequest;
use App\Http\Requests\DestroyJobRequest;
use App\Http\Requests\JobRequest;
use App\Models\Job;
use App\Repositories\JobRepository;
use Illuminate\Support\Facades\Gate;
use JustBetter\UniqueValues\Support\UniqueValue;
use Illuminate\Support\Str;
use Carbon\Carbon;

class JobController extends Controller
{
    protected $jobRepository;

    public function __construct(JobRepository $jobRepository)
    {
        $this->jobRepository = $jobRepository;
    }

    public function index(IndexJobRequest $request)
    {
        Gate::authorize('viewAny', Job::class);
        // List jobs
        $jobs = $this->jobRepository->all();

        return view('jobs.index', compact('jobs'));
    }

    public function create(CreateJobRequest $request)
    {
        Gate::authorize('create', Job::class);

        return view('jobs.create');
    }

    /**
     * Store a newly created job with unique reference
     */
    public function store(JobRequest $request)
    {
        Gate::authorize('create', Job::class);

        // Generate unique job reference
        $jobReference = UniqueValue::make()
            ->scope('job-references')
            ->attempts(10)
            ->generator(function (int $attempt): string {
                $year = Carbon::now()->format('Y');
                $baseNumber = str_pad((string) (1000 + $attempt), 4, '0', STR_PAD_LEFT);
                return "JOB-{$year}-{$baseNumber}";
            })
            ->generate();

        // Generate unique slug
        $jobSlug = UniqueValue::make()
            ->scope('job-slugs')
            ->attempts(15)
            ->generator(function (int $attempt) use ($request): string {
                $baseSlug = Str::slug($request->job_title);
                return $attempt === 0 ? $baseSlug : "{$baseSlug}-{$attempt}";
            })
            ->generate();

        // Create job with unique values
        $job = $this->jobRepository->create([
            'job_reference' => $jobReference,
            'slug' => $jobSlug,
            // ... other job fields
        ]);

        return response()->json([
            'success' => true,
            'job' => $job,
            'message' => __('Job created successfully with reference: :reference', ['reference' => $jobReference])
        ]);
    }

    public function show(ShowJobRequest $request, Job $job)
    {
        Gate::authorize('view', $job);

        return view('jobs.show', compact('job'));
    }

    public function edit(EditJobRequest $request, Job $job)
    {
        Gate::authorize('update', $job);

        return view('jobs.edit', compact('job'));
    }

    public function update(JobRequest $request, Job $job)
    {
        Gate::authorize('update', $job);
        $this->jobRepository->update($job->id, $request->validated());

        return redirect()->route('jobs.index')->with('success', 'Job updated successfully.');
    }

    public function destroy(DestroyJobRequest $request, Job $job)
    {
        Gate::authorize('delete', $job);
        $this->jobRepository->delete($job->id);

        return redirect()->route('jobs.index')->with('success', 'Job deleted successfully.');
    }

    /**
     * Generate unique application reference
     */
    public function generateApplicationReference($jobId, $candidateId)
    {
        return UniqueValue::make()
            ->scope('application-references')
            ->subject("job-{$jobId}-candidate-{$candidateId}")
            ->attempts(5)
            ->generator(function (int $attempt) use ($jobId, $candidateId): string {
                $timestamp = Carbon::now()->format('ymd');
                $suffix = $attempt > 0 ? "-{$attempt}" : '';
                return "APP-{$timestamp}-{$jobId}-{$candidateId}{$suffix}";
            })
            ->generate();
    }
}
