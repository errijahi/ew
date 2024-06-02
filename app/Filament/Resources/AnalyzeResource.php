<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AnalyzeResource\Pages;
use App\Models\Analyze;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Carbon\Carbon;

class AnalyzeResource extends Resource
{
    protected static ?string $model = Analyze::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Finances';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        $columns = [
            TextColumn::make('name')
                ->getStateUsing(function ($record) {
                    return $record->name ?? ($record->account_name ?? $record->payee);
                }),
        ];

        // Add dynamic columns for each month
        $currentYear = Carbon::now()->year;
        for ($month = 1; $month <= 12; $month++) {
            $monthName = Carbon::create()->month($month)->format('F'); // Get full month name
            $columns[] = TextColumn::make('month_' . $month)
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

        return $table->columns($columns);
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
