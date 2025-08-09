<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationGroup = 'Content';

    public static function getNavigationGroup(): ?string
    {
        return __('Content');
    }

    public static function getNavigationLabel(): string
    {
        return __('Posts');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('General'))
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('title')->label(__('Title'))->required()->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', \Str::slug((string) $state))),
                        Forms\Components\TextInput::make('slug')->label(__('Slug'))->maxLength(255)->unique(ignoreRecord: true),
                        Forms\Components\Select::make('created_by')->relationship('user', 'name')->label(__('Author'))->searchable()->preload()->required(),
                        Forms\Components\Textarea::make('description')->label(__('Description'))->rows(6)->columnSpanFull(),
                        Forms\Components\Textarea::make('content')->label(__('Content'))->rows(12)->columnSpanFull(),
                        Forms\Components\Textarea::make('excerpt')->label(__('Excerpt'))->rows(3)->columnSpanFull(),
                    ]),
                Forms\Components\Section::make(__('Publish'))
                    ->columns(4)
                    ->schema([
                        Forms\Components\Toggle::make('is_active')->label(__('Active'))->inline(false)->default(true),
                        Forms\Components\Toggle::make('is_featured')->label(__('Featured'))->inline(false)->default(false),
                        Forms\Components\Toggle::make('is_published')->label(__('Published'))->inline(false)->default(false),
                        Forms\Components\Toggle::make('is_default')->label(__('Default'))->inline(false)->default(false),
                        Forms\Components\DateTimePicker::make('published_at')->label(__('Published At'))->seconds(false),
                    ]),
                Forms\Components\Section::make(__('SEO'))
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('meta_title')->label(__('Meta Title'))->maxLength(255),
                        Forms\Components\TextInput::make('meta_keywords')->label(__('Meta Keywords'))->maxLength(255),
                        Forms\Components\Textarea::make('meta_description')->label(__('Meta Description'))->rows(3)->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->label(__('Title'))->searchable()->sortable(),
                Tables\Columns\TextColumn::make('user.name')->label(__('Author'))->sortable(),
                Tables\Columns\IconColumn::make('is_active')->label(__('Active'))->boolean()->sortable(),
                Tables\Columns\IconColumn::make('is_featured')->label(__('Featured'))->boolean()->sortable()->toggleable(),
                Tables\Columns\IconColumn::make('is_published')->label(__('Published'))->boolean()->sortable(),
                Tables\Columns\TextColumn::make('published_at')->dateTime()->since()->label(__('Published'))->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->since()->label(__('Created'))->sortable()->toggleable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label(__('Active')),
                Tables\Filters\TernaryFilter::make('is_featured')->label(__('Featured')),
                Tables\Filters\TernaryFilter::make('is_published')->label(__('Published')),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'view' => Pages\ViewPost::route('/{record}'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
