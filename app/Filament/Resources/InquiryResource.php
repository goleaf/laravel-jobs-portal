<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InquiryResource\Pages;
use App\Models\Inquiry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

class InquiryResource extends Resource
{
    protected static ?string $model = Inquiry::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'System';

    public static function getNavigationGroup(): ?string
    {
        return __('System');
    }

    public static function getNavigationLabel(): string
    {
        return __('Inquiries');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Contact'))
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('name')->required()->maxLength(255),
                        Forms\Components\TextInput::make('email')->email()->required()->maxLength(255),
                        Forms\Components\TextInput::make('phone_no')->label(__('Phone'))->maxLength(20),
                    ]),
                Forms\Components\Section::make(__('Message'))
                    ->columns(1)
                    ->schema([
                        Forms\Components\TextInput::make('subject')->required()->maxLength(255),
                        Forms\Components\Textarea::make('message')->rows(8)->required()->columnSpanFull(),
                    ]),
                Forms\Components\Section::make(__('Status'))
                    ->columns(4)
                    ->schema([
                        Forms\Components\Toggle::make('is_read')->inline(false)->default(false),
                        Forms\Components\Toggle::make('is_resolved')->inline(false)->default(false),
                        Forms\Components\Toggle::make('is_active')->inline(false)->default(true),
                        Forms\Components\Select::make('priority')->options([
                            Inquiry::PRIORITY_LOW => __('Low'),
                            Inquiry::PRIORITY_MEDIUM => __('Medium'),
                            Inquiry::PRIORITY_HIGH => __('High'),
                            Inquiry::PRIORITY_URGENT => __('Urgent'),
                        ])->searchable(),
                        Forms\Components\Select::make('status')->options([
                            Inquiry::STATUS_PENDING => __('Pending'),
                            Inquiry::STATUS_IN_PROGRESS => __('In Progress'),
                            Inquiry::STATUS_RESOLVED => __('Resolved'),
                            Inquiry::STATUS_CLOSED => __('Closed'),
                        ])->searchable(),
                        Forms\Components\Select::make('category')->options([
                            Inquiry::CATEGORY_GENERAL => __('General'),
                            Inquiry::CATEGORY_TECHNICAL => __('Technical'),
                            Inquiry::CATEGORY_BILLING => __('Billing'),
                            Inquiry::CATEGORY_SUPPORT => __('Support'),
                            Inquiry::CATEGORY_FEATURE_REQUEST => __('Feature Request'),
                            Inquiry::CATEGORY_BUG_REPORT => __('Bug Report'),
                        ])->searchable(),
                        Forms\Components\Select::make('assigned_to')->relationship('assignedUser', 'name')->label(__('Assigned To'))->searchable()->preload(),
                        Forms\Components\DateTimePicker::make('resolved_at')->seconds(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('Email'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject')
                    ->label(__('Subject'))
                    ->limit(40)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('priority_label')
                    ->label(__('Priority'))
                    ->badge()
                    ->color(fn (Inquiry $record): string => match ($record->priority) {
                        Inquiry::PRIORITY_URGENT => 'danger',
                        Inquiry::PRIORITY_HIGH => 'warning',
                        Inquiry::PRIORITY_MEDIUM => 'info',
                        Inquiry::PRIORITY_LOW => 'success',
                        default => 'gray',
                    })
                    ->sortable('priority'),
                Tables\Columns\TextColumn::make('status_label')
                    ->label(__('Status'))
                    ->badge()
                    ->color(fn (Inquiry $record): string => match ($record->status) {
                        Inquiry::STATUS_RESOLVED => 'success',
                        Inquiry::STATUS_IN_PROGRESS => 'warning',
                        Inquiry::STATUS_CLOSED => 'gray',
                        Inquiry::STATUS_PENDING => 'info',
                        default => 'gray',
                    })
                    ->sortable('status'),
                Tables\Columns\TextColumn::make('category_label')
                    ->label(__('Category'))
                    ->badge()
                    ->color('info')
                    ->sortable('category')
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_read')
                    ->label(__('Read'))
                    ->boolean()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_resolved')
                    ->label(__('Resolved'))
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('assignedUser.name')
                    ->label(__('Assigned'))
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created'))
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_read')->label(__('Read')),
                Tables\Filters\TernaryFilter::make('is_resolved')->label(__('Resolved')),
                Tables\Filters\TernaryFilter::make('is_active')->label(__('Active')),
                Tables\Filters\SelectFilter::make('status')->options([
                    Inquiry::STATUS_PENDING => __('Pending'),
                    Inquiry::STATUS_IN_PROGRESS => __('In Progress'),
                    Inquiry::STATUS_RESOLVED => __('Resolved'),
                    Inquiry::STATUS_CLOSED => __('Closed'),
                ]),
                Tables\Filters\SelectFilter::make('priority')->options([
                    Inquiry::PRIORITY_LOW => __('Low'),
                    Inquiry::PRIORITY_MEDIUM => __('Medium'),
                    Inquiry::PRIORITY_HIGH => __('High'),
                    Inquiry::PRIORITY_URGENT => __('Urgent'),
                ]),
            ])
            ->actions([
                Tables\Actions\Action::make('mark_read')
                    ->label(__('Mark as Read'))
                    ->icon('heroicon-o-check')
                    ->visible(fn (Inquiry $record): bool => ! $record->is_read)
                    ->action(function (Inquiry $record) {
                        $record->markAsRead();
                        Notification::make()->title(__('Marked as read'))->success()->send();
                    }),
                Tables\Actions\Action::make('mark_resolved')
                    ->label(__('Mark as Resolved'))
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn (Inquiry $record): bool => ! $record->is_resolved)
                    ->action(function (Inquiry $record) {
                        $record->markAsResolved();
                        Notification::make()->title(__('Marked as resolved'))->success()->send();
                    }),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInquiries::route('/'),
            'create' => Pages\CreateInquiry::route('/create'),
            'view' => Pages\ViewInquiry::route('/{record}'),
            'edit' => Pages\EditInquiry::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email', 'subject', 'message'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return (string) ($record->subject ?? $record->name);
    }
}
