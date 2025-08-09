<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyResource\Pages;
use App\Filament\Resources\CompanyResource\RelationManagers;
use App\Models\Company;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('General'))
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('name')->required()->maxLength(255),
                        Forms\Components\TextInput::make('slug')->maxLength(255)->helperText(__('Leave empty to auto-generate')),
                        Forms\Components\TextInput::make('email')->email()->maxLength(255),
                        Forms\Components\TextInput::make('phone')->tel()->maxLength(50),
                        Forms\Components\TextInput::make('website')->url()->maxLength(255),
                        Forms\Components\Textarea::make('short_description')->columnSpanFull(),
                        Forms\Components\Textarea::make('description')->columnSpanFull(),
                    ]),
                Forms\Components\Section::make(__('Classification'))
                    ->columns(3)
                    ->schema([
                        Forms\Components\Select::make('industry_id')->relationship('industry', 'name')->searchable()->preload(),
                        Forms\Components\Select::make('company_size_id')->relationship('companySize', 'name')->searchable()->preload(),
                        Forms\Components\Select::make('ownership_type_id')->relationship('ownerShipType', 'name')->searchable()->preload(),
                        Forms\Components\TextInput::make('founded_year')->numeric()->minValue(1800)->maxValue((int) date('Y')),
                        Forms\Components\TextInput::make('employee_count')->numeric()->minValue(0),
                        Forms\Components\TextInput::make('unique_id')->maxLength(100),
                        Forms\Components\TextInput::make('company_type')->maxLength(100),
                        Forms\Components\TextInput::make('revenue')->maxLength(100),
                        Forms\Components\TextInput::make('market_cap')->maxLength(100),
                        Forms\Components\TextInput::make('stock_symbol')->maxLength(50),
                    ]),
                Forms\Components\Section::make(__('Location'))
                    ->columns(3)
                    ->schema([
                        Forms\Components\Select::make('country_id')->relationship('country', 'name')->searchable()->preload(),
                        Forms\Components\Select::make('state_id')->relationship('state', 'name')->searchable()->preload(),
                        Forms\Components\Select::make('city_id')->relationship('city', 'name')->searchable()->preload(),
                        Forms\Components\TextInput::make('address')->maxLength(255)->columnSpanFull(),
                        Forms\Components\TextInput::make('postal_code')->maxLength(20),
                        Forms\Components\TextInput::make('latitude')->numeric()->minValue(-90)->maxValue(90),
                        Forms\Components\TextInput::make('longitude')->numeric()->minValue(-180)->maxValue(180),
                        Forms\Components\TextInput::make('location')->maxLength(255),
                        Forms\Components\TextInput::make('location2')->maxLength(255),
                    ]),
                Forms\Components\Section::make(__('Media'))
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('logo')->label(__('Logo Path'))->maxLength(255),
                        Forms\Components\TextInput::make('cover_image')->label(__('Cover Image Path'))->maxLength(255),
                    ]),
                Forms\Components\Section::make(__('Social'))
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('social_facebook')->url()->maxLength(255),
                        Forms\Components\TextInput::make('social_twitter')->url()->maxLength(255),
                        Forms\Components\TextInput::make('social_linkedin')->url()->maxLength(255),
                        Forms\Components\TextInput::make('social_instagram')->url()->maxLength(255),
                        Forms\Components\TextInput::make('social_youtube')->url()->maxLength(255),
                        Forms\Components\TextInput::make('social_github')->url()->maxLength(255),
                        Forms\Components\TextInput::make('facebook_url')->url()->maxLength(255)->label(__('Facebook (legacy)'))->columnSpanFull()->hidden(),
                        Forms\Components\TextInput::make('twitter_url')->url()->maxLength(255)->label(__('Twitter (legacy)'))->hidden(),
                        Forms\Components\TextInput::make('linkedin_url')->url()->maxLength(255)->label(__('LinkedIn (legacy)'))->hidden(),
                        Forms\Components\TextInput::make('google_plus_url')->url()->maxLength(255)->label(__('Google+ (legacy)'))->hidden(),
                        Forms\Components\TextInput::make('pinterest_url')->url()->maxLength(255)->label(__('Pinterest (legacy)'))->hidden(),
                    ]),
                Forms\Components\Section::make(__('People & Culture'))
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('ceo_name')->maxLength(255),
                        Forms\Components\TextInput::make('ceo')->maxLength(255),
                        Forms\Components\TextInput::make('no_of_offices')->numeric()->minValue(0),
                        Forms\Components\TextInput::make('no_of_employees')->numeric()->minValue(0),
                        Forms\Components\Textarea::make('culture_description')->columnSpanFull(),
                        Forms\Components\Textarea::make('benefits')->columnSpanFull(),
                        Forms\Components\Textarea::make('technologies')->columnSpanFull(),
                        Forms\Components\Textarea::make('certifications')->columnSpanFull(),
                        Forms\Components\Textarea::make('awards')->columnSpanFull(),
                        Forms\Components\Textarea::make('office_locations')->columnSpanFull(),
                        Forms\Components\TextInput::make('working_hours')->maxLength(255),
                        Forms\Components\TextInput::make('dress_code')->maxLength(255),
                        Forms\Components\TextInput::make('headquarters')->maxLength(255),
                        Forms\Components\Textarea::make('mission_statement')->columnSpanFull(),
                        Forms\Components\Textarea::make('vision_statement')->columnSpanFull(),
                        Forms\Components\Textarea::make('values')->columnSpanFull(),
                        Forms\Components\Textarea::make('company_culture')->columnSpanFull(),
                        Forms\Components\Textarea::make('diversity_policy')->columnSpanFull(),
                    ]),
                Forms\Components\Section::make(__('Flags'))
                    ->columns(4)
                    ->schema([
                        Forms\Components\Toggle::make('is_active')->inline(false)->default(true),
                        Forms\Components\Toggle::make('is_featured')->inline(false)->default(false),
                        Forms\Components\Toggle::make('is_verified')->inline(false)->default(false),
                        Forms\Components\Toggle::make('is_private')->inline(false)->default(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('industry.name')->label(__('Industry'))->sortable(),
                Tables\Columns\TextColumn::make('companySize.name')->label(__('Size'))->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('ownershipType.name')->label(__('Ownership'))->toggleable(),
                Tables\Columns\IconColumn::make('is_active')->boolean()->sortable(),
                Tables\Columns\IconColumn::make('is_verified')->boolean()->sortable()->toggleable(),
                Tables\Columns\IconColumn::make('is_featured')->boolean()->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('created_at')->label(__('Created'))->dateTime()->since()->sortable()->toggleable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active'),
                Tables\Filters\TernaryFilter::make('is_verified'),
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompanies::route('/'),
            'create' => Pages\CreateCompany::route('/create'),
            'view' => Pages\ViewCompany::route('/{record}'),
            'edit' => Pages\EditCompany::route('/{record}/edit'),
        ];
    }
}
