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

        $test = 'test';

        $selectedPeriod = session('status') ?? 'year';
        $timeRange = session('timeRange') ?? 'last 7 days';

        $currentYear = Carbon::now()->year;
        $startYear = $currentYear - 5;

        $data = [];
        $tableValues = [];
        $values = Transaction::get();
        $sums = [];
        $averages = [];

        if ($selectedPeriod === 'month') {
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

                $transactionData = [];
                foreach ($values as $value) {
                    $tagId = self::getSelectedModel($selectedModel, $value);
                    $monthKey = $value->created_at->format('F');
                    $yearKey = $value->created_at->year;

                    if (isset($transactionData[$tagId][$yearKey][$monthKey])) {
                        $transactionData[$tagId][$yearKey][$monthKey]['amount'] += $value->amount;
                    } else {
                        $transactionData[$tagId][$yearKey][$monthKey] = [
                            'amount' => $value->amount,
                        ];
                    }
                }

                // Aggregate the data into a common key without year
                if (! isset($data[$monthName])) {
                    $data[$monthName] = [];
                }

                $tableValues = $transactionData;
                $totalTransactionsSum = 0;
                $totalTagsCount = 0;

                foreach ($selectedModel as $model) {
                    $tagId = $model->id;

                    $transactionsSum = Transaction::where('tag_id', $tagId)
                        ->whereYear('created_at', $year)
                        ->whereMonth('created_at', $currentDate->month)
                        ->sum('amount');

                    if ($transactionsSum > 0) {
                        $totalTransactionsSum += $transactionsSum;
                        $totalTagsCount++;
                    }
                }

                if (! isset($sums[$monthName])) {
                    $sums[$monthName] = 0;
                }
                $sums[$monthName] += $totalTransactionsSum;

                if ($totalTagsCount > 0) {
                    $averageAmount = $totalTransactionsSum / $totalTagsCount;
                } else {
                    $averageAmount = 0;
                }

                $averages[$monthName] = $averageAmount;

                // Move to the next month
                $currentDate->addMonth();
            }
        } elseif ($selectedPeriod === 'year') {
            if ($timeRange === 'last 3 years') {
                $startYear = Carbon::now()->year - 2;
            }

            for ($year = $startYear; $year <= $currentYear; $year++) {
                $transactionData = [];
                foreach ($values as $value) {
                    $tagId = self::getSelectedModel($selectedModel, $value);
                    $yearKey = $value->created_at->year;

                    if (isset($transactionData[$tagId][$yearKey])) {
                        $transactionData[$tagId][$yearKey]['amount'] += $value->amount;
                    } else {
                        $transactionData[$tagId][$yearKey] = [
                            'amount' => $value->amount,
                        ];
                    }
                }

                $data[$year] = $transactionData;
                $tableValues = $transactionData;

                $totalTransactionsSum = 0;
                $totalTagsCount = 0;

                foreach ($selectedModel as $model) {
                    $tagId = $model->id;

                    $transactionsSum = Transaction::where('tag_id', $tagId)
                        ->whereYear('created_at', $year)
                        ->sum('amount');

                    $transactionsCount = Transaction::where('tag_id', $tagId)
                        ->whereYear('created_at', $year)
                        ->where('amount', '>', 0)
                        ->count();

                    if ($transactionsSum > 0) {
                        $totalTransactionsSum += $transactionsSum;

                        $totalTagsCount++;
                    }
                }

                if (! isset($sums[$year])) {
                    $sums[$year] = 0;
                }
                $sums[$year] += $totalTransactionsSum;

                if ($totalTagsCount > 0) {
                    $averageAmount = $totalTransactionsSum / $totalTagsCount;
                } else {
                    $averageAmount = 0;
                }

                $averages[$year] = $averageAmount;
            }
        } elseif ($selectedPeriod === 'week') {
            $numberOfWeeks = 3;

            if ($timeRange === 'last 6 weeks') {
                $numberOfWeeks = 6;
            }

            $startOfWeek = Carbon::now()->subWeeks($numberOfWeeks)->startOfWeek();
            $endOfWeek = Carbon::now()->endOfWeek();

            $weeks = [];
            $data = [];
            $sums = [];
            $averages = [];

            while ($startOfWeek->lte($endOfWeek)) {
                $weekEnd = $startOfWeek->copy()->endOfWeek();
                $weeks[] = [
                    'start' => $startOfWeek->copy(),
                    'end' => $weekEnd,
                ];
                $startOfWeek->addWeek();
            }

            foreach ($weeks as $week) {
                $weekLabel = $week['start']->format('d M').' - '.$week['end']->format('d M');

                $transactionData = [];
                $totalTransactionsSum = 0;
                $totalTagsCount = 0;

                foreach ($values as $value) {
                    $tagId = self::getSelectedModel($selectedModel, $value);
                    $weekKey = $value->created_at->weekOfYear;

                    if (isset($transactionData[$tagId][$weekKey])) {
                        $transactionData[$tagId][$weekKey]['amount'] += $value->amount;
                    } else {
                        $transactionData[$tagId][$weekKey] = [
                            'amount' => $value->amount,
                        ];
                    }

                    $data[$weekLabel] = $transactionData;
                    $tableValues = $transactionData;
                }

                foreach ($selectedModel as $model) {
                    $tagId = $model->id;

                    $transactionsSum = Transaction::where('tag_id', $tagId)
                        ->whereBetween('created_at', [$week['start'], $week['end']])
                        ->sum('amount');

                    // Fetch transactions with amount > 0 for the current tag and week
                    $transactionsCount = Transaction::where('tag_id', $tagId)
                        ->whereBetween('created_at', [$week['start'], $week['end']])
                        ->where('amount', '>', 0)
                        ->count();

                    if ($transactionsSum > 0) {
                        $totalTransactionsSum += $transactionsSum;
                        $totalTagsCount++;
                    }
                }

                if (! isset($sums[$weekLabel])) {
                    $sums[$weekLabel] = 0;
                }
                $sums[$weekLabel] += $totalTransactionsSum;

                if ($totalTagsCount > 0) {
                    $averageAmount = $totalTransactionsSum / $totalTagsCount;
                } else {
                    $averageAmount = 0;
                }

                $averages[$weekLabel] = $averageAmount;
            }
        } elseif ($selectedPeriod === 'day') {
            $numberOfDays = 7;

            if ($timeRange === 'last 30 days') {
                $numberOfDays = 30;
            }

            $startDate = Carbon::now()->subDays($numberOfDays)->startOfDay();
            $endDate = Carbon::now()->endOfDay();

            for ($day = $startDate; $day <= $endDate; $day->addDay()) {
                $dayLabel = $day->format('d M');

                $totalTransactionsSum = 0;
                $totalTagsCount = 0;

                $startDate = Carbon::now()->subDays($numberOfDays);
                $endDate = Carbon::now();
                $transactionData = self::getTransactionData($selectedModel, $startDate, $endDate);

                $data[$dayLabel] = $transactionData;
                $tableValues = $transactionData;

                foreach ($selectedModel as $model) {
                    $tagId = $model->id;

                    $transactionsSum = Transaction::where('tag_id', $tagId)
                        ->whereDate('created_at', $day)
                        ->sum('amount');

                    // Fetch transactions with amount > 0
                    $transactionsCount = Transaction::where('tag_id', $tagId)
                        ->whereDate('created_at', $day)
                        ->where('amount', '>', 0)
                        ->count();

                    if ($transactionsSum > 0) {
                        $totalTransactionsSum += $transactionsSum;

                        $totalTagsCount++;
                    }
                }

                if (! isset($sums[$dayLabel])) {
                    $sums[$dayLabel] = 0;
                }
                $sums[$dayLabel] += $totalTransactionsSum;
//                dd($totalTransactionsSum);

                if ($totalTagsCount > 0) {
                    $averageAmount = $totalTransactionsSum / $totalTagsCount;
                } else {
                    $averageAmount = 0;
                }

                $averages[$dayLabel] = $averageAmount;
            }
        }

