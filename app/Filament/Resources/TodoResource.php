<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TodoResource\Pages;
use App\Models\Todo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TodoResource extends Resource
{
    protected static ?string $model = Todo::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-circle';

    protected static ?string $navigationGroup = 'Tasks';

    public static function getNavigationGroup(): ?string
    {
        return __('Tasks');
    }

    public static function getNavigationLabel(): string
    {
        return __('Todos');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Details'))
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('user_id')->relationship('user', 'name')->label(__('User'))->searchable()->preload()->required(),
                        Forms\Components\TextInput::make('title')->label(__('Title'))->required()->maxLength(255),
                        Forms\Components\Textarea::make('description')->label(__('Description'))->rows(4)->columnSpanFull(),
                        Forms\Components\Select::make('category')->label(__('Category'))->options([
                            Todo::CATEGORY_PERSONAL => __('Personal'),
                            Todo::CATEGORY_WORK => __('Work'),
                            Todo::CATEGORY_PROJECT => __('Project'),
                            Todo::CATEGORY_MEETING => __('Meeting'),
                            Todo::CATEGORY_REMINDER => __('Reminder'),
                            Todo::CATEGORY_ADMIN => __('Admin'),
                        ])->searchable(),
                        Forms\Components\Select::make('priority')->label(__('Priority'))->options([
                            Todo::PRIORITY_LOW => __('Low'),
                            Todo::PRIORITY_MEDIUM => __('Medium'),
                            Todo::PRIORITY_HIGH => __('High'),
                            Todo::PRIORITY_URGENT => __('Urgent'),
                        ])->required(),
                        Forms\Components\DateTimePicker::make('due_date')->label(__('Due Date'))->seconds(false),
                        Forms\Components\DateTimePicker::make('completed_at')->label(__('Completed At'))->seconds(false),
                        Forms\Components\TextInput::make('estimated_minutes')->label(__('Estimated (min)'))->numeric()->minValue(1),
                        Forms\Components\TextInput::make('actual_minutes')->label(__('Actual (min)'))->numeric()->minValue(1),
                        Forms\Components\TagsInput::make('tags')->label(__('Tags'))->separator(','),
                        Forms\Components\TextInput::make('sort_order')->label(__('Sort Order'))->numeric(),
                        Forms\Components\Toggle::make('is_completed')->label(__('Completed'))->inline(false)->default(false),
                        Forms\Components\Toggle::make('is_active')->label(__('Active'))->inline(false)->default(true),
                        Forms\Components\Toggle::make('is_recurring')->label(__('Recurring'))->inline(false)->default(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->label(__('Title'))->searchable()->sortable(),
                Tables\Columns\TextColumn::make('user.name')->label(__('User'))->sortable(),
                Tables\Columns\TextColumn::make('priority')->badge()->label(__('Priority'))->sortable(),
                Tables\Columns\TextColumn::make('category')->badge()->label(__('Category'))->sortable()->toggleable(),
                Tables\Columns\IconColumn::make('is_completed')->label(__('Completed'))->boolean()->sortable(),
                Tables\Columns\TextColumn::make('due_date')->dateTime()->since()->label(__('Due'))->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->since()->label(__('Created'))->sortable()->toggleable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_completed')->label(__('Completed')),
                Tables\Filters\TernaryFilter::make('is_active')->label(__('Active')),
                Tables\Filters\SelectFilter::make('priority')->options([
                    Todo::PRIORITY_LOW => __('Low'),
                    Todo::PRIORITY_MEDIUM => __('Medium'),
                    Todo::PRIORITY_HIGH => __('High'),
                    Todo::PRIORITY_URGENT => __('Urgent'),
                ]),
                Tables\Filters\SelectFilter::make('category')->options([
                    Todo::CATEGORY_PERSONAL => __('Personal'),
                    Todo::CATEGORY_WORK => __('Work'),
                    Todo::CATEGORY_PROJECT => __('Project'),
                    Todo::CATEGORY_MEETING => __('Meeting'),
                    Todo::CATEGORY_REMINDER => __('Reminder'),
                    Todo::CATEGORY_ADMIN => __('Admin'),
                ]),
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
            'index' => Pages\ListTodos::route('/'),
            'create' => Pages\CreateTodo::route('/create'),
            'view' => Pages\ViewTodo::route('/{record}'),
            'edit' => Pages\EditTodo::route('/{record}/edit'),
        ];
    }
}
