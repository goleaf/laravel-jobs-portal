<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCompanySizeRequest;
use App\Http\Requests\UpdateCompanySizeRequest;
use App\Models\Company;
use App\Models\CompanySize;
use App\Repositories\CompanySizeRepository;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * CompanySizeController
 * 
 * Handles company size management for the Laravel Job Portal
 * Context7 Level 4 transformation - Universal CRUD patterns
 */
class CompanySizeController extends AppBaseController
{
    /** @var CompanySizeRepository */
    private $companySizeRepository;

    public function __construct(CompanySizeRepository $companySizeRepo)
    {
        $this->companySizeRepository = $companySizeRepo;
    }

    /**
     * Display a listing of the CompanySize.
     *
     * @param  Request  $request
     * @return Factory|View
     *
     * @throws Exception
     */
    public function index(): View
    {
        return view('company_sizes.index');
    }

    /**
     * Show the form for creating a new CompanySize.
     */
    public function create(): View
    {
        return view('company_sizes.create');
    }

    /**
     * Store a newly created CompanySize in storage.
     */
    public function store(CreateCompanySizeRequest $request): JsonResponse
    {
        $input = $request->all();
        $companySize = $this->companySizeRepository->create($input);

        return $this->sendResponse($companySize, __('messages.flash.company_size_save'));
    }

    /**
     * Display the specified CompanySize.
     */
    public function show(CompanySize $companySize): View
    {
        return view('company_sizes.show', compact('companySize'));
    }

    /**
     * Show the form for editing the specified CompanySize.
     */
    public function edit(CompanySize $companySize): JsonResponse
    {
        return $this->sendResponse($companySize, __('messages.flash.retrieved'));
    }

    /**
     * Update the specified CompanySize in storage.
     */
    public function update(UpdateCompanySizeRequest $request, CompanySize $companySize): JsonResponse
    {
        $input = $request->all();
        $this->companySizeRepository->update($input, $companySize->id);

        return $this->sendSuccess(__('messages.flash.company_size_update'));
    }

    /**
     * Remove the specified CompanySize from storage.
     *
     * @throws Exception
     */
    public function destroy(CompanySize $companySize): JsonResponse
    {
        $companyModels = [
            Company::class,
        ];
        $result = canDelete($companyModels, 'company_size_id', $companySize->id);
        if ($result) {
            return $this->sendError(__('messages.flash.company_size_cant_delete'));
        }
        $companySize->delete();

        return $this->sendSuccess(__('messages.flash.company_size_delete'));
    }

    /**
     * Get company sizes data for DataTables
     */
    public function getData(Request $request): JsonResponse
    {
        $companySizes = $this->companySizeRepository->all();
        
        return datatables($companySizes)
            ->addColumn('action', function ($companySize) {
                return view('company_sizes.action', compact('companySize'))->render();
            })
            ->editColumn('created_at', function ($companySize) {
                return $companySize->created_at->format('Y-m-d H:i:s');
            })
            ->rawColumns(['action'])
            ->make(true);
    }
} 