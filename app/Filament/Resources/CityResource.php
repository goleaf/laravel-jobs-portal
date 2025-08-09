<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CityResource\Pages;
use App\Models\City;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CityResource extends Resource
{
    protected static ?string $model = City::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationGroup = 'References';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('state_id')
                            ->relationship('state', 'name')
                            ->searchable()
                            ->required(),
                        Forms\Components\TextInput::make('name')->required()->maxLength(180),
                        Forms\Components\TextInput::make('timezone')->maxLength(50),
                        Forms\Components\TextInput::make('population')->numeric(),
                        Forms\Components\TextInput::make('latitude')->numeric(),
                        Forms\Components\TextInput::make('longitude')->numeric(),
                        Forms\Components\Toggle::make('is_active')->inline(false)->default(true),
                        Forms\Components\Toggle::make('is_featured')->inline(false)->default(false),
                        Forms\Components\Toggle::make('is_metropolitan')->inline(false)->default(false),
                        Forms\Components\Toggle::make('is_major')->inline(false)->default(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('state.name')->label('State')->sortable(),
                Tables\Columns\TextColumn::make('timezone')->toggleable()->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('population')->numeric()->sortable(),
                Tables\Columns\IconColumn::make('is_active')->boolean()->sortable(),
                Tables\Columns\IconColumn::make('is_featured')->boolean()->sortable(),
                Tables\Columns\IconColumn::make('is_metropolitan')->boolean()->sortable(),
                Tables\Columns\IconColumn::make('is_major')->boolean()->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->since()->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active'),
                Tables\Filters\TernaryFilter::make('is_featured'),
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
            'index' => Pages\ListCities::route('/'),
            'create' => Pages\CreateCity::route('/create'),
            'view' => Pages\ViewCity::route('/{record}'),
            'edit' => Pages\EditCity::route('/{record}/edit'),
        ];
    }
}


