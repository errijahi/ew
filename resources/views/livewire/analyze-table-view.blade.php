<div class="filament-tables-component w-full">
    <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow w-full">
        <table class="min-w-full w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Time period</th>
                @foreach (array_keys($transactionDataByPeriod) as $periodLabel)
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ $periodLabel }}</th>
                @endforeach
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Count</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sum</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Average</th>
            </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800">
            @foreach ($selectedModel as $modelId => $model)
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ $model->name ?? $model->account_name }}</th>
                    @php
                        $totalCount = 0;
                        $totalSum = 0;
                    @endphp
                    @foreach (array_keys($transactionDataByPeriod) as $periodLabel)
                        <td class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            @php
                                $currentYear2 = Carbon\Carbon::now()->year;
                                $amount = 0;
                            @endphp
                            @if(is_string($periodLabel) && !str_contains($periodLabel, ' '))
                                @php
                                    $amount = $tableValues[$model->id][$currentYear2][$periodLabel]['amount'] ?? 0;
                                @endphp
                            @elseif(is_string($periodLabel) )
                                @php
                                    $startDate = Carbon\Carbon::createFromFormat('d M', explode(' - ', $periodLabel)[0]);
                                    $currentYear = Carbon\Carbon::now()->year;
                                    $currentMonth = Carbon\Carbon::now()->month;
                                    $fullDate = Carbon\Carbon::createFromFormat('d M Y', $startDate->format('d M') . ' ' . $currentYear);
                                    $weekOfYear = $fullDate->weekOfYear;
                                    $day = $fullDate->day;
                                @endphp
                                @if( (is_string($periodLabel) && str_contains($periodLabel, ' - ')))
                                    @php
                                        $amount = $tableValues[$model->id][$currentYear][$weekOfYear]['amount'] ?? 0;
                                    @endphp
                                @else
                                    @php
                                        $results = [];
                                    @endphp
                                        @php
                                            $month = explode(' ',$periodLabel)[1];
                                            $monthAbbreviation = explode(' ', $periodLabel)[1];

                                            $months = [
                                                'Jan' => 1,
                                                'Feb' => 2,
                                                'Mar' => 3,
                                                'Apr' => 4,
                                                'May' => 5,
                                                'Jun' => 6,
                                                'Jul' => 7,
                                                'Aug' => 8,
                                                'Sep' => 9,
                                                'Oct' => 10,
                                                'Nov' => 11,
                                                'Dec' => 12,
                                            ];

                                            $monthNumber = $months[$monthAbbreviation];
                                            $amount = $tableValues[$model->id][$currentYear][$monthNumber][$day]['amount'] ?? 0;
                                            if ($amount !== 0) {
                                                $results[] = $amount;
                                            }
                                        @endphp
                                @endif
                            @else
                                @php
                                        $amount = $tableValues[$model->id][$periodLabel]['amount'] ?? 0;
                                @endphp
                            @endif
                            {{ $amount }}
                            @php
                                if ($amount !== 0) {
                                    $totalCount++;
                                    $totalSum += $amount;
                                }
                            @endphp
                        </td>
                    @endforeach
                    @php
                        $average = $totalCount > 0 ? $totalSum / $totalCount : 0;
                    @endphp
                    <td class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ $totalCount }}</td>
                    <td class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ $totalSum }}</td>
                    <td class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ number_format($average, 2) }}</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot class="bg-gray-50 dark:bg-gray-900">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sum</th>
                @foreach (array_keys($transactionDataByPeriod) as $periodLabel)
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{$sums[$periodLabel]}}</th>
                @endforeach
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"></th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"></th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"></th>
            </tr>
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Average</th>
                @foreach (array_keys($transactionDataByPeriod) as $periodLabel)
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{$averages[$periodLabel]}}</th>
                @endforeach
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"></th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"></th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"></th>
            </tr>
            </tfoot>
        </table>
    </div>
</div>
