<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriptionResource\Pages;
use App\Filament\Resources\SubscriptionResource\RelationManagers;
use App\Models\Subscription;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationGroup = 'Billing';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('plan_id')->relationship('plan', 'name')->required()->searchable()->preload(),
                Forms\Components\Select::make('company_id')->relationship('company', 'name')->searchable()->preload(),
                Forms\Components\DatePicker::make('starts_at')->label('Starts')->required(),
                Forms\Components\DatePicker::make('ends_at')->label('Ends'),
                Forms\Components\Toggle::make('is_active')->inline(false)->default(true),
                Forms\Components\TextInput::make('status')->maxLength(50),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('plan.name')->label('Plan')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('company.name')->label('Company')->toggleable(),
                Tables\Columns\TextColumn::make('starts_at')->date()->sortable(),
                Tables\Columns\TextColumn::make('ends_at')->date()->sortable()->toggleable(),
                Tables\Columns\IconColumn::make('is_active')->boolean()->label('Active')->sortable(),
                Tables\Columns\TextColumn::make('status')->badge()->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->since()->sortable()->toggleable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active'),
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
            'index' => Pages\ManageSubscriptions::route('/'),
        ];
    }
}
