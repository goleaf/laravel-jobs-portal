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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Details')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')->required()->maxLength(255),
                        Forms\Components\TextInput::make('stripe_plan_id')->label('Stripe Plan ID')->maxLength(255),
                        Forms\Components\TextInput::make('amount')->numeric()->required()->prefix('$')->rule('decimal:0,2'),
                        Forms\Components\Select::make('salary_currency_id')
                            ->label('Currency')
                            ->relationship('salaryCurrency', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('duration_days')->numeric()->label('Duration (days)')->default(30),
                        Forms\Components\TextInput::make('allowed_jobs')->numeric()->label('Allowed Jobs')->helperText('Use -1 for unlimited'),
                        Forms\Components\TextInput::make('max_featured_jobs')->numeric()->label('Max Featured Jobs')->default(0),
                    ]),
                Forms\Components\Section::make('Features')
                    ->columns(3)
                    ->schema([
                        Forms\Components\Toggle::make('is_active')->inline(false)->default(true),
                        Forms\Components\Toggle::make('is_featured')->inline(false)->default(false),
                        Forms\Components\Toggle::make('is_trial_plan')->inline(false)->label('Trial Plan')->default(false),
                        Forms\Components\Toggle::make('priority_support')->inline(false)->default(false),
                        Forms\Components\Toggle::make('analytics_access')->inline(false)->default(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('amount')->money(fn ($record) => optional($record->salaryCurrency)->name ?? 'USD')->sortable(),
                Tables\Columns\TextColumn::make('duration_days')->label('Days')->sortable(),
                Tables\Columns\IconColumn::make('is_trial_plan')->boolean()->label('Trial')->sortable(),
                Tables\Columns\IconColumn::make('is_active')->boolean()->label('Active')->sortable(),
                Tables\Columns\IconColumn::make('is_featured')->boolean()->label('Featured')->sortable(),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->since()->sortable()->toggleable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active'),
                Tables\Filters\TernaryFilter::make('is_featured'),
                Tables\Filters\TernaryFilter::make('is_trial_plan')->label('Trial'),
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
