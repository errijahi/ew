<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BudgetResource\Pages;
use App\Models\Budget;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BudgetResource extends Resource
{
    protected static ?string $model = Budget::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Finances';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category.name'),
                TextInputColumn::make('budget')->label("This period's budget"),
                TextColumn::make("this period's total")->placeholder('---'),
                TextColumn::make('difference')->placeholder('---'),
                TextColumn::make("last period's budget")->placeholder('---')
                    ->extraAttributes([
                        'style' => 'border-left: 2px solid black;',
                    ]),
                TextColumn::make("last period's total")->placeholder('---'),
                TextColumn::make('difference2')->label('Difference')->placeholder('---'),
            ])
            ->filters([
                SelectFilter::make('year')
                    ->label('Year')
                    ->options([
                        '2023' => '2023',
                        '2024' => '2024',
                        '2025' => '2025',
                    ]),
                SelectFilter::make('month')
                    ->label('Month')
                    ->options([
                        '1' => 'January',
                        '2' => 'February',
                        '3' => 'March',
                        '4' => 'April',
                        '5' => 'May',
                        '6' => 'June',
                        '7' => 'July',
                        '8' => 'August',
                        '9' => 'September',
                        '10' => 'October',
                        '11' => 'November',
                        '12' => 'December',
                    ]),
            ])
            ->bulkActions([
                //                Tables\Actions\BulkActionGroup::make([
                //                    Tables\Actions\DeleteBulkAction::make(),
                //                ]),
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
        ];
    }
}
