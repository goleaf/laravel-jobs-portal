<?php

namespace App\Http\Controllers\Job;

use App\Http\Controllers\Controller;
use App\Http\Requests\Job\StoreJobTypeRequest;
use App\Http\Requests\Job\UpdateJobTypeRequest;
use App\Models\JobType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JobTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }
    
    /**
     * Display the job types page.
     */
    public function index(): View
    {
        return view('job_types.index');
    }

    /**
     * Store a newly created job type.
     */
    public function store(StoreJobTypeRequest $request): JsonResponse
    {
        $jobType = JobType::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => __('job_type.messages.created_successfully'),
            'data' => $jobType,
        ]);
    }

    /**
     * Get job type details for editing.
     */
    public function edit($id): JsonResponse
    {
        $jobType = JobType::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $jobType,
        ]);
    }

    /**
     * Update the specified job type.
     */
    public function update(UpdateJobTypeRequest $request, $id): JsonResponse
    {
        $jobType = JobType::findOrFail($id);
        $jobType->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => __('job_type.messages.updated_successfully'),
            'data' => $jobType,
        ]);
    }

    /**
     * Remove the specified job type.
     */
    public function destroy($id): JsonResponse
    {
        $jobType = JobType::findOrFail($id);
        $jobType->delete();

        return response()->json([
            'success' => true,
            'message' => __('job_type.messages.deleted_successfully'),
        ]);
    }
} 