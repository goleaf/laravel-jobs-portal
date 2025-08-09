<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InquiryResource\Pages;
use App\Models\Inquiry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

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
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('subject')->limit(40)->searchable()->sortable(),
                Tables\Columns\TextColumn::make('priority')->badge()->sortable(),
                Tables\Columns\TextColumn::make('status')->badge()->sortable(),
                Tables\Columns\TextColumn::make('category')->badge()->sortable()->toggleable(),
                Tables\Columns\IconColumn::make('is_read')->label(__('Read'))->boolean()->sortable()->toggleable(),
                Tables\Columns\IconColumn::make('is_resolved')->label(__('Resolved'))->boolean()->sortable(),
                Tables\Columns\TextColumn::make('assignedUser.name')->label(__('Assigned'))->toggleable(),
                Tables\Columns\TextColumn::make('created_at')->label(__('Created'))->since()->sortable(),
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
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('mark_read')
                    ->label(__('Mark Read'))
                    ->icon('heroicon-o-envelope-open')
                    ->visible(fn (Inquiry $record) => ! $record->is_read)
                    ->action(function (Inquiry $record) {
                        $record->markAsRead();
                        \Filament\Notifications\Notification::make()->title(__('Inquiry marked as read'))->success()->send();
                    }),
                Tables\Actions\Action::make('mark_unread')
                    ->label(__('Mark Unread'))
                    ->icon('heroicon-o-envelope')
                    ->visible(fn (Inquiry $record) => $record->is_read)
                    ->action(function (Inquiry $record) {
                        $record->markAsUnread();
                        \Filament\Notifications\Notification::make()->title(__('Inquiry marked as unread'))->success()->send();
                    }),
                Tables\Actions\Action::make('resolve')
                    ->label(__('Resolve'))
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn (Inquiry $record) => ! $record->is_resolved)
                    ->requiresConfirmation()
                    ->action(function (Inquiry $record) {
                        $record->markAsResolved();
                        \Filament\Notifications\Notification::make()->title(__('Inquiry resolved'))->success()->send();
                    }),
                Tables\Actions\Action::make('assign_to_me')
                    ->label(__('Assign to me'))
                    ->icon('heroicon-o-user-plus')
                    ->visible(fn (Inquiry $record) => auth()->check() && ($record->assigned_to !== auth()->id()))
                    ->action(function (Inquiry $record) {
                        if (auth()->check()) {
                            $record->assignTo(auth()->id());
                            \Filament\Notifications\Notification::make()->title(__('Inquiry assigned to you'))->success()->send();
                        }
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulk_mark_read')
                        ->label(__('Mark Read'))
                        ->icon('heroicon-o-envelope-open')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->markAsRead();
                            }
                        }),
                    Tables\Actions\BulkAction::make('bulk_mark_unread')
                        ->label(__('Mark Unread'))
                        ->icon('heroicon-o-envelope')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->markAsUnread();
                            }
                        }),
                    Tables\Actions\BulkAction::make('bulk_resolve')
                        ->label(__('Resolve'))
                        ->color('success')
                        ->icon('heroicon-o-check-circle')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->markAsResolved();
                            }
                        }),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
}
