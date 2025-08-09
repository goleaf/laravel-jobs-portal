<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SkillResource\Pages;
use App\Models\Skill;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SkillResource extends Resource
{
    protected static ?string $model = Skill::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('Name'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('description')
                            ->label(__('Description'))
                            ->rows(4)
                            ->maxLength(65535)
                            ->columnSpanFull(),

                        Forms\Components\Select::make('category')
                            ->label(__('Category'))
                            ->options(\App\Models\Skill::CATEGORIES)
                            ->searchable(),

                        Forms\Components\Select::make('level')
                            ->label(__('Level'))
                            ->options(\App\Models\Skill::LEVELS)
                            ->searchable(),

                        Forms\Components\TextInput::make('sort_order')
                            ->label(__('Sort Order'))
                            ->numeric(),

                        Forms\Components\Toggle::make('is_active')
                            ->label(__('Active'))
                            ->inline(false)
                            ->default(true),

                        Forms\Components\Toggle::make('is_default')
                            ->label(__('Default'))
                            ->inline(false),

                        Forms\Components\Toggle::make('is_featured')
                            ->label(__('Featured'))
                            ->inline(false),

                        Forms\Components\Toggle::make('is_technical')
                            ->label(__('Technical'))
                            ->inline(false),
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

                Tables\Columns\TextColumn::make('category')
                    ->label(__('Category'))
                    ->toggleable(),

                Tables\Columns\TextColumn::make('level')
                    ->label(__('Level'))
                    ->toggleable(),

                Tables\Columns\TextColumn::make('description')
                    ->label(__('Description'))
                    ->limit(60)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label(__('Sort'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('Active'))
                    ->boolean()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_default')
                    ->label(__('Default'))
                    ->boolean()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_featured')
                    ->label(__('Featured'))
                    ->boolean()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('is_technical')
                    ->label(__('Technical'))
                    ->boolean()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\TernaryFilter::make('is_active')->label(__('Active')),
                Tables\Filters\TernaryFilter::make('is_featured')->label(__('Featured')),
                Tables\Filters\TernaryFilter::make('is_default')->label(__('Default')),
                Tables\Filters\TernaryFilter::make('is_technical')->label(__('Technical')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
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
            'index' => Pages\ListSkills::route('/'),
            'create' => Pages\CreateSkill::route('/create'),
            'view' => Pages\ViewSkill::route('/{record}'),
            'edit' => Pages\EditSkill::route('/{record}/edit'),
        ];
    }
}


