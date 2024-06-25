<div class="filament-tables-component w-full">
    <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow w-full">
        <table class="min-w-full w-full divide-y divide-gray-200 dark:divide-gray-700">
            <tfoot class="bg-gray-50 dark:bg-gray-900">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Time period</th>
                @foreach (range(1, 12) as $month)
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Test</th>
                @endforeach
            </tr>
            @foreach ($tagName as $tag)
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ $tag }}</th>
                    @foreach (range(1, 12) as $month)
                        <td class="px-6 py-4 whitespace-nowrap text-xs font-medium text-gray-500 dark:text-gray-400">Test</td>
                    @endforeach
                </tr>
            @endforeach
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sum</th>
                @foreach (range(1, 12) as $month)
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Test</th>
                @endforeach
            </tr>
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Average</th>
                @foreach (range(1, 12) as $month)
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Test</th>
                @endforeach
            </tr>
            </tfoot>
        </table>
    </div>
</div>
