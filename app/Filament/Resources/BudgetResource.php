<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BudgetResource\Pages;
use App\Models\Budget;
use App\Models\Category;
use Carbon\Carbon;
use Exception;
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

    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextInputColumn::make('budget')->label("This period's budget")->rules(['numeric'])
                    ->beforeStateUpdated(function ($state, Category $record) {

                        $year = session('selected_year');
                        $month = session('selected_month');

                        $attributes = [
                            'year' => $year,
                            'month' => $month,
                            'category_id' => $record->id,
                            'team_id' => $record->team_id,
                        ];

                        Budget::updateOrCreate(
                            $attributes,
                            ['budget' => $state]
                        );
                    })->updateStateUsing(function (&$state) {
                        $state = null;
                    }),
                TextColumn::make('transactions_sum_amount')->sum([
                    'transactions' => fn (Builder $query) => $query->whereBetween('created_at',
                        [Carbon::create(session('selected_year'), session('selected_month'), 1)?->startOfDay(),
                            Carbon::create(session('selected_year'), session('selected_month'), 1)?->endOfMonth()->endOfDay()]),
                ], 'amount')->label("This period's total")->placeholder('---'),
                TextColumn::make('Difference')->state('This state must be here for formatStateUsing to work')
                    ->formatStateUsing(function ($record) {

                        $year = session('selected_year');
                        $month = session('selected_month');

                        $budget = Budget::where('category_id', $record->id)
                            ->where('team_id', $record->team_id)
                            ->where('year', $year)
                            ->where('month', $month)
                            ->value('budget');

                        return number_format(($budget - $record->transactions_sum_amount), 2);
                    })->placeholder('---'),
                TextColumn::make("last period's budget")->state('This state must be here for formatStateUsing to work')
                    ->formatStateUsing(function ($record) {
                        $year = session('selected_year');
                        $month = session('selected_month') - 1;

                        if ($month < 1) {
                            $year--;
                            $month = 12;
                        }

                        $budget = Budget::where('category_id', $record->id)
                            ->where('team_id', $record->team_id)
                            ->where('year', $year)
                            ->where('month', $month)
                            ->value('budget');

                        return number_format($budget, 2);
                    })
                    ->placeholder('---')
                    ->extraAttributes([
                        'style' => 'border-left: 2px solid black;',
                    ]),
                TextColumn::make("last period's total")
                //                    ->sum([
//                        'transactions' => fn (Builder $query) => $query->whereBetween('created_at', [
//                            // Determine the previous month and handle year changes
//                            Carbon::create(session('selected_year'), session('selected_month'), 1)
//                                ?->subMonth()->startOfMonth()->startOfDay(),
//                            Carbon::create(session('selected_year'), session('selected_month'), 1)
//                                ?->subMonth()->endOfMonth()->endOfDay()
//                        ]),
//                    ], 'amount')
                    ->placeholder('---'),
                TextColumn::make('difference2')->label('Difference')->placeholder('---'),
            ])
            ->filters([
                SelectFilter::make('year')
                    ->label('Year')
                    ->options(function () {
                        $currentYear = date('Y');
                        $startYear = '2000';
                        //This part needs to stay like this $currentYear + '10' cos using . will cause big performance issues
                        $years = range($startYear, $currentYear + '10');

                        return array_combine($years, $years);
                    })
                    ->default(date('Y'))
                    ->selectablePlaceholder(false)
                    ->indicateUsing(function (): ?string {
                        return null;
                    })
                    ->query(function (Builder $query): Builder {
                        return $query;
                    }),
                SelectFilter::make('month')
                    ->label('Month')
                    ->options(function () {
                        // Define all months
                        return [
                            '01' => 'January',
                            '02' => 'February',
                            '03' => 'March',
                            '04' => 'April',
                            '05' => 'May',
                            '06' => 'June',
                            '07' => 'July',
                            '08' => 'August',
                            '09' => 'September',
                            '10' => 'October',
                            '11' => 'November',
                            '12' => 'December',
                        ];
                    })
                    ->default(date('m'))
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
