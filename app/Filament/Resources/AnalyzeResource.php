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
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Session;

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



       $tagName = Tag::get()->pluck('name');
       $value = session('key');

        if($value === 'tag'){
            $tagName = Tag::get()->pluck('name');
        }
        if($value === 'categories'){
            $tagName = Category::get()->pluck('name');
        }
        if($value === 'account'){
            $tagName = Account::get()->pluck('account_name');
        }
        if($value === 'recurring'){
            $tagName = RecurringItem::get()->pluck('name');
        }
        if($value === 'payee'){
            $tagName = Payee::get()->pluck('name');
        }

       $test = 'test';
       $table->content(
            view('livewire.your-table-view',['table' => $test, 'tagName' => $tagName])
       );




        $selectedPeriod = session('status') ?? 'year';
        $timeRange = session('timeRange') ?? 'last 7 days';

        $currentYear = Carbon::now()->year;
        $startYear = $currentYear - 5;

        if ($selectedPeriod === 'month') {
            $startMonth = Carbon::now()->month - 5;
            $endMonth = Carbon::now()->month;

            if ($timeRange === 'last 3 months') {
                $startMonth = Carbon::now()->month - 2;
                $endMonth = Carbon::now()->month;
            }

            for ($month = $startMonth; $month <= $endMonth; $month++) {

                $monthName = Carbon::create()?->month($month)->format('F');
                $columns[] = TextColumn::make('month_'.$month)
                    ->label($monthName)
                    ->getStateUsing(function ($record) use ($month) {
                        $values = $record->getMonthlyData(null, null);

                        $transactionData = [];
                        foreach ($values as $value) {
                            $tagId = $value->tag_id;
                            $monthKey = $value->created_at->month;

                            if (isset($transactionData[$tagId][$monthKey])) {
                                $transactionData[$tagId][$monthKey]['amount'] += $value->amount;
                            } else {
                                $transactionData[$tagId][$monthKey] = [
                                    'amount' => $value->amount,
                                ];
                            }
                        }

                        return $transactionData[$record->id][$month] ?? null;
                    });
            }
        } elseif ($selectedPeriod === 'year') {

            if ($timeRange === 'last 3 years') {
                $startYear = Carbon::now()->year - 2;
            }

            for ($year = $startYear; $year <= $currentYear; $year++) {
                $columns[] = TextColumn::make('year_'.$year)
                    ->label((string) $year)
                    ->getStateUsing(function ($record) use ($year) {
                        $values = $record->getMonthlyData(null, $year);

                        $transactionData = [];
                        foreach ($values as $value) {
                            $tagId = $value->tag_id;
                            $yearKey = $value->created_at->year;

                            if (isset($transactionData[$tagId][$yearKey])) {
                                $transactionData[$tagId][$yearKey]['amount'] += $value->amount;
                            } else {
                                $transactionData[$tagId][$yearKey] = [
                                    'amount' => $value->amount,
                                ];
                            }
                        }

                        return $transactionData[$record->id][$year] ?? null;
                    });
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

            foreach ($weeks as $index => $week) {
                $weekLabel = $week['start']->format('d M').' - '.$week['end']->format('d M');

                $columns[] = TextColumn::make('week_'.$index)
                    ->label($weekLabel)
                    ->getStateUsing(function ($record) use ($week) {
                        $values = $record->getMonthlyData($week['start'], $week['end']);

                        $transactionData = [];
                        foreach ($values as $value) {
                            $tagId = $value->tag_id;
                            $weekKey = $value->created_at->weekOfYear;

                            if (isset($transactionData[$tagId][$weekKey])) {
                                $transactionData[$tagId][$weekKey]['amount'] += $value->amount;
                            } else {
                                $transactionData[$tagId][$weekKey] = [
                                    'amount' => $value->amount,
                                ];
                            }
                        }

                        return $transactionData[$record->id][$week['start']->weekOfYear] ?? null;
                    });
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

                $columns[] = TextColumn::make('day_'.$day->day)
                    ->label($dayLabel)
                    ->getStateUsing(function ($record) use ($day) {
                        $values = $record->getMonthlyData($day, null);

                        $transactionData = [];
                        foreach ($values as $value) {
                            $tagId = $value->tag_id;
                            $dayKey = $value->created_at->day;

                            if (isset($transactionData[$tagId][$dayKey])) {
                                $transactionData[$tagId][$dayKey]['amount'] += $value->amount;
                            } else {
                                $transactionData[$tagId][$dayKey] = [
                                    'amount' => $value->amount,
                                ];
                            }
                        }

                        return $transactionData[$record->id][$day->day] ?? null;
                    });
            }
        }

        $columns[] = TextColumn::make('total')->getStateUsing(function ($record) {
            $tags = $record->getMonthlyData(null, null);
            $transactionsByTag = [];

            foreach ($tags as $tag) {
                $transactions = Transaction::where('tag_id', $tag->id)->get();
                $transactionsByTag[$tag->id] = $transactions->sum('amount');
            }

            return $transactionsByTag[$record->id];
        });

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
}
