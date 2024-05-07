<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Setup';

    public static function form(Form $form): Form
    {
        $teamId = auth()->user()->teams[0]->id;

        return $form
            ->schema([
                TextInput::make('name'),
                TextInput::make('description'),
                TextInput::make('budget'),
                Select::make('sub_category_group_id')
                    ->options(Category::where('team_id', $teamId)->pluck('name', 'id')->toArray())
                    ->native(false),
                Toggle::make('treat_as_income'),
                Toggle::make('exclude_from_budget'),
                Toggle::make('exclude_from_total'),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('description'),
                IconColumn::make('treat_as_income')
                    ->boolean(),
                IconColumn::make('exclude_from_budget')
                    ->boolean(),
                IconColumn::make('exclude_from_total')
                    ->boolean(),
                TextColumn::make('budget'),
                TextColumn::make('parentCategoryGroup.name')
                    ->label('Category group'),
            ])
            ->groups([
                Group::make('parentCategoryGroup.name')
                    ->collapsible(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
