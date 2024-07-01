<div class="filament-tables-component w-full">
    <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow w-full">
        <table class="min-w-full w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead>
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Time period</th>
                @foreach (array_keys($data) as $monthName)
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ $monthName }}</th>
                @endforeach
            </tr>
            </thead>
            <tbody>
            @foreach ($tagName as $tagId => $tag)
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ $tag->name ?? $tag->account_name }}</th>
                    @foreach (array_keys($data) as $monthName)
{{--                        {{dd($data)}}--}}
                        <td class="px-6 py-4 whitespace-nowrap text-xs font-medium text-gray-500 dark:text-gray-400">
                            @if(is_string($monthName) && !str_contains($monthName, ' '))
                                 {{ $tableValues[$tag->id][DateTime::createFromFormat('F', $monthName)->format('n')]['amount'] ?? "0"}}
                            @elseif(is_string($monthName))
                                @php
                                    $startDate = Carbon\Carbon::createFromFormat('d M', explode(' - ', $monthName)[0]);
                                    $currentYear = Carbon\Carbon::now()->year;
                                    $fullDate = Carbon\Carbon::createFromFormat('d M Y', $startDate->format('d M') . ' ' . $currentYear);
                                    $weekOfYear = $fullDate->weekOfYear;
                                    $day = $fullDate->day;
                                @endphp
                                @if( (is_string($monthName) && str_contains($monthName, ' - ')))
                                        {{ $tableValues[$tagId][$weekOfYear]['amount'] ?? "0"}}
                                @else
                                    {{ $tableValues[$tagId][$day]['amount'] ?? "0"}}
                                @endif

                            @else
                                {{ $tableValues[$tagId][$monthName]['amount'] ?? "0"}}
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
            </tbody>
            <tfoot class="bg-gray-50 dark:bg-gray-900">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sum</th>
{{--                {{dd($sums)}}--}}
                @foreach (array_keys($data) as $monthName)
{{--                        {{dd($sum)}}--}}
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{$sums[$monthName]}}</th>
                @endforeach
            </tr>
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Average</th>
                @foreach (array_keys($data) as $monthName)
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Test</th>
                @endforeach
            </tr>
            </tfoot>
        </table>
    </div>
</div>
