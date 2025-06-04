<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AppBaseController;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\MaritalStatus;
use App\Models\Skill;
use App\Models\SalaryPeriod;
use App\Models\Industry;
use App\Models\CompanySize;
use App\Models\FunctionalArea;
use App\Models\CareerLevel;
use App\Models\SalaryCurrency;
use App\Models\OwnerShipType;
use App\Models\Language;
use Illuminate\View\View;
use App\Http\Requests\Admin\StoreAdminRequest;
use App\Http\Requests\Admin\UpdateAdminRequest;

class MasterDataController extends AppBaseController
{
    /**
     * Display countries listing
     */
    public function countries(): View
    {
        return view('countries.index');
    }
    
    /**
     * Display states listing
     */
    public function states(): View
    {
        $countries = Country::orderBy('name')->pluck('name', 'id');
        return view('states.index', compact('countries'));
    }
    
    /**
     * Display cities listing
     */
    public function cities(): View
    {
        $states = State::orderBy('name')->pluck('name', 'id');
        return view('cities.index', compact('states'));
    }
    
    /**
     * Display marital status listing
     */
    public function maritalStatus(): View
    {
        return view('marital_status.index');
    }
    
    /**
     * Display skills listing
     */
    public function skills(): View
    {
        return view('skills.index');
    }
    
    /**
     * Display salary periods listing
     */
    public function salaryPeriods(): View
    {
        return view('salary_periods.index');
    }
    
    /**
     * Display industries listing
     */
    public function industries(): View
    {
        return view('industries.index');
    }
    
    /**
     * Display company sizes listing
     */
    public function companySizes(): View
    {
        return view('company_sizes.index');
    }
    
    /**
     * Display functional areas listing
     */
    public function functionalAreas(): View
    {
        return view('functional_areas.index');
    }
    
    /**
     * Display career levels listing
     */
    public function careerLevels(): View
    {
        return view('career_levels.index');
    }
    
    /**
     * Display salary currencies listing
     */
    public function salaryCurrencies(): View
    {
        return view('salary_currencies.index');
    }
    
    /**
     * Display ownership types listing
     */
    public function ownershipTypes(): View
    {
        return view('ownership_types.index');
    }
    
    /**
     * Display languages listing
     */
    public function languages(): View
    {
        return view('languages.index');
    }
} 