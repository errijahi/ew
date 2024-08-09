<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="filament-tables-component w-full">
    <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow w-full">
        @if ($viewType === 'table')
            <table class="min-w-full w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"></th>
                    @foreach (array_keys($transactionDataByPeriod) as $periodLabel)
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ $periodLabel }}</th>
                    @endforeach
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Count
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Sum
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Average
                    </th>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ $model['name'] }}</th>
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Sum
                    </th>
                    @foreach (array_keys($transactionDataByPeriod) as $periodLabel)
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ $totalSums[$periodLabel] }}</th>
                    @endforeach
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"></th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"></th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"></th>
                </tr>
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Average
                    </th>
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
        @endif
    </div>
</div>

@php
    $shouldRelod = session('reloadPage');
    if($shouldRelod  === 'true' && isset($viewType) && $viewType !== 'table') {
         session(['reloadPage' => 'false']);
@endphp

<meta http-equiv="refresh" content="0">

@php
    }
@endphp


@if($viewType === 'pie')
    <div>
        <canvas id="pieChart" style="height: 50vh"></canvas>
    </div>
@endif

@if($viewType === 'bar')
    <div>
        <canvas id="barChart" style="height: 50vh"></canvas>
    </div>
@endif

@if($viewType === 'line')
    <div>
        <canvas id="lineChart" style="height: 50vh"></canvas>
    </div>
@endif

@if($viewType === 'stacked')
    <div>
        <canvas id="stackedChart" style="height: 50vh"></canvas>
    </div>
@endif

@php
        $currentYear = Carbon\Carbon::now()->year;
        $labels = [];
        $totalSums = [];

        foreach ($paginatedData as $modelId => $model) {
            $labels[] = $model['name'];
            $modelSum = 0;

            foreach (array_keys($transactionDataByPeriod) as $periodLabel) {
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
                    $modelSum += $amount;
                }
            }

            $totalSums[] = $modelSum;
        }

@endphp

<script defer>
    let labels = @json($labels);
    let sums = @json($totalSums);
    let period = @json($transactionDataByPeriod);
    {{--let datasets1 = @json($datasets, JSON_THROW_ON_ERROR); // Fetch the PHP data--}}
        let tableValues = @json($tableValues);
        let paginatedData = @json($paginatedData);
        let selectedPeriod = @json($selectedPeriod);


    document.addEventListener('DOMContentLoaded', function () {
        const pie = document.getElementById('pieChart');
        if (pie) {
            new Chart(pie, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        label: '# of Votes',
                        data: sums,
                        borderWidth: 1
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                }
            });
        }

        const bar = document.getElementById('barChart');
        if (bar) {
            const data = {
                labels: labels,
                datasets: [{
                    label: 'Red',
                    data: sums,
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }]
            };

            const config = {
                type: 'bar',
                data: data,
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    maintainAspectRatio: false,
                },
            };

            new Chart(bar, config);
        }

        const line = document.getElementById('lineChart');
        if (line) {
            const labels = ['January', 'February', 'March', 'April', 'May', 'June', 'July'];
            const data = {
                labels: labels,
                datasets: [{
                    label: 'Red',
                    data: sums,
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }]
            };

            const config = {
                type: 'line',
                data: data,
                options: {
                    maintainAspectRatio: false,
                },
            };

            new Chart(line, config);
        }

        let labels1 = Object.keys(period);
        // console.log(tableValues);
        // console.log(labels1);

        let test = [];
        let ids = [];
        // let sums2 = [];

        console.log(tableValues);
        console.log(selectedPeriod)

        paginatedData['data'].forEach((model) => {
            const yearData = tableValues[model['id']];

            if(selectedPeriod === 'year'){
                ids.push(model['id']);
                test.push(yearData);
            }else if (yearData && yearData['2024']) {
                ids.push(model['id']);
                test.push(yearData['2024']);
            }
        });

        // console.log(test);
        // console.log(ids);
        // console.log(labels1);

// Initialize sums2 as an array of empty arrays based on the number of ids
        let sums2 = Array(ids.length).fill(null).map(() => Array(labels1.length).fill(0));

        // console.log(sums2);

// Populate sums2 with the values from test
        ids.forEach((id, index) => {
            const values = test[index];
            if (values) {
                labels1.forEach((label, monthIndex) => {
                    // Extract the numeric value if it exists and is an object
                    if (values[label] && typeof values[label] === 'object' && values[label].amount) {
                        sums2[index][monthIndex] = parseFloat(values[label].amount); // Convert to number
                    } else if (typeof values[label] === 'number') {
                        sums2[index][monthIndex] = values[label]; // Directly assign if it's already a number
                    } else {
                        sums2[index][monthIndex] = 0; // Default to 0 if no amount is available
                    }
                });
            }
        });
        console.log(sums2);

        // let sums2 = [
        //     // The array itself is tag, but the values in the array are data for the months.
        //     [10, 20, 30, 40, 50], // Data for dataset 1 (e.g., tag1)
        //     [15, 25, 35, 45, 55], // Data for dataset 2 (e.g., tag2)
        //     [20, 30, 40, 50, 60],  // Data for dataset 3 (e.g., tag3)
        //     [20, 40, 60, 70, 80],  // Data for dataset 3 (e.g., tag4)
        //     [20, 50, 70, 80, 90, 100],  // Data for dataset 3 (e.g., tag5)
        // ];



//         let datasets1 = [];
//
//         const targetYear = '2024';
//
// // Iterate over each tagId in tableValues
//         Object.entries(tableValues).forEach(([tagId, years], i) => {
//             let tagAmounts = new Array(labels1.length).fill(0); // Initialize with zeros for each month
//
//             let hasData = false; // Flag to check if the tag has any data
//
//             // Iterate over each year and get the amounts for each month
//             Object.entries(years).forEach(([year, monthsData]) => {
//                 if (year === targetYear) { // Check if the year matches the target year
//                     labels1.forEach((month, index) => {
//                         if (monthsData[month]) {
//                             tagAmounts[index] = monthsData[month].amount;
//                             hasData = true; // Mark that there is data for this tag
//                         }
//                     });
//                 }
//             });
//
//             // Only add dataset if the tag has valid data
//             if (hasData) {
//                 // Determine color index based on the current dataset index
//                 // let colorIndex = i % colors.length;
//
//                 // Create a dataset object for each tagId
//                 datasets1.push({
//                     data: tagAmounts,
//                     label: labels[tagId] || `Tag ${tagId}`, // Use tag name if available, otherwise fallback to `Tag ${tagId}`
//                     // backgroundColor: colors[colorIndex], // Use the color based on index
//                     // borderColor: colors[colorIndex].replace('0.5', '1') // Slightly darker border color
//                 });
//             }
//         });

        const stacked = document.getElementById('stackedChart');
        if (stacked) {

            let datasets = [];
            for (let i = 0; i < sums2.length; i++) {
                datasets.push({
                    data: sums2[i],
                    label: labels[i],
                });
            }

            let data = {
                labels: labels1,
                datasets: datasets
            };

            // console.log(data);

            const config = {
                type: 'bar',
                data: data,
                options: {
                    indexAxis: 'y', // This option makes the bar chart horizontal
                    scales: {
                        x: {
                            stacked: true,
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true,
                        }
                    },
                    plugins: {
                        legend: {
                            display: false // Hides the legend
                        }
                    },
                    maintainAspectRatio: false,
                },

            };

            new Chart(stacked, config);
        }
    });
</script>

