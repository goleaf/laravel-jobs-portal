<?php

namespace App\Filament\Resources;

use App\Actions\SendJobApplicationNotification;
use App\Filament\Resources\JobApplicationResource\Pages;
use App\Models\JobApplication;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;

class JobApplicationResource extends Resource
{
    protected static ?string $model = JobApplication::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Jobs';

    public static function getNavigationGroup(): ?string
    {
        return __('Jobs');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('status')->label(__('Status'))
                            ->options([
                                JobApplication::STATUS_PENDING => __('Pending'),
                                JobApplication::STATUS_REVIEWED => __('Reviewed'),
                                JobApplication::STATUS_SHORTLISTED => __('Shortlisted'),
                                JobApplication::STATUS_INTERVIEW_SCHEDULED => __('Interview Scheduled'),
                                JobApplication::STATUS_INTERVIEW_COMPLETED => __('Interview Completed'),
                                JobApplication::STATUS_OFFERED => __('Offered'),
                                JobApplication::STATUS_HIRED => __('Hired'),
                                JobApplication::STATUS_REJECTED => __('Rejected'),
                                JobApplication::STATUS_WITHDRAWN => __('Withdrawn'),
                            ])->required(),
                        Forms\Components\Textarea::make('notes')->columnSpanFull(),
                        Forms\Components\TextInput::make('expected_salary')->numeric()->label(__('Expected Salary')),
                        Forms\Components\DatePicker::make('available_start_date')->label(__('Available Start Date')),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('job.job_title')->label(__('Job'))->searchable()->sortable(),
                Tables\Columns\TextColumn::make('candidate.user.email')->label(__('Candidate'))->searchable(),
                Tables\Columns\TextColumn::make('status')->badge()->label(__('Status'))->sortable(),
                Tables\Columns\TextColumn::make('applied_at')->dateTime()->since()->label(__('Applied'))->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    JobApplication::STATUS_PENDING => __('Pending'),
                    JobApplication::STATUS_REVIEWED => __('Reviewed'),
                    JobApplication::STATUS_SHORTLISTED => __('Shortlisted'),
                    JobApplication::STATUS_INTERVIEW_SCHEDULED => __('Interview Scheduled'),
                    JobApplication::STATUS_INTERVIEW_COMPLETED => __('Interview Completed'),
                    JobApplication::STATUS_OFFERED => __('Offered'),
                    JobApplication::STATUS_HIRED => __('Hired'),
                    JobApplication::STATUS_REJECTED => __('Rejected'),
                    JobApplication::STATUS_WITHDRAWN => __('Withdrawn'),
                ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Action::make('shortlist')
                    ->label(__('Shortlist'))
                    ->icon('heroicon-o-user-plus')
                    ->visible(fn ($record) => $record->status !== JobApplication::STATUS_SHORTLISTED)
                    ->action(function (JobApplication $record) {
                        $old = $record->status;
                        $record->update(['status' => JobApplication::STATUS_SHORTLISTED, 'reviewed_at' => now()]);
                        SendJobApplicationNotification::dispatch($record, 'candidate');
                    }),
                Action::make('reject')
                    ->label(__('Reject'))
                    ->color('danger')
                    ->icon('heroicon-o-x-mark')
                    ->visible(fn ($record) => $record->status !== JobApplication::STATUS_REJECTED)
                    ->requiresConfirmation()
                    ->action(function (JobApplication $record) {
                        $record->update(['status' => JobApplication::STATUS_REJECTED, 'reviewed_at' => now()]);
                        SendJobApplicationNotification::dispatch($record, 'candidate');
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJobApplications::route('/'),
            'create' => Pages\CreateJobApplication::route('/create'),
            'view' => Pages\ViewJobApplication::route('/{record}'),
            'edit' => Pages\EditJobApplication::route('/{record}/edit'),
        ];
    }
}
