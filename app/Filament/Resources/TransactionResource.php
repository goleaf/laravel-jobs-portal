<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';

    protected static ?string $navigationGroup = 'Billing';

    public static function getNavigationGroup(): ?string
    {
        return __('Billing');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('amount')->numeric()->required()->prefix('$')->rule('decimal:0,2'),
                Forms\Components\TextInput::make('currency')->maxLength(10)->required(),
                Forms\Components\TextInput::make('gateway')->maxLength(50)->required(),
                Forms\Components\Select::make('subscription_id')->relationship('subscription', 'id')->searchable()->preload(),
                Forms\Components\Select::make('company_id')->relationship('company', 'name')->searchable()->preload(),
                Forms\Components\TextInput::make('status')->maxLength(50)->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('#')->sortable(),
                Tables\Columns\TextColumn::make('amount')->label(__('Amount'))->sortable(),
                Tables\Columns\TextColumn::make('currency')->label(__('Currency'))->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('gateway')->label(__('Gateway'))->toggleable(),
                Tables\Columns\BadgeColumn::make('status')->colors([
                    'success' => 'paid',
                    'warning' => 'pending',
                    'danger' => 'failed',
                ])->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label(__('Created'))->dateTime()->since()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('gateway')->options([
                    'paypal' => __('PayPal'),
                    'paystack' => __('Paystack'),
                    'stripe' => __('Stripe'),
                ]),
                Tables\Filters\SelectFilter::make('status')->options([
                    'paid' => __('Paid'),
                    'pending' => __('Pending'),
                    'failed' => __('Failed'),
                    'refunded' => __('Refunded'),
                ]),
                Tables\Filters\Filter::make('date_range')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['until'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),
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
            'index' => Pages\ManageTransactions::route('/'),
        ];
    }
}
