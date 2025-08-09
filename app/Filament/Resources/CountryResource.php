<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CountryResource\Pages;
use App\Models\Country;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CountryResource extends Resource
{
    protected static ?string $model = Country::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-asia-australia';

    public static function getNavigationGroup(): ?string
    {
        return __('Location');
    }

    public static function getNavigationLabel(): string
    {
        return __('Countries');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('name')->label(__('Name'))->required()->maxLength(255),
                        Forms\Components\TextInput::make('short_code')->label(__('ISO Code'))->maxLength(10),
                        Forms\Components\TextInput::make('phone_code')->label(__('Phone Code'))->maxLength(10),
                        Forms\Components\TextInput::make('region')->label(__('Region'))->maxLength(50),
                        Forms\Components\TextInput::make('continent')->label(__('Continent'))->maxLength(50),
                        Forms\Components\Toggle::make('is_active')->label(__('Active'))->inline(false)->default(true),
                        Forms\Components\Toggle::make('is_default')->label(__('Default'))->inline(false)->default(false),
                        Forms\Components\Toggle::make('is_featured')->label(__('Featured'))->inline(false)->default(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label(__('Name'))
                    ->searchable()->sortable(),
                Tables\Columns\TextColumn::make('short_code')->label(__('ISO'))->nToggleable(false)->sortable(),
                Tables\Columns\TextColumn::make('phone_code')->label(__('Phone'))->toggleable(),
                Tables\Columns\IconColumn::make('is_active')->label(__('Active'))->boolean()->sortable(),
                Tables\Columns\IconColumn::make('is_default')->label(__('Default'))->boolean()->sortable(),
                Tables\Columns\IconColumn::make('is_featured')->label(__('Featured'))->boolean()->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label(__('Created'))->dateTime()->since()->sortable()->toggleable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label(__('Active')),
                Tables\Filters\TernaryFilter::make('is_default')->label(__('Default')),
                Tables\Filters\TernaryFilter::make('is_featured')->label(__('Featured')),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCountries::route('/'),
            'create' => Pages\CreateCountry::route('/create'),
            'view' => Pages\ViewCountry::route('/{record}'),
            'edit' => Pages\EditCountry::route('/{record}/edit'),
        ];
    }
}


