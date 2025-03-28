<?php

namespace App\Http\Controllers;

use App\Models\JobType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class JobTypeController extends Controller
{
    /**
     * Display the job types page.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        return view('job_types.index');
    }

    /**
     * Store a newly created job type.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:job_types',
            'description' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $jobType = JobType::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => __('messages.common.created_successfully', ['model' => __('messages.job_type.job_type')]),
            'data' => $jobType
        ]);
    }

    /**
     * Get job type details for editing.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id): JsonResponse
    {
        $jobType = JobType::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $jobType
        ]);
    }

    /**
     * Update the specified job type.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        $jobType = JobType::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:job_types,name,' . $id,
            'description' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $jobType->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => __('messages.common.updated_successfully', ['model' => __('messages.job_type.job_type')]),
            'data' => $jobType
        ]);
    }

    /**
     * Remove the specified job type.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        $jobType = JobType::findOrFail($id);
        $jobType->delete();

        return response()->json([
            'success' => true,
            'message' => __('messages.common.deleted_successfully', ['model' => __('messages.job_type.job_type')])
        ]);
    }
}
