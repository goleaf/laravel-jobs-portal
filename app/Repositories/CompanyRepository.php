<?php

namespace App\Repositories;

use App\Models\Company;
use Illuminate\Container\Container as Application;

class CompanyRepository extends BaseRepository
{
    protected $fieldSearchable = [
        "company_name",
        "details"
    ];

    public function model()
    {
        return Company::class;
    }

    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }
}
