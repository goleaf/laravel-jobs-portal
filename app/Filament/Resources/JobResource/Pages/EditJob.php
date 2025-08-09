<?php

namespace App\Filament\Resources\JobResource\Pages;

use App\Actions\PublishJob;
use App\Filament\Resources\JobResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditJob extends EditRecord
{
    protected static string $resource = JobResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('publish')
                ->label(__('Publish'))
                ->icon('heroicon-o-paper-airplane')
                ->visible(fn () => $this->record && ! $this->record->published_at)
                ->requiresConfirmation()
                ->action(function () {
                    PublishJob::run($this->record);
                    $this->notify('success', __('Job published successfully'));
                    $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
                }),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (empty($data['slug']) && ! empty($data['job_title'])) {
            $data['slug'] = Str::slug($data['job_title']);
        }

        $data['meta_title'] = $data['meta_title'] ?? Str::limit(($data['job_title'] ?? '').' - Job Opening', 60);
        $data['meta_description'] = $data['meta_description'] ?? Str::limit(strip_tags((string) ($data['description'] ?? '')), 160);

        return $data;
    }
}
