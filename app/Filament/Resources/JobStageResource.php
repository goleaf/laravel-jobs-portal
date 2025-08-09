<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JobStageResource\Pages;
use App\Models\JobStage;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class JobStageResource extends Resource
{
    protected static ?string $model = JobStage::class;

    protected static ?string $navigationIcon = 'heroicon-o-flag';

    protected static ?string $navigationGroup = 'Jobs';

    protected static ?string $navigationLabel = 'Job Stages';

    public static function getNavigationGroup(): ?string
    {
        return __('Jobs');
    }

    public static function getNavigationLabel(): string
    {
        return __('Job Stages');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('Details'))
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),
                        TextInput::make('order')
                            ->label(__('Order'))
                            ->numeric()->default(0),
                        Forms\Components\Toggle::make('is_active')->inline(false)->default(true),
                        Forms\Components\Toggle::make('is_default')->inline(false)->default(false),
                    ])
                    ->columns(3),

                Section::make(__('Assignment'))
                    ->schema([
                        TextInput::make('company_id')
                            ->numeric()
                            ->label(__('Company ID'))
                            ->helperText(__('Link to a company by its ID.')),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('company_id')->label(__('Company ID'))->toggleable(),
                TextColumn::make('order')->label(__('Order'))->sortable()->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_active')->boolean()->label(__('Active'))->sortable(),
                IconColumn::make('is_default')->boolean()->label(__('Default'))->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')->dateTime()->since()->sortable()->toggleable(),
            ])
            ->filters([
                TernaryFilter::make('is_active')->label(__('Active')),
                TernaryFilter::make('is_default')->label(__('Default')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListJobStages::route('/'),
            'create' => Pages\CreateJobStage::route('/create'),
            'view' => Pages\ViewJobStage::route('/{record}'),
            'edit' => Pages\EditJobStage::route('/{record}/edit'),
        ];
    }
}


