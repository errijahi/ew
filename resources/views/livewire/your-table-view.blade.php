<div class="filament-tables-component w-full">
    <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow w-full">
        <table class="min-w-full w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Time period</th>
                @foreach (array_keys($data) as $monthName)
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ $monthName }}</th>
                @endforeach
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Count</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sum</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Average</th>
            </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800">
            @foreach ($tagName as $tagId => $tag)
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ $tag->name ?? $tag->account_name }}</th>
                    @php
                        $totalCount = 0;
                        $totalSum = 0;
                    @endphp
                    @foreach (array_keys($data) as $monthName)
                        <td class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            @php
                                $currentYear2 = Carbon\Carbon::now()->year;
                                $amount = 0;
                            @endphp
                            @if(is_string($monthName) && !str_contains($monthName, ' '))
                                @php
                                    $amount = $tableValues[$tag->id][$currentYear2][$monthName]['amount'] ?? 0;
                                @endphp
                            @elseif(is_string($monthName))
                                @php
                                    $startDate = Carbon\Carbon::createFromFormat('d M', explode(' - ', $monthName)[0]);
                                    $currentYear = Carbon\Carbon::now()->year;
                                    $currentMonth = Carbon\Carbon::now()->month;
                                    $fullDate = Carbon\Carbon::createFromFormat('d M Y', $startDate->format('d M') . ' ' . $currentYear);
                                    $weekOfYear = $fullDate->weekOfYear;
                                    $day = $fullDate->day;
                                @endphp
                                @if( (is_string($monthName) && str_contains($monthName, ' - ')))
                                    @php
                                        $amount = $tableValues[$tag->id][$weekOfYear]['amount'] ?? 0;
                                    @endphp
                                @else
                                    @php
                                        $results = [];
                                    @endphp

                                    @for($month = 1; $month <= 12; $month++)
                                        @php
                                            $amount = $tableValues[$tag->id][$currentYear][$month][$day]['amount'] ?? 0;
                                            if ($amount !== 0) {
                                                $results[] = $amount;
                                            }
                                        @endphp
                                    @endfor

                                    @foreach ($results as $result)
                                        {{ $result }}
                                    @endforeach

                                    @if (empty($results))
                                        0
                                    @endif
                                @endif
                            @else
                                @php
                                    $amount = $tableValues[$tag->id][$monthName]['amount'] ?? 0;
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
                @foreach (array_keys($data) as $monthName)
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{$sums[$monthName]}}</th>
                @endforeach
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"></th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"></th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"></th>
            </tr>
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Average</th>
                @foreach (array_keys($data) as $monthName)
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{$averages[$monthName]}}</th>
                @endforeach
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"></th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"></th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"></th>
            </tr>
            </tfoot>
        </table>
    </div>
</div>
