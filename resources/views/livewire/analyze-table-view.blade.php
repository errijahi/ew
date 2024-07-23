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
            @php
                $totalCounts = array_fill_keys(array_keys($transactionDataByPeriod), 0);
                $totalSums = array_fill_keys(array_keys($transactionDataByPeriod), 0);
                $totalCountsOverall = 0;
                $totalSumsOverall = 0;
                $currentYear = Carbon\Carbon::now()->year;
            @endphp

            @foreach ($paginatedData as $modelId => $model)
                @php
                    $totalCount = 0;
                    $totalSum = 0;
                @endphp
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ $model['name'] ?? $model['account_name'] }}</th>
                    @foreach (array_keys($transactionDataByPeriod) as $periodLabel)
                        @php
                            $amount = 0;
                            if (is_string($periodLabel) && !str_contains($periodLabel, ' ')) {
                                $amount = $tableValues[$model['id']][$currentYear][$periodLabel]['amount'] ?? 0;
                            } elseif (is_string($periodLabel)) {
                                $startDate = Carbon\Carbon::createFromFormat('d M', explode(' - ', $periodLabel)[0]);
                                $fullDate = Carbon\Carbon::createFromFormat('d M Y', $startDate->format('d M') . ' ' . $currentYear);
                                $weekOfYear = $fullDate->weekOfYear;
                                $day = $fullDate->day;

                                if (str_contains($periodLabel, ' - ')) {
                                    $amount = $tableValues[$model['id']][$currentYear][$weekOfYear]['amount'] ?? 0;
                                } else {
                                    $month = explode(' ', $periodLabel)[1];
                                    $monthName = DateTime::createFromFormat('M', $month)->format('F');
                                    $amount = $tableValues[$model['id']][$currentYear][$monthName][$day]['amount'] ?? 0;
                                }
                            } else {
                                $amount = $tableValues[$model['id']][$periodLabel]['amount'] ?? 0;
                            }

                            if ($amount !== 0) {
                                $totalCount++;
                                $totalSum += $amount;
                                $totalCounts[$periodLabel]++;
                                $totalSums[$periodLabel] += $amount;
                                $totalCountsOverall++;
                                $totalSumsOverall += $amount;
                            }
                        @endphp
                        <td class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ $amount }}</td>
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ $totalSums[$periodLabel] }}</th>
                @endforeach
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"></th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"></th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"></th>
            </tr>
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Average</th>
                @foreach (array_keys($transactionDataByPeriod) as $periodLabel)
                    @php
                        $average = $totalCounts[$periodLabel] > 0 ? $totalSums[$periodLabel] / $totalCounts[$periodLabel] : 0;
                    @endphp
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ number_format($average, 2) }}</th>
                @endforeach
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"></th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"></th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"></th>
            </tr>
            </tfoot>
        </table>
    </div>
</div>
