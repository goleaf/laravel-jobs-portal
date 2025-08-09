<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CareerLevelResource\Pages;
use App\Models\CareerLevel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CareerLevelResource extends Resource
{
    protected static ?string $model = CareerLevel::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-trending-up';

    protected static ?string $navigationGroup = 'References';

    public static function getNavigationGroup(): ?string
    {
        return __('References');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('level_name')
                            ->label(__('Name'))
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', \Str::slug((string) $state))),
                        Forms\Components\TextInput::make('slug')->maxLength(255)->unique(ignoreRecord: true),
                        Forms\Components\Textarea::make('description')->columnSpanFull(),
                        Forms\Components\TextInput::make('level_order')->numeric()->required(),
                        Forms\Components\TextInput::make('min_experience_years')->numeric()->minValue(0),
                        Forms\Components\TextInput::make('max_experience_years')->numeric()->minValue(0),
                        Forms\Components\TextInput::make('icon')->maxLength(255),
                        Forms\Components\ColorPicker::make('color'),
                        Forms\Components\Toggle::make('is_active')->inline(false)->default(true),
                        Forms\Components\Toggle::make('is_default')->inline(false)->default(false),
                    ]),
                Forms\Components\Section::make(__('SEO'))
                    ->collapsed()
                    ->schema([
                        Forms\Components\TextInput::make('meta_title')->maxLength(255),
                        Forms\Components\Textarea::make('meta_description')->rows(3),
                        Forms\Components\TextInput::make('meta_keywords')->maxLength(255),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('level_name')->label(__('Name'))->searchable()->sortable(),
                Tables\Columns\TextColumn::make('level_order')->numeric()->sortable(),
                Tables\Columns\IconColumn::make('is_active')->boolean()->sortable(),
                Tables\Columns\IconColumn::make('is_default')->boolean()->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->since()->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active'),
                Tables\Filters\TernaryFilter::make('is_default'),
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
            'index' => Pages\ListCareerLevels::route('/'),
            'create' => Pages\CreateCareerLevel::route('/create'),
            'view' => Pages\ViewCareerLevel::route('/{record}'),
            'edit' => Pages\EditCareerLevel::route('/{record}/edit'),
        ];
    }
}


