<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JobShiftResource\Pages;
use App\Models\JobShift;
use Filament\Forms;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class JobShiftResource extends Resource
{
    protected static ?string $model = JobShift::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationGroup = 'Jobs';

    protected static ?string $navigationLabel = 'Job Shifts';

    public static function getNavigationGroup(): ?string
    {
        return __('Jobs');
    }

    public static function getNavigationLabel(): string
    {
        return __('Job Shifts');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('Details'))
                    ->schema([
                        TextInput::make('shift')
                            ->label(__('Shift'))
                            ->required()
                            ->maxLength(170),
                        Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make(__('Schedule'))
                    ->schema([
                        TimePicker::make('start_time')->seconds(false)->withoutSeconds(),
                        TimePicker::make('end_time')->seconds(false)->withoutSeconds(),
                        TextInput::make('duration_hours')->numeric()->minValue(0)->maxValue(24),
                        Toggle::make('is_flexible')->inline(false),
                    ])
                    ->columns(4),

                Section::make(__('Display'))
                    ->schema([
                        TextInput::make('icon')->maxLength(50),
                        ColorPicker::make('color')->rgba(false),
                        TextInput::make('sort_order')->numeric()->default(0),
                        Toggle::make('is_active')->inline(false),
                        Toggle::make('is_default')->inline(false),
                        Toggle::make('is_featured')->inline(false),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('shift')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('formatted_time_range')
                    ->label(__('Time Range'))
                    ->toggleable(),
                IconColumn::make('is_active')->boolean()->label(__('Active'))->sortable(),
                IconColumn::make('is_default')->boolean()->label(__('Default'))->sortable(),
                TextColumn::make('updated_at')->dateTime()->since()->sortable()->toggleable(),
            ])
            ->filters([
                TernaryFilter::make('is_active')->label(__('Active')),
                TernaryFilter::make('is_default')->label(__('Default')),
                TernaryFilter::make('is_flexible')->label(__('Flexible')),
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
            'index' => Pages\ListJobShifts::route('/'),
            'create' => Pages\CreateJobShift::route('/create'),
            'view' => Pages\ViewJobShift::route('/{record}'),
            'edit' => Pages\EditJobShift::route('/{record}/edit'),
        ];
    }
}


