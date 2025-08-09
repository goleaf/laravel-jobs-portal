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
                        Forms\Components\TextInput::make('iso_code')->label(__('ISO2'))->maxLength(10),
                        Forms\Components\TextInput::make('currency')->label(__('Currency'))->maxLength(10),
                        Forms\Components\TextInput::make('phone_code')->label(__('Phone Code'))->maxLength(10),
                        Forms\Components\TextInput::make('region')->label(__('Region'))->maxLength(50),
                        Forms\Components\TextInput::make('continent')->label(__('Continent'))->maxLength(50),
                        Forms\Components\TextInput::make('capital')->label(__('Capital'))->maxLength(100),
                        Forms\Components\TextInput::make('timezone')->label(__('Timezone'))->maxLength(100),
                        Forms\Components\TextInput::make('population')->label(__('Population'))->numeric()->minValue(0),
                        Forms\Components\TextInput::make('area_km2')->label(__('Area (km²)'))->numeric()->minValue(0),
                        Forms\Components\TextInput::make('flag_url')->label(__('Flag URL'))->url()->maxLength(255),
                        Forms\Components\TagsInput::make('languages')->label(__('Languages'))->separator(','),
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
                Tables\Columns\TextColumn::make('short_code')->label(__('ISO'))->sortable(),
                Tables\Columns\TextColumn::make('iso_code')->label(__('ISO2'))->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('currency')->label(__('Currency'))->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('phone_code')->label(__('Phone'))->toggleable(),
                Tables\Columns\TextColumn::make('capital')->label(__('Capital'))->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('timezone')->label(__('Timezone'))->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('population')->label(__('Population'))->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('area_km2')->label(__('Area (km²)'))->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\ImageColumn::make('flag_url')->label(__('Flag'))->square()->toggleable(isToggledHiddenByDefault: true),
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


