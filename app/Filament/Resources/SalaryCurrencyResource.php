<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalaryCurrencyResource\Pages;
use App\Models\SalaryCurrency;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SalaryCurrencyResource extends Resource
{
    protected static ?string $model = SalaryCurrency::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    public static function getNavigationGroup(): ?string
    {
        return __('References');
    }

    public static function getNavigationLabel(): string
    {
        return __('Salary Currencies');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('currency_name')
                            ->label(__('Currency Name'))
                            ->required()
                            ->maxLength(170)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('currency_code')
                            ->label(__('Code'))
                            ->required()
                            ->minLength(3)
                            ->maxLength(3),
                        Forms\Components\TextInput::make('currency_icon')
                            ->label(__('Symbol'))
                            ->maxLength(10)
                            ->default('$'),
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
                Tables\Columns\TextColumn::make('currency_name')
                    ->label(__('Currency Name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('currency_code')
                    ->label(__('Code'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('currency_icon')
                    ->label(__('Symbol'))
                    ->limit(4)
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
            'index' => Pages\ListSalaryCurrencies::route('/'),
            'create' => Pages\CreateSalaryCurrency::route('/create'),
            'view' => Pages\ViewSalaryCurrency::route('/{record}'),
            'edit' => Pages\EditSalaryCurrency::route('/{record}/edit'),
        ];
    }
}
