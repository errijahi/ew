<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AnalyzeResource\Pages;
use App\Models\Account;
use App\Models\Analyze;
use App\Models\Category;
use App\Models\Payee;
use App\Models\RecurringItem;
use App\Models\Tag;
use App\Models\Transaction;
use Carbon\Carbon;
use Filament\Resources\Resource;
use Filament\Tables\Actions\SelectAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AnalyzeResource extends Resource
{
    protected static ?string $model = Analyze::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Finances';

    public static function table(Table $table): Table
    {

        $table->headerActions([
            SelectAction::make('make custom table header filters')
                ->view('livewire.table-filter'),
        ]);

        $selectedModel = Tag::get();
        $Model = session('key');

        if ($Model === 'tag') {
            $selectedModel = Tag::get();
        }
        if ($Model === 'categories') {
            $selectedModel = Category::get();
        }
        if ($Model === 'account') {
            $selectedModel = Account::get();
        }
        if ($Model === 'recurring') {
            $selectedModel = RecurringItem::get();
        }
        if ($Model === 'payee') {
            $selectedModel = Payee::get();
        }

        $selectedPeriod = session('status') ?? 'year';
        $timeRange = session('timeRange') ?? 'last 7 days';
        $currentYear = Carbon::now()->year;
        $startYear = $currentYear - 5;

        $transactionDataByPeriod = [];
        $tableValues = [];
        $transactionValues = Transaction::get();
        $sums = [];
        $averages = [];

         if ($selectedPeriod === 'year') {
            if ($timeRange === 'last 3 years') {
                $startYear = Carbon::now()->year - 2;
            }

            for ($year = $startYear; $year <= $currentYear; $year++) {
                $tableValues = [];
                $searchBy = '';
                foreach ($transactionValues as $transactionValue) {
                    $modelData = self::getSelectedModel($selectedModel, $transactionValue);
                    $modelId = $modelData['ModelValues'];
                    $yearKey = $transactionValue->created_at->year;
                    $searchBy = $modelData['SearchBy'];

                    if (isset($tableValues[$modelId][$yearKey])) {
                        $tableValues[$modelId][$yearKey]['amount'] += $transactionValue->amount;
                    } else {
                        $tableValues[$modelId][$yearKey] = [
                            'amount' => $transactionValue->amount,
                        ];
                    }
                }

                $transactionDataByPeriod[$year] = $tableValues;

                $transactionCalculation = Transaction::calculateTransaction(
                    selectedModel: $selectedModel,
                    searchBy: $searchBy,
                    period: 'year',
                    year: $year
                );

                $sums[$year] = $transactionCalculation['transactionsSum'];
                $averages[$year] = $transactionCalculation['averageAmount'];
            }
        }elseif ($selectedPeriod === 'month') {
             $startDate = Carbon::now()->subMonths(5)->startOfMonth();
             $endDate = Carbon::now()->endOfMonth();

             if ($timeRange === 'last 3 months') {
                 $startDate = Carbon::now()->subMonths(2)->startOfMonth();
                 $endDate = Carbon::now()->endOfMonth();
             }

             $currentDate = $startDate->copy();

             while ($currentDate <= $endDate) {
                 $monthName = $currentDate->format('F');
                 $year = $currentDate->year;
                 $searchBy = '';

                 $tableValues = [];
                 foreach ($transactionValues as $transactionValue) {
                     $modelData = self::getSelectedModel($selectedModel, $transactionValue);
                     $modelId = $modelData['ModelValues'];
                     $searchBy = $modelData['SearchBy'];
                     $monthKey = $transactionValue->created_at->format('F');
                     $yearKey = $transactionValue->created_at->year;

                     if (isset($tableValues[$modelId][$yearKey][$monthKey])) {
                         $tableValues[$modelId][$yearKey][$monthKey]['amount'] += $transactionValue->amount;
                     } else {
                         $tableValues[$modelId][$yearKey][$monthKey] = [
                             'amount' => $transactionValue->amount,
                         ];
                     }
                 }

                 if (! isset($data[$monthName])) {
                     $transactionDataByPeriod[$monthName] = [];
                 }

                 $transactionCalculation = Transaction::calculateTransaction(
                     selectedModel: $selectedModel,
                     searchBy: $searchBy,
                     period: 'month',
                     year: $year,
                     currentDate: $currentDate
                 );

                 $sums[$monthName] = $transactionCalculation['transactionsSum'];
                 $averages[$monthName] = $transactionCalculation['averageAmount'];


                 $currentDate->addMonth();
             }
         } elseif ($selectedPeriod === 'week') {
            $numberOfWeeks = 6;
            if ($timeRange === 'last 4 weeks') {
                $numberOfWeeks = 3;
            }

            $startOfWeek = Carbon::now()->subWeeks($numberOfWeeks)->startOfWeek();
            $endOfWeek = Carbon::now()->endOfWeek();

            $weeks = [];
            while ($startOfWeek->lte($endOfWeek)) {
                $weeks[] = [
                    'start' => $startOfWeek->copy(),
                    'end' => $startOfWeek->copy()->endOfWeek(),
                ];
                $startOfWeek->addWeek();
            }

            foreach ($weeks as $week) {
                $tableValues = [];
                $searchBy = '';

                foreach ($transactionValues as $transactionValue) {
                    $modelData = self::getSelectedModel($selectedModel, $transactionValue);
                    $modelId = $modelData['ModelValues'];
                    $weekKey = $transactionValue->created_at->weekOfYear;
                    $yearKey = $transactionValue->created_at->year;
                    $searchBy = $modelData['SearchBy'];

                    if (isset($tableValues[$modelId][$yearKey][$weekKey])) {
                        $tableValues[$modelId][$yearKey][$weekKey]['amount'] += $transactionValue->amount;
                    } else {
                        $tableValues[$modelId][$yearKey][$weekKey] = [
                            'amount' => $transactionValue->amount,
                        ];
                    }
                }

                $weekLabel = $week['start']->format('d M').' - '.$week['end']->format('d M');
                $transactionDataByPeriod[$weekLabel] = $tableValues;

                $transactionCalculation = Transaction::calculateTransaction(
                    selectedModel: $selectedModel,
                    searchBy: $searchBy,
                    period: 'week',
                    week: $week
                );

                $sums[$weekLabel] = $transactionCalculation['transactionsSum'];
                $averages[$weekLabel] = $transactionCalculation['averageAmount'];
            }

        } elseif ($selectedPeriod === 'day') {
            $numberOfDays = 7;

            if ($timeRange === 'last 30 days') {
                $numberOfDays = 30;
            }

            $startDate = Carbon::now()->subDays($numberOfDays)->startOfDay();
            $endDate = Carbon::now()->endOfDay();

            $days = [];
            while ($startDate->lte($endDate)) {
                $days[] = $startDate->copy();
                $startDate->addDay();
            }

            foreach ($days as $day) {
                $tableValues = [];
                $searchBy = '';

                foreach ($transactionValues as $transactionValue) {
                    $modelData = self::getSelectedModel($selectedModel, $transactionValue);
                    $modelId = $modelData['ModelValues'];
                    $dayKey = $transactionValue->created_at->dayOfMonth;
                    $monthKey = $transactionValue->created_at->month;
                    $yearKey = $transactionValue->created_at->year;
                    $searchBy = $modelData['SearchBy'];

                    if (isset($tableValues[$modelId][$yearKey][$monthKey][$dayKey])) {
                        $tableValues[$modelId][$yearKey][$monthKey][$dayKey]['amount'] += $transactionValue->amount;
                    } else {
                        $tableValues[$modelId][$yearKey][$monthKey][$dayKey] = [
                            'amount' => $transactionValue->amount,
                        ];
                    }
                }

                $dayLabel = $day->format('d M');
                $transactionDataByPeriod[$dayLabel] = $tableValues;

                $transactionCalculation = Transaction::calculateTransaction(
                    selectedModel: $selectedModel,
                    searchBy: $searchBy,
                    period: 'day',
                    day: $day
                );

                $sums[$dayLabel] = $transactionCalculation['transactionsSum'];
                $averages[$dayLabel] = $transactionCalculation['averageAmount'];
            }
        }

        $table->content(
            view('livewire.analyze-table-view', compact(
                'selectedModel',
                'tableValues',
                'transactionDataByPeriod',
                'sums',
                'averages',
            ))
        );

        $columns[] = TextColumn::make('This needs to return something to work, it is here only for that reason.');

        return $table->columns($columns);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAnalyzes::route('/'),
        ];
    }

    public static function getSelectedModel($selectedModel, $value)
    {
        $ModelValues = '';
        $SearchBy = '';

        if ($selectedModel[0]->getTable() === 'tags') {
            $ModelValues = $value->tag_id;
            $SearchBy = 'tag_id';
        }
        if ($selectedModel[0]->getTable() === 'categories') {
            $ModelValues = $value->category_id;
            $SearchBy = 'category_id';
        }
        if ($selectedModel[0]->getTable() === 'accounts') {
            $ModelValues = $value->account->id;
            $SearchBy = 'account_id';
        }
        if ($selectedModel[0]->getTable() === 'recurring_items') {
            $ModelValues = $value->recurringItem->id ?? '0';
            $SearchBy = 'recurring_item_id';
        }
        if ($selectedModel[0]->getTable() === 'payees') {
            $ModelValues = $value->payee->id ?? '0';
            $SearchBy = 'payee_id';
        }

        return ['ModelValues' => $ModelValues, 'SearchBy' => $SearchBy];
    }
}
