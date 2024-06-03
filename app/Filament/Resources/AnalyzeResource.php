<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AnalyzeResource\Pages;
use App\Models\Analyze;
use Carbon\Carbon;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Tables\Filters\SelectFilter;

class AnalyzeResource extends Resource
{
    protected static ?string $model = Analyze::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Finances';

//    You can pass in type value month or year
    public static function table(Table $table, $type = 'week'): Table
    {
        $columns = [
            TextColumn::make('name')
                ->getStateUsing(function ($record) {
                    return $record->name ?? ($record->account_name ?? $record->payee);
                }),
        ];

        // Adding a dynamic filter
        $filters = [
            SelectFilter::make('period')
                ->options([
                    'year' => 'Year',
                    'month' => 'Month',
                    'week' => 'Week',
                    'day' => 'Day',
                ])
                ->query(function ($query) {
                    // Prevent the filter from affecting the SQL query
//                    dd($query);

                    return $query;
                })
        ];

        // Determine the selected period
//        $livewire->getTableFilterState('period')['value']
        $selectedPeriod = request('tableFilters')['period']['value'] ?? 'year';


        $currentYear = Carbon::now()->year;
        $startYear = $currentYear - 5; // Start from 6 years ago, It shows last 6 years, should be dynamic.
        $lastMonth = Carbon::now()->subMonth();
        $daysInLastMonth = $lastMonth->daysInMonth;

        if ($type === 'month') {
            for ($month = 1; $month <= 12; $month++) {
                $monthName = Carbon::create()->month($month)->format('F');

                $columns[] = TextColumn::make('month_'.$month)
                    ->label($monthName)
                    ->getStateUsing(function ($record) use ($month, $currentYear) {
                        $values = $record->getMonthlyData($month, $currentYear);

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
        } elseif ($type === 'year') {
            for ($year = $startYear; $year <= $currentYear; $year++) {
                $columns[] = TextColumn::make('year_'.$year)
                    ->label((string)$year)
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
        } elseif ($type === 'week') {
            $firstDayOfMonth = $lastMonth->copy()->startOfMonth();
            $lastDayOfMonth = $lastMonth->copy()->endOfMonth();

            $weeks = [];
            $startOfWeek = $firstDayOfMonth->copy()->startOfWeek();
            while ($startOfWeek->lte($lastDayOfMonth)) {
                $endOfWeek = $startOfWeek->copy()->endOfWeek();
                $weeks[] = [
                    'start' => $startOfWeek->copy(),
                    'end' =>  $endOfWeek,
                ];
                $startOfWeek->addWeek();
            }

            foreach ($weeks as $index => $week) {
                $weekLabel = $week['start']->format('d M') . ' - ' . $week['end']->format('d M');

                $columns[] = TextColumn::make('week_' . $index)
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
            } elseif ($type=== 'day') {
            for ($day = 1; $day <= $daysInLastMonth; $day++) {
                $dayLabel = $lastMonth->copy()->day($day)->format('d M');

                $columns[] = TextColumn::make('day_'.$day)
                    ->label($dayLabel)
                    ->getStateUsing(function ($record) use ($lastMonth, $day) {
                        $dayDate = $lastMonth->copy()->day($day);
                        $values = $record->getMonthlyData($dayDate,null);

                        $transactionData = [];
                        foreach ($values as $value) {
                            $tagId = $value->tag_id;
                            $monthKey = $value->created_at->month;
                            $dayKey = $value->created_at->day;


                            if (isset($transactionData[$tagId][$monthKey][$dayKey])) {
                                $transactionData[$tagId][$monthKey][$dayKey]['amount'] += $value->amount;
                            } else {
                                $transactionData[$tagId][$monthKey][$dayKey] = [
                                    'amount' => $value->amount,
                                ];
                            }
                        }

                        return $transactionData[$record->id][$lastMonth->month][$day] ?? null;
                    });
            }
        }

        return $table->columns($columns)->filters($filters);
//        return $table->columns($columns);
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
            'index' => Pages\ListAnalyzes::route('/'),
            //            'create' => Pages\CreateAnalyze::route('/create'),
            //            'edit' => Pages\EditAnalyze::route('/{record}/edit'),
        ];
    }
}
