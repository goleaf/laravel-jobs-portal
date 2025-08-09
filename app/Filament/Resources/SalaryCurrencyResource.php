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
                    ->columns(3)
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
                        Forms\Components\TextInput::make('currency_symbol')
                            ->label(__('Currency Symbol'))
                            ->maxLength(10),
                        Forms\Components\TextInput::make('exchange_rate')
                            ->label(__('Exchange Rate'))
                            ->numeric()->minValue(0),
                        Forms\Components\TextInput::make('base_currency')
                            ->label(__('Base Currency'))
                            ->maxLength(3),
                        Forms\Components\TextInput::make('decimal_places')
                            ->label(__('Decimal Places'))
                            ->numeric()->minValue(0)->maxValue(8),
                        Forms\Components\TextInput::make('number_format')
                            ->label(__('Number Format'))
                            ->maxLength(50),
                        Forms\Components\TagsInput::make('supported_countries')
                            ->label(__('Supported Countries'))
                            ->separator(','),
                        Forms\Components\DateTimePicker::make('last_rate_update')
                            ->label(__('Last Rate Update'))
                            ->seconds(false),
                        Forms\Components\Toggle::make('is_active')
                            ->label(__('Active'))
                            ->inline(false)
                            ->default(true),
                        Forms\Components\Toggle::make('is_default')
                            ->label(__('Default'))
                            ->inline(false)
                            ->default(false),
                        Forms\Components\Toggle::make('is_crypto')
                            ->label(__('Is Crypto'))
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
                Tables\Columns\TextColumn::make('currency_symbol')
                    ->label(__('Currency Symbol'))
                    ->limit(6)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('exchange_rate')
                    ->label(__('Exchange Rate'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('base_currency')
                    ->label(__('Base'))
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('decimal_places')
                    ->label(__('Decimals'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('Active'))
                    ->boolean()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_default')
                    ->label(__('Default'))
                    ->boolean()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_crypto')
                    ->label(__('Crypto'))
                    ->boolean()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('last_rate_update')
                    ->label(__('Last Update'))
                    ->dateTime()
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created'))
                    ->dateTime()
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_default')->label(__('Default')),
                Tables\Filters\TernaryFilter::make('is_active')->label(__('Active')),
                Tables\Filters\TernaryFilter::make('is_crypto')->label(__('Crypto')),
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
