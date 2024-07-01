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
use App\Models\TransactionRecurringItem;
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

        if ($selectedPeriod === 'month') {
            $startMonth = Carbon::now()->subMonths(5)->month;
            $endMonth = Carbon::now()->month;

            if ($timeRange === 'last 3 months') {
                $startMonth = Carbon::now()->subMonths(2)->month;
                $endMonth = Carbon::now()->month;
            }

            for ($month = $startMonth; $month <= $endMonth; $month++) {
                $monthName = Carbon::create()?->month($month)->format('F');

                $transactionData = [];
                foreach ($values as $value) {
                    $tagId = self::getSelectedModel($selectedModel, $value);
                    $monthKey = $value->created_at->month;

                    if (isset($transactionData[$tagId][$monthKey])) {
                        $transactionData[$tagId][$monthKey]['amount'] += $value->amount;
                    } else {
                        $transactionData[$tagId][$monthKey] = [
                            'amount' => $value->amount,
                        ];
                    }
                }

                $data[$monthName] = $transactionData;
                $tableValues = $transactionData;

                foreach ($selectedModel as $model) {
                    $tagId = $model->id;
                    $transactions = Transaction::where('tag_id', $tagId)
                        ->whereMonth('created_at', $month)
                        ->sum('amount');

                    if (! isset($sums[$monthName])) {
                        $sums[$monthName] = 0; // Initialize if not already set
                    }

                    $sums[$monthName] += $transactions; // Accumulate the sum for the month
                }

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

                foreach ($selectedModel as $model) {
                    $tagId = $model->id;
                    $transactions = Transaction::where('tag_id', $tagId)
                        ->whereYear('created_at', $year)
                        ->sum('amount');

                    if (! isset($sums[$year])) {
                        $sums[$year] = 0; // Initialize if not already set
                    }

                    $sums[$year] += $transactions; // Accumulate the sum for the month
                }
            }
        } elseif ($selectedPeriod === 'week') {
            $numberOfWeeks = 3;

            if ($timeRange === 'last 6 weeks') {
                $numberOfWeeks = 6;
            }

            $startOfWeek = Carbon::now()->subWeeks($numberOfWeeks)->startOfWeek();
            $endOfWeek = Carbon::now()->endOfWeek();

            $weeks = [];

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
                }

                $data[$weekLabel] = $transactionData;
                $tableValues = $transactionData;

                foreach ($selectedModel as $model) {
                    $tagId = $model->id;
                    $transactions = Transaction::where('tag_id', $tagId)
                        ->whereBetween('created_at', [$week['start'], $week['end']])
                        ->sum('amount');

                    $startFormatted = $week['start']->format('d M');
                    $endFormatted = $week['end']->format('d M');

                    $weekKey = "$startFormatted - $endFormatted";

                    if (! isset($sums[$weekKey])) {
                        $sums[$weekKey] = 0;
                    }

                    $sums[$weekKey] += $transactions;
                }
            }
        } elseif ($selectedPeriod === 'day') {
            $numberOfDays = 6;

            if ($timeRange === 'last 30 days') {
                $numberOfDays = 30;
            }

            $startDate = Carbon::now()->subDays($numberOfDays)->startOfDay();
            $endDate = Carbon::now()->endOfDay();

            for ($day = $startDate; $day <= $endDate; $day->addDay()) {
                $dayLabel = $day->format('d M');

                $transactionData = [];
                foreach ($values as $value) {
                    $tagId = self::getSelectedModel($selectedModel, $value);
                    $dayKey = $value->created_at->day;

                    if (isset($transactionData[$tagId][$dayKey])) {
                        $transactionData[$tagId][$dayKey]['amount'] += $value->amount;
                    } else {
                        $transactionData[$tagId][$dayKey] = [
                            'amount' => $value->amount,
                        ];
                    }

                    $data[$dayLabel] = $transactionData;
                    $tableValues = $transactionData;

                    foreach ($selectedModel as $model) {
                        $tagId = $model->id;
                        $transactions = Transaction::where('tag_id', $tagId)
                            ->whereDate('created_at', $day->format('Y-m-d'))
                            ->sum('amount');

                        $startFormatted = $startDate->format('d M');

                        if (! isset($sums[$startFormatted])) {
                            $sums[$startFormatted] = 0;
                        }

                        $sums[$startFormatted] += $transactions;
                    }
                }
            }

        }

        //       dd($sums);
        $table->content(
            view('livewire.your-table-view', [
                'table' => $test,
                'tagName' => $selectedModel,
                'transactionData' => $values,
                'tableValues' => $tableValues,
                'data' => $data,
                'sums' => $sums,
            ])
        );

        $columns[] = TextColumn::make('count')->getStateUsing(function ($record) {
            $values = $record->getMonthlyData(null, null);

            $transactionsByTag = [];
            $columnNames = '';

            if ($record->getTable() === 'tags') {
                $columnNames = 'tag_id';
            }

            if ($record->getTable() === 'categories') {
                $columnNames = 'category_id';
            }

            if ($record->getTable() === 'accounts') {
                $columnNames = 'accounts';
            }

            if ($record->getTable() === 'recurring_items') {
                $columnNames = 'recurring_items';
            }

            if ($record->getTable() === 'transactions') {
                $columnNames = 'recurring_items';
            }

            foreach ($values as $value) {
                $test = $value->id;

                if ($columnNames === 'accounts') {
                    $transactionsByTag[$value->id] = Transaction::count();

                    return $transactionsByTag[$record->id];
                }

                if ($columnNames === 'recurring_items') {
                    $transactionsByTag[$value->id] = TransactionRecurringItem::where('recurring_item_id', $test)->count();

                    return $transactionsByTag[$value->id];
                }

                if ($columnNames === 'payee') {
                    $test = $value->payee;
                }

                $transactionsByTag[$value->id] = Transaction::where($columnNames, $test)->count();
            }

            return $transactionsByTag[$record->id];
        });

        $columns[] = TextColumn::make('sum avg')->getStateUsing(function ($record) {
            $tags = $record->getMonthlyData(null, null);
            $transactionsByTag = [];

            foreach ($tags as $tag) {
                $transactions = Transaction::where('tag_id', $tag->id)->get();
                $transactionsByTag[$tag->id] = $transactions->avg('amount');
            }

            return $transactionsByTag[$record->id];
        });

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
}