//        dd($sums);
        $table->content(
            view('livewire.your-table-view', [
                'table' => $test,
                'tagName' => $selectedModel,
                'transactionData' => $values,
                'tableValues' => $tableValues,
                'data' => $data,
                'sums' => $sums,
                'averages' => $averages,
            ])
        );

        $columns[] = TextColumn::make('table return placeholder');

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

        if ($selectedModel[0]->getTable() === 'tags') {
            $ModelValues = $value->tag_id;
        }
        if ($selectedModel[0]->getTable() === 'categories') {
            $ModelValues = $value->category_id;
        }
        if ($selectedModel[0]->getTable() === 'accounts') {
            $ModelValues = $value->team->accounts[0]->id;
        }
        if ($selectedModel[0]->getTable() === 'recurring_items') {
            $ModelValues = $value->recurringItem[0]->id ?? '0';
        }
        if ($selectedModel[0]->getTable() === 'payees') {
            $ModelValues = $value->payee[0]->id ?? '0';
        }

        return $ModelValues;
    }

    public static function getTransactionData($selectedModel, $startDate, $endDate)
    {
        $transactionData = Transaction::whereBetween('created_at', [$startDate, $endDate])->get();
        $transactionAmounts = [];

        foreach ($transactionData as $value) {
            $yearKey = $value->created_at->year;
            $monthKey = $value->created_at->format('M');
            $dayKey = $value->created_at->day;

            $tagId = self::getSelectedModel($selectedModel, $value);

            if (isset($transactionAmounts[$tagId][$yearKey][$monthKey][$dayKey])) {
                $transactionAmounts[$tagId][$yearKey][$monthKey][$dayKey]['amount'] += $value->amount;
            } else {
                $transactionAmounts[$tagId][$yearKey][$monthKey][$dayKey] = [
                    'amount' => $value->amount,
                ];
            }
        }

        return $transactionAmounts;
    }
}

// It seems that all of these errors are connected to the pivot tables and db
// accounts has bug  in days, weeks, months, years does not show correct data with footer
// recurring has bug  in days, weeks, months, years does not show correct data with footer
// payee has bug  in days, weeks, months, years does not show correct data with footer

