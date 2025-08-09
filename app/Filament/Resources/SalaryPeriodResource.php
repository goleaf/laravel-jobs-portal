<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalaryPeriodResource\Pages;
use App\Models\SalaryPeriod;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SalaryPeriodResource extends Resource
{
    protected static ?string $model = SalaryPeriod::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    public static function getNavigationGroup(): ?string
    {
        return __('References');
    }

    public static function getNavigationLabel(): string
    {
        return __('Salary Periods');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('period')
                            ->label(__('Period'))
                            ->required()
                            ->maxLength(170)
                            ->unique(ignoreRecord: true),
                        Forms\Components\Textarea::make('description')
                            ->label(__('Description'))
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('is_default')
                            ->label(__('Default'))
                            ->inline(false)
                            ->default(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('period')
                    ->label(__('Period'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_default')
                    ->label(__('Default'))
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created'))
                    ->dateTime()
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_default')->label(__('Default')),
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
            'index' => Pages\ListSalaryPeriods::route('/'),
            'create' => Pages\CreateSalaryPeriod::route('/create'),
            'view' => Pages\ViewSalaryPeriod::route('/{record}'),
            'edit' => Pages\EditSalaryPeriod::route('/{record}/edit'),
        ];
    }
}
