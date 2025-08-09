<?php

namespace App\Filament\Resources;

use App\Actions\PublishJob;
use App\Filament\Resources\JobResource\Pages;
use App\Models\Job;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class JobResource extends Resource
{
    protected static ?string $model = Job::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationGroup = 'Jobs';

    public static function getNavigationGroup(): ?string
    {
        return __('Jobs');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('General'))
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('job_title')
                            ->label(__('Title'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('slug')
                            ->maxLength(255)
                            ->helperText(__('Leave empty to auto-generate'))
                            ->dehydrated(false),
                        Forms\Components\Textarea::make('description')
                            ->rows(6)
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make(__('Details'))
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('salary_from')->numeric()->label(__('Salary From')),
                        Forms\Components\TextInput::make('salary_to')->numeric()->label(__('Salary To')),
                        Forms\Components\Select::make('salary_currency_id')
                            ->relationship('currency', 'currency_name')
                            ->searchable(),
                        Forms\Components\Select::make('salary_period_id')
                            ->relationship('salaryPeriod', 'period')
                            ->searchable(),
                        Forms\Components\Select::make('job_type_id')
                            ->relationship('jobType', 'name')
                            ->searchable(),
                        Forms\Components\Select::make('career_level_id')
                            ->relationship('careerLevel', 'level_name')
                            ->searchable(),
                        Forms\Components\Select::make('functional_area_id')
                            ->relationship('functionalArea', 'name')
                            ->searchable(),
                        Forms\Components\Select::make('job_shift_id')
                            ->relationship('jobShift', 'shift')
                            ->searchable(),
                        Forms\Components\DatePicker::make('job_expiry_date')->label(__('Expiry Date')),
                    ]),
                Forms\Components\Section::make(__('Location'))
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('country')->maxLength(255),
                        Forms\Components\TextInput::make('state')->maxLength(255),
                        Forms\Components\TextInput::make('city')->maxLength(255),
                    ]),
                Forms\Components\Section::make(__('Flags'))
                    ->columns(4)
                    ->schema([
                        Forms\Components\Toggle::make('is_freelance')->inline(false),
                        Forms\Components\Toggle::make('is_suspended')->inline(false),
                        Forms\Components\Toggle::make('hide_salary')->inline(false),
                        Forms\Components\Toggle::make('no_preference')->inline(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('job_title')->label(__('Title'))->searchable()->sortable(),
                Tables\Columns\TextColumn::make('jobType.name')->label(__('Type'))->sortable(),
                Tables\Columns\TextColumn::make('salary_from')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('salary_to')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('city')->sortable()->toggleable(),
                Tables\Columns\IconColumn::make('is_suspended')->label(__('Suspended'))->boolean()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_freelance')->label(__('Freelance'))->boolean()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->since()->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_suspended')->label(__('Suspended')),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make()->visible(fn ($record) => method_exists($record, 'trashed') && $record->trashed()),
                Tables\Actions\ForceDeleteAction::make()->visible(fn ($record) => method_exists($record, 'trashed') && $record->trashed()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJobs::route('/'),
            'create' => Pages\CreateJob::route('/create'),
            'view' => Pages\ViewJob::route('/{record}'),
            'edit' => Pages\EditJob::route('/{record}/edit'),
        ];
    }

}
