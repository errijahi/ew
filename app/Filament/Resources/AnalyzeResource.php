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
        $transactionValues = Transaction::get();
        $sums = [];
        $averages = [];
        $tableValues = '';

        if ($selectedPeriod === 'year') {
            if ($timeRange === 'last 3 years') {
                $startYear = Carbon::now()->year - 2;
            }

            for ($year = $startYear; $year <= $currentYear; $year++) {
                $aggregatedTransactionValues = Transaction::aggregateTransactionValues($transactionValues, $selectedModel, 'year');
                $tableValues = $aggregatedTransactionValues['tableValues'];
                $searchBy = $aggregatedTransactionValues['searchBy'];
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
        } elseif ($selectedPeriod === 'month') {
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

                $aggregatedTransactionValues = Transaction::aggregateTransactionValues($transactionValues, $selectedModel, 'month');
                $tableValues = $aggregatedTransactionValues['tableValues'];
                $searchBy = $aggregatedTransactionValues['searchBy'];
                $transactionDataByPeriod[$monthName] = $tableValues;

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
                $aggregatedTransactionValues = Transaction::aggregateTransactionValues($transactionValues, $selectedModel, 'week');
                $tableValues = $aggregatedTransactionValues['tableValues'];
                $searchBy = $aggregatedTransactionValues['searchBy'];
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
                $aggregatedTransactionValues = Transaction::aggregateTransactionValues($transactionValues, $selectedModel, 'day');
                $tableValues = $aggregatedTransactionValues['tableValues'];
                $searchBy = $aggregatedTransactionValues['searchBy'];
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
}
