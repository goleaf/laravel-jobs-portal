<?php

namespace App\Http\Controllers\Api\Universal;

use App\Http\Controllers\UniversalBaseController;
use App\Models\Company;
use App\Http\Resources\Universal\CompanyResource;
use App\Http\Resources\Universal\CompanyCollection;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Universal Company API Controller
 * Implements MCP best practices for API endpoints
 */
class CompanyApiController extends UniversalBaseController
{
    /**
     * Universal Pattern: Display a listing of the resource with caching
     */
    public function index(StoreRequest $request): JsonResponse
    {
        try {
            $cacheKey = $this->generateCacheKey($request, 'company_index');
            
            $query = Company::query();
            
            // Universal Pattern: Optimize with eager loading
            $query = $this->optimizeQuery($query, ['user'], ['applications']);
            
            // Universal Pattern: Apply filters
            if ($request->has('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }
            
            // Universal Pattern: Use cursor pagination for large datasets
            $companys = $this->paginateWithCursor($query);
            
            return $this->jsonResponse([
                'companys' => new App\Http\Resources\Universal\CompanyCollection($companys)
            ]);
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch companys', 500, [], [
                'exception' => $e->getMessage()
            ]);
        }
    }

    /**
     * Universal Pattern: Display the specified resource with caching
     */
    public function show($id): JsonResponse
    {
        try {
            $company = $this->findCached(Company::class, $id, ['user']);
            
            if (!$company) {
                return $this->errorResponse(ucfirst('company') . ' not found', 404);
            }
            
            return $this->jsonResponse([
                'company' => new App\Http\Resources\Universal\CompanyResource($company)
            ]);
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch company', 500, [], [
                'exception' => $e->getMessage()
            ]);
        }
    }

    /**
     * Universal Pattern: Store a newly created resource with validation
     */
    public function store(StoreRequest $request): JsonResponse
    {
        try {
            // Universal Pattern: Rate limited action
            return $this->rateLimitedAction($request, 'create_company', function () use ($request) {
                $validated = $request->validate([
                    'name' => 'required|string|max:255',
                    // Add more validation rules as needed
                ]);
                
                $company = $this->executeTransaction(function () use ($validated) {
                    return Company::create($validated);
                });
                
                $this->clearModelCache(Company::class, $company->id);
                
                return $this->jsonResponse([
                    'company' => new App\Http\Resources\Universal\CompanyResource($company)
                ], 'Created successfully', 201);
            });
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create company', 500, [], [
                'exception' => $e->getMessage()
            ]);
        }
    }

    /**
     * Universal Pattern: Update the specified resource with optimistic locking
     */
    public function update(UpdateRequest $request, $id): JsonResponse
    {
        try {
            $company = Company::findOrFail($id);
            
            // Universal Pattern: Rate limited action
            return $this->rateLimitedAction($request, 'update_company', function () use ($request, $company) {
                $validated = $request->validate([
                    'name' => 'sometimes|required|string|max:255',
                    // Add more validation rules as needed
                ]);
                
                $this->executeTransaction(function () use ($company, $validated) {
                    $company->update($validated);
                });
                
                $this->clearModelCache(Company::class, $company->id);
                
                return $this->jsonResponse([
                    'company' => new App\Http\Resources\Universal\CompanyResource($company->fresh())
                ], 'Updated successfully');
            });
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update company', 500, [], [
                'exception' => $e->getMessage()
            ]);
        }
    }

    /**
     * Universal Pattern: Remove the specified resource with soft delete
     */
    public function destroy($id): JsonResponse
    {
        try {
            $company = Company::findOrFail($id);
            
            // Universal Pattern: Rate limited action
            return $this->rateLimitedAction($request ?? request(), 'delete_company', function () use ($company) {
                $this->executeTransaction(function () use ($company) {
                    $company->delete();
                });
                
                $this->clearModelCache(Company::class, $company->id);
                
                return $this->jsonResponse([], 'Deleted successfully');
            });
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete company', 500, [], [
                'exception' => $e->getMessage()
            ]);
        }
    }
}