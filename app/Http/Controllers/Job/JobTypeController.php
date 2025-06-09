<?php

namespace App\Http\Controllers\Job;

use App\Models\JobType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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
    public function store(JobStoreStoreJobTypeRequest $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:job_types',
            'description' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $jobType = JobType::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => __('messages.common.created_successfully', ['model' => __('messages.job_type.job_type')]),
            'data' => $jobType,
        ]);
    }

    /**
     * Get job type details for editing.
     *
     * @param  int  $id
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
     *
     * @param  int  $id
     */
    public function update(JobUpdateUpdateJobTypeRequest $request, $id): JsonResponse
    {
        $jobType = JobType::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:job_types,name,'.$id,
            'description' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $jobType->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => __('messages.common.updated_successfully', ['model' => __('messages.job_type.job_type')]),
            'data' => $jobType,
        ]);
    }

    /**
     * Remove the specified job type.
     *
     * @param  int  $id
     */
    public function destroy($id): JsonResponse
    {
        $jobType = JobType::findOrFail($id);
        $jobType->delete();

        return response()->json([
            'success' => true,
            'message' => __('messages.common.deleted_successfully', ['model' => __('messages.job_type.job_type')]),
        ]);
    }
}
