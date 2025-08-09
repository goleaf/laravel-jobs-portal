<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlanResource\Pages;
use App\Filament\Resources\PlanResource\RelationManagers;
use App\Models\Plan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PlanResource extends Resource
{
    protected static ?string $model = Plan::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationGroup = 'Billing';

    public static function getNavigationGroup(): ?string
    {
        return __('Billing');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Details'))
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')->required()->maxLength(255),
                        Forms\Components\TextInput::make('stripe_plan_id')->label(__('Stripe Plan ID'))->maxLength(255),
                        Forms\Components\TextInput::make('amount')->numeric()->required()->prefix('$')->rule('decimal:0,2'),
                        Forms\Components\Select::make('salary_currency_id')
                            ->label(__('Currency'))
                            ->relationship('salaryCurrency', 'currency_name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('duration_days')->numeric()->label(__('Duration (days)'))->default(30),
                        Forms\Components\TextInput::make('allowed_jobs')->numeric()->label(__('Allowed Jobs'))->helperText(__('Use -1 for unlimited')),
                        Forms\Components\TextInput::make('max_featured_jobs')->numeric()->label(__('Max Featured Jobs'))->default(0),
                    ]),
                Forms\Components\Section::make(__('Features'))
                    ->columns(3)
                    ->schema([
                        Forms\Components\Toggle::make('is_active')->inline(false)->default(true),
                        Forms\Components\Toggle::make('is_featured')->inline(false)->default(false),
                        Forms\Components\Toggle::make('is_trial_plan')->inline(false)->label(__('Trial Plan'))->default(false),
                        Forms\Components\Toggle::make('priority_support')->inline(false)->label(__('Priority Support'))->default(false),
                        Forms\Components\Toggle::make('analytics_access')->inline(false)->label(__('Analytics Access'))->default(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('amount')->money(fn ($record) => optional($record->salaryCurrency)->currency_code ?? 'USD')->sortable(),
                Tables\Columns\TextColumn::make('duration_days')->label(__('Days'))->sortable(),
                Tables\Columns\IconColumn::make('is_trial_plan')->boolean()->label(__('Trial'))->sortable(),
                Tables\Columns\IconColumn::make('is_active')->boolean()->label(__('Active'))->sortable(),
                Tables\Columns\IconColumn::make('is_featured')->boolean()->label(__('Featured'))->sortable(),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->since()->sortable()->toggleable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active'),
                Tables\Filters\TernaryFilter::make('is_featured'),
                Tables\Filters\TernaryFilter::make('is_trial_plan')->label(__('Trial')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ManagePlans::route('/'),
        ];
    }
}
