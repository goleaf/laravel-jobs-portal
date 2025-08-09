<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanySizeResource\Pages;
use App\Models\CompanySize;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CompanySizeResource extends Resource
{
    protected static ?string $model = CompanySize::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'References';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('size')->required()->maxLength(255),
                        Forms\Components\TextInput::make('name')->maxLength(255),
                        Forms\Components\TextInput::make('display_name')->maxLength(255),
                        Forms\Components\Textarea::make('description')->columnSpanFull(),
                        Forms\Components\TextInput::make('min_employees')->numeric(),
                        Forms\Components\TextInput::make('max_employees')->numeric(),
                        Forms\Components\TextInput::make('order')->numeric(),
                        Forms\Components\TextInput::make('short_description')->maxLength(255),
                        Forms\Components\TextInput::make('icon')->maxLength(255),
                        Forms\Components\ColorPicker::make('color'),
                        Forms\Components\Toggle::make('is_active')->inline(false)->default(true),
                        Forms\Components\Toggle::make('is_default')->inline(false)->default(false),
                        Forms\Components\Toggle::make('is_featured')->inline(false)->default(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('size')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\IconColumn::make('is_active')->boolean()->sortable(),
                Tables\Columns\IconColumn::make('is_default')->boolean()->sortable(),
                Tables\Columns\IconColumn::make('is_featured')->boolean()->sortable(),
                Tables\Columns\TextColumn::make('order')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->since()->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active'),
                Tables\Filters\TernaryFilter::make('is_default'),
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
            'index' => Pages\ListCompanySizes::route('/'),
            'create' => Pages\CreateCompanySize::route('/create'),
            'view' => Pages\ViewCompanySize::route('/{record}'),
            'edit' => Pages\EditCompanySize::route('/{record}/edit'),
        ];
    }
}


