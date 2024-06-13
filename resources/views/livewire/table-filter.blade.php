<div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; align-items: center;">
            <div>
                <form wire:submit.prevent="create" id="show-time">
                    <x-filament::input.wrapper>
                        <x-filament::input.select wire:model="status" wire:change="create">
                            <option value="year">Year</option>
                            <option value="month">Month</option>
                            <option value="week">Week</option>
                            <option value="day">Day</option>
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </form>
            </div>

            <div>
                <form wire:submit.prevent="createTimeRange" id="show-time2">
                    <x-filament::input.wrapper>
                        <x-filament::input.select wire:model="timeRange" id="timeRangeSelect" wire:change="createTimeRange">
                            <option value="last 7 days">Last 7 days</option>
                            <option value="last 30 days">Last 30 days</option>
                            <option value="last 3 weeks">Last 4 weeks</option>
                            <option value="last 6 weeks">Last 7 weeks</option>
                            <option value="last 3 months">Last 3 months</option>
                            <option value="last 6 months">Last 6 months</option>
                            <option value="last 3 years">Last 3 years</option>
                            <option value="last 6 years">Last 6 years</option>
{{--                            <option value="custom">Custom Date Range</option>--}}
                        </x-filament::input.select>
                    </x-filament::input.wrapper>

                    <!-- Custom date range input fields -->
{{--                    <div id="customDateRange" style="display: none;">--}}
{{--                        <x-filament::input.wrapper>--}}
{{--                            <x-filament::input id="startDate" wire:model="startDate" type="date" />--}}
{{--                        </x-filament::input.wrapper>--}}

{{--                        <x-filament::input.wrapper>--}}
{{--                            <x-filament::input id="endDate" wire:model="endDate" type="date" />--}}
{{--                        </x-filament::input.wrapper>--}}
{{--                    </div>--}}
                </form>
            </div>

{{--            <script>--}}
{{--                document.addEventListener('DOMContentLoaded', function () {--}}
{{--                    const timeRangeSelect = document.getElementById('timeRangeSelect');--}}
{{--                    const customDateRange = document.getElementById('customDateRange');--}}

{{--                    timeRangeSelect.addEventListener('change', function () {--}}
{{--                        if (this.value === 'custom') {--}}
{{--                            customDateRange.style.display = 'block';--}}
{{--                        } else {--}}
{{--                            customDateRange.style.display = 'none';--}}
{{--                        }--}}
{{--                    });--}}
{{--                });--}}
{{--            </script>--}}
        </div>
</div>
