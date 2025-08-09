<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsLetterResource\Pages;
use App\Models\NewsLetter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class NewsLetterResource extends Resource
{
    protected static ?string $model = NewsLetter::class;

    protected static ?string $navigationIcon = 'heroicon-o-at-symbol';

    protected static ?string $navigationGroup = 'System';

    public static function getNavigationGroup(): ?string
    {
        return __('System');
    }

    public static function getNavigationLabel(): string
    {
        return __('Newsletter Subscribers');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Subscriber'))
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('email')->email()->required()->maxLength(255),
                        Forms\Components\TextInput::make('name')->maxLength(255),
                        Forms\Components\Select::make('source')->options([
                            NewsLetter::SOURCE_WEBSITE => __('Website'),
                            NewsLetter::SOURCE_POPUP => __('Popup'),
                            NewsLetter::SOURCE_FOOTER => __('Footer'),
                            NewsLetter::SOURCE_LANDING_PAGE => __('Landing Page'),
                            NewsLetter::SOURCE_SOCIAL_MEDIA => __('Social Media'),
                            NewsLetter::SOURCE_MANUAL => __('Manual'),
                            NewsLetter::SOURCE_IMPORT => __('Import'),
                        ])->searchable(),
                        Forms\Components\Toggle::make('is_active')->inline(false)->default(true),
                        Forms\Components\Toggle::make('is_verified')->inline(false)->default(false),
                    ]),
                Forms\Components\Section::make(__('Status'))
                    ->columns(3)
                    ->schema([
                        Forms\Components\DateTimePicker::make('subscribed_at')->seconds(false),
                        Forms\Components\DateTimePicker::make('unsubscribed_at')->seconds(false),
                        Forms\Components\DateTimePicker::make('verified_at')->seconds(false),
                        Forms\Components\DateTimePicker::make('last_email_sent_at')->seconds(false),
                        Forms\Components\TextInput::make('emails_sent_count')->numeric()->minValue(0),
                    ]),
                Forms\Components\Section::make(__('Advanced'))
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('verification_token')->maxLength(255),
                        Forms\Components\TextInput::make('unsubscribe_token')->maxLength(255),
                        Forms\Components\KeyValue::make('preferences')->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('email')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('source')->badge()->sortable()->toggleable(),
                Tables\Columns\IconColumn::make('is_active')->label(__('Active'))->boolean()->sortable(),
                Tables\Columns\IconColumn::make('is_verified')->label(__('Verified'))->boolean()->sortable(),
                Tables\Columns\TextColumn::make('subscribed_at')->dateTime()->since()->label(__('Subscribed'))->sortable(),
                Tables\Columns\TextColumn::make('unsubscribed_at')->dateTime()->since()->label(__('Unsubscribed'))->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('emails_sent_count')->numeric()->label(__('Emails Sent'))->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->since()->sortable()->toggleable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label(__('Active')),
                Tables\Filters\TernaryFilter::make('is_verified')->label(__('Verified')),
                Tables\Filters\Filter::make('subscribed')
                    ->label(__('Subscribed'))
                    ->query(fn ($q) => $q->whereNotNull('subscribed_at')->whereNull('unsubscribed_at')),
                Tables\Filters\Filter::make('unsubscribed')
                    ->label(__('Unsubscribed'))
                    ->query(fn ($q) => $q->whereNotNull('unsubscribed_at')),
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
            'index' => Pages\ManageNewsLetters::route('/'),
        ];
    }
}
