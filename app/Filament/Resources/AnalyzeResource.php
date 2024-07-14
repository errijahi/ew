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

        //        dd($timeRange);

        $transactionDataByPeriod = [];
        $tableValues = [];
        $transactionValues = Transaction::get();
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
                foreach ($transactionValues as $transactionValue) {
                    $modelData = self::getSelectedModel($selectedModel, $transactionValue);
                    $modelId = $modelData['ModelValues'];
                    $searchBy = $modelData['SearchBy'];
                    $monthKey = $transactionValue->created_at->format('F');
                    $yearKey = $transactionValue->created_at->year;

                    if (isset($transactionData[$modelId][$yearKey][$monthKey])) {
                        $transactionData[$modelId][$yearKey][$monthKey]['amount'] += $transactionValue->amount;
                    } else {
                        $transactionData[$modelId][$yearKey][$monthKey] = [
                            'amount' => $transactionValue->amount,
                        ];
                    }
                }

                if (! isset($data[$monthName])) {
                    $transactionDataByPeriod[$monthName] = [];
                }

                $tableValues = $transactionData;
                $totalTransactionsSum = 0;
                $totalTagsCount = 0;

                foreach ($selectedModel as $model) {
                    $modelId = $model->id;

                    $transactionsSum = Transaction::where($searchBy, $modelId)
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
                $currentDate->addMonth();
            }
        } elseif ($selectedPeriod === 'year') {
            if ($timeRange === 'last 3 years') {
                $startYear = Carbon::now()->year - 2;
            }

            for ($year = $startYear; $year <= $currentYear; $year++) {
                $transactionData = [];
                $searchBy = '';
                foreach ($transactionValues as $transactionValue) {
                    $modelData = self::getSelectedModel($selectedModel, $transactionValue);
                    $modelId = $modelData['ModelValues'];
                    $yearKey = $transactionValue->created_at->year;
                    $searchBy = $modelData['SearchBy'];

                    if (isset($transactionData[$modelId][$yearKey])) {
                        $transactionData[$modelId][$yearKey]['amount'] += $transactionValue->amount;
                    } else {
                        $transactionData[$modelId][$yearKey] = [
                            'amount' => $transactionValue->amount,
                        ];
                    }
                }

                $transactionDataByPeriod[$year] = $transactionData;
                $tableValues = $transactionData;

                $totalTransactionsSum = 0;
                $totalTagsCount = 0;

                foreach ($selectedModel as $model) {
                    $modelId = $model->id;

                    $transactionsSum = Transaction::where($searchBy, $modelId)
                        ->whereYear('created_at', $year)
                        ->sum('amount');

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
                $transactionData = [];
                $searchBy = '';

                foreach ($transactionValues as $transactionValue) {
                    $modelData = self::getSelectedModel($selectedModel, $transactionValue);
                    $modelId = $modelData['ModelValues'];
                    $weekKey = $transactionValue->created_at->weekOfYear;
                    $yearKey = $transactionValue->created_at->year;
                    $searchBy = $modelData['SearchBy'];

                    if (isset($transactionData[$modelId][$yearKey][$weekKey])) {
                        $transactionData[$modelId][$yearKey][$weekKey]['amount'] += $transactionValue->amount;
                    } else {
                        $transactionData[$modelId][$yearKey][$weekKey] = [
                            'amount' => $transactionValue->amount,
                        ];
                    }
                }

                $weekLabel = $week['start']->format('d M').' - '.$week['end']->format('d M');
                $transactionDataByPeriod[$weekLabel] = $transactionData;
                $tableValues = $transactionData;

                $totalTransactionsSum = 0;
                $totalTagsCount = 0;

                foreach ($selectedModel as $model) {
                    $modelId = $model->id;

                    $transactionsSum = Transaction::where($searchBy, $modelId)
                        ->whereBetween('created_at', [$week['start'], $week['end']])
                        ->sum('amount');

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

            $days = [];
            while ($startDate->lte($endDate)) {
                $days[] = $startDate->copy();
                $startDate->addDay();
            }

            foreach ($days as $day) {
                $transactionData = [];
                $searchBy = '';

                foreach ($transactionValues as $transactionValue) {
                    $modelData = self::getSelectedModel($selectedModel, $transactionValue);
                    $modelId = $modelData['ModelValues'];
                    $dayKey = $transactionValue->created_at->dayOfMonth;
                    $monthKey = $transactionValue->created_at->month;
                    $yearKey = $transactionValue->created_at->year;
                    $searchBy = $modelData['SearchBy'];

                    if (isset($transactionData[$modelId][$yearKey][$monthKey][$dayKey])) {
                        $transactionData[$modelId][$yearKey][$monthKey][$dayKey]['amount'] += $transactionValue->amount;
                    } else {
                        $transactionData[$modelId][$yearKey][$monthKey][$dayKey] = [
                            'amount' => $transactionValue->amount,
                        ];
                    }
                }

                $dayLabel = $day->format('d M');
                $transactionDataByPeriod[$dayLabel] = $transactionData;
                $tableValues = $transactionData;

                $totalTransactionsSum = 0;
                $totalTagsCount = 0;

                foreach ($selectedModel as $model) {
                    $modelId = $model->id;

                    $transactionsSum = Transaction::where($searchBy, $modelId)
                        ->whereDate('created_at', $day)
                        ->sum('amount');

                    if ($transactionsSum > 0) {
                        $totalTransactionsSum += $transactionsSum;
                        $totalTagsCount++;
                    }
                }

                if (! isset($sums[$dayLabel])) {
                    $sums[$dayLabel] = 0;
                }
                $sums[$dayLabel] += $totalTransactionsSum;

                if ($totalTagsCount > 0) {
                    $averageAmount = $totalTransactionsSum / $totalTagsCount;
                } else {
                    $averageAmount = 0;
                }

                $averages[$dayLabel] = $averageAmount;
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
