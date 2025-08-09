<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmailTemplateResource\Pages;
use App\Models\EmailTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EmailTemplateResource extends Resource
{
    protected static ?string $model = EmailTemplate::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationGroup = 'System';

    public static function getNavigationGroup(): ?string
    {
        return __('System');
    }

    public static function getNavigationLabel(): string
    {
        return __('Email Templates');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Details'))
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('template_name')->label(__('Name'))->required()->maxLength(255)->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('subject')->required()->maxLength(255),
                        Forms\Components\Select::make('category')->options(EmailTemplate::CATEGORIES)->label(__('Category'))->searchable(),
                        Forms\Components\TextInput::make('preview_text')->label(__('Preview Text'))->maxLength(255),
                        Forms\Components\Textarea::make('description')->label(__('Description'))->columnSpanFull(),
                        Forms\Components\Toggle::make('is_active')->label(__('Active'))->inline(false)->default(true),
                        Forms\Components\Toggle::make('is_default')->label(__('Default'))->inline(false)->default(false),
                        Forms\Components\Toggle::make('is_system')->label(__('System'))->inline(false)->default(false),
                    ]),
                Forms\Components\Section::make(__('Content'))
                    ->columns(1)
                    ->schema([
                        Forms\Components\Textarea::make('body')->label(__('Body'))->rows(12)->required()->columnSpanFull(),
                        Forms\Components\KeyValue::make('placeholders')->label(__('Placeholders'))->addButtonLabel(__('Add placeholder'))->columnSpanFull(),
                        Forms\Components\Textarea::make('variables')->label(__('Variables (raw)'))->rows(3)->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('template_name')->label(__('Name'))->searchable()->sortable(),
                Tables\Columns\TextColumn::make('subject')->searchable()->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('category')->label(__('Category'))->toggleable(),
                Tables\Columns\IconColumn::make('is_active')->label(__('Active'))->boolean()->sortable(),
                Tables\Columns\IconColumn::make('is_default')->label(__('Default'))->boolean()->sortable()->toggleable(),
                Tables\Columns\IconColumn::make('is_system')->label(__('System'))->boolean()->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('created_at')->label(__('Created'))->dateTime()->since()->sortable()->toggleable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label(__('Active')),
                Tables\Filters\TernaryFilter::make('is_default')->label(__('Default')),
                Tables\Filters\TernaryFilter::make('is_system')->label(__('System')),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmailTemplates::route('/'),
            'create' => Pages\CreateEmailTemplate::route('/create'),
            'view' => Pages\ViewEmailTemplate::route('/{record}'),
            'edit' => Pages\EditEmailTemplate::route('/{record}/edit'),
        ];
    }
}
