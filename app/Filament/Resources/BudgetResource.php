<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BudgetResource\Pages;
use App\Models\Category;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BudgetResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Finances';

    protected static ?string $label = 'Budget';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('year'),
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
                    ])
                    ->default('2024')
                    ->selectablePlaceholder(false)
                    ->indicateUsing(function (): ?string {
                        return null;
                    })
                    ->query(function (Builder $query): Builder {
                        return $query;
                    }),
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
