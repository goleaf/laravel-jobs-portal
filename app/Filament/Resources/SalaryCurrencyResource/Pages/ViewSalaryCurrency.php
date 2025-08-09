<?php

namespace App\Filament\Resources\SalaryCurrencyResource\Pages;

use App\Filament\Resources\SalaryCurrencyResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSalaryCurrency extends ViewRecord
{
    protected static string $resource = SalaryCurrencyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
