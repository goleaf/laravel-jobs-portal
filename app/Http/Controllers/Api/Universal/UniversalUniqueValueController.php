<?php

namespace App\Http\Controllers\Api\Universal;

use App\Http\Controllers\Controller;
use App\Services\Universal\UniversalUniqueValueService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Universal Unique Value API Controller
 *
 * Provides REST API endpoints for generating unique values
 * using the Laravel Unique Values package integration.
 */
class UniversalUniqueValueController extends Controller
{
    public function __construct(
        protected UniversalUniqueValueService $uniqueValueService
    ) {}

    /**
     * Generate a unique job reference.
     */
    public function generateJobReference(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'job_id' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $jobId = $request->input('job_id');
            $reference = $this->uniqueValueService->generateJobReference($jobId);

            return response()->json([
                'success' => true,
                'data' => [
                    'job_reference' => $reference,
                    'job_id' => $jobId,
                    'format' => 'JOB-YYYY-XXXXXX',
                ],
                'message' => 'Job reference generated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate job reference',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate a unique application code.
     */
    public function generateApplicationCode(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'application_id' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $applicationId = $request->input('application_id');
            $code = $this->uniqueValueService->generateApplicationCode($applicationId);

            return response()->json([
                'success' => true,
                'data' => [
                    'application_code' => $code,
                    'application_id' => $applicationId,
                    'format' => 'APP-YYYYMMDD-XXXXX',
                ],
                'message' => 'Application code generated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate application code',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate a unique candidate code.
     */
    public function generateCandidateCode(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'candidate_id' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $candidateId = $request->input('candidate_id');
            $code = $this->uniqueValueService->generateCandidateCode($candidateId);

            return response()->json([
                'success' => true,
                'data' => [
                    'candidate_code' => $code,
                    'candidate_id' => $candidateId,
                    'format' => 'CAN-XXXXXX',
                ],
                'message' => 'Candidate code generated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate candidate code',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate a unique company code.
     */
    public function generateCompanyCode(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $companyId = $request->input('company_id');
            $code = $this->uniqueValueService->generateCompanyCode($companyId);

            return response()->json([
                'success' => true,
                'data' => [
                    'company_code' => $code,
                    'company_id' => $companyId,
                    'format' => 'COM-YYYY-XXXXX',
                ],
                'message' => 'Company code generated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate company code',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate a unique slug.
     */
    public function generateSlug(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|min:1|max:255',
            'scope' => 'nullable|string|min:3|max:50',
            'subject_id' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $title = $request->input('title');
            $scope = $request->input('scope', 'general-slug');
            $subjectId = $request->input('subject_id');

            $slug = $this->uniqueValueService->generateUniqueSlug($title, $scope, $subjectId);

            return response()->json([
                'success' => true,
                'data' => [
                    'slug' => $slug,
                    'original_title' => $title,
                    'scope' => $scope,
                    'subject_id' => $subjectId,
                ],
                'message' => 'Slug generated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate slug',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate multiple unique values in batch.
     */
    public function generateBatch(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'type' => ['required', 'string', Rule::in([
                'job-reference', 'application-code', 'candidate-code',
                'company-code', 'invoice-number', 'order-reference',
            ])],
            'subject_ids' => 'required|array|min:1|max:50',
            'subject_ids.*' => 'integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $type = $request->input('type');
            $subjectIds = $request->input('subject_ids');

            $results = $this->uniqueValueService->generateBatch($type, $subjectIds);

            return response()->json([
                'success' => true,
                'data' => [
                    'type' => $type,
                    'results' => $results,
                    'total_requested' => count($subjectIds),
                    'total_generated' => count(array_filter($results)),
                    'failed_generations' => array_keys(array_filter($results, fn ($v) => $v === null)),
                ],
                'message' => 'Batch generation completed',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate batch values',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get unique value generation statistics.
     */
    public function getStats(): JsonResponse
    {
        try {
            $stats = $this->uniqueValueService->getGenerationStats();

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Statistics retrieved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve statistics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate custom unique value.
     */
    public function generateCustom(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'scope' => 'required|string|min:3|max:100',
            'pattern' => 'required|string|min:1|max:100',
            'subject_id' => 'nullable|integer|min:1',
            'max_attempts' => 'nullable|integer|min:1|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $scope = $request->input('scope');
            $pattern = $request->input('pattern');
            $subjectId = $request->input('subject_id');
            $maxAttempts = $request->input('max_attempts', 3);

            $generator = function (int $attempt) use ($pattern): string {
                return str_replace(['{attempt}', '{counter}'], $attempt, $pattern);
            };

            $value = $this->uniqueValueService->generateCustomUnique(
                $scope,
                $generator,
                $subjectId,
                $maxAttempts
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'value' => $value,
                    'scope' => $scope,
                    'pattern' => $pattern,
                    'subject_id' => $subjectId,
                    'max_attempts' => $maxAttempts,
                ],
                'message' => 'Custom value generated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate custom value',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
