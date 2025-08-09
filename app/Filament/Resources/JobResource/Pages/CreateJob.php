<?php

namespace App\Filament\Resources\JobResource\Pages;

use App\Actions\CreateJob as CreateJobAction;
use App\Filament\Resources\JobResource;
use App\Models\Job;
use Filament\Resources\Pages\CreateRecord;

class CreateJob extends CreateRecord
{
    protected static string $resource = JobResource::class;

    protected function handleRecordCreation(array $data): Job
    {
        return CreateJobAction::run($data, auth()->id());
    }
}
