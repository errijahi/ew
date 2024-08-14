<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BudgetResource\Pages;
use App\Models\Budget;
use App\Models\Category;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Table;

class BudgetResource extends Resource
{
    protected static ?string $model = Budget::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Finances';

    public static function form(Form $form): Form
    {
        $teamId = auth()->user()->teams[0]->id;
        $categories = Category::where('team_id', $teamId)
            ->pluck('name', 'id')
            ->toArray();

        $budgetCategoryIds = Budget::pluck('category_id')->toArray();
        $filteredCategories = array_filter(
            $categories,
            fn ($name, $id) => ! in_array($id, $budgetCategoryIds),
            ARRAY_FILTER_USE_BOTH
        );

        return $form
            ->schema([
                Select::make('category_id')
                    ->options($filteredCategories)
                    ->native(false)
                    ->required(),
                TextInput::make('budget')->numeric()->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category.name'),
                TextInputColumn::make('budget')->label("This period's budget"),
                TextColumn::make("this period's total"),
                TextColumn::make('difference'),
                TextColumn::make("last period's budget"),
                TextColumn::make("last period's total"),
                TextColumn::make('difference'),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBudgets::route('/'),
            'create' => Pages\CreateBudget::route('/create'),
            'edit' => Pages\EditBudget::route('/{record}/edit'),
        ];
    }
}
