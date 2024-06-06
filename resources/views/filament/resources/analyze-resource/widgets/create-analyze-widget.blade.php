<div>
    <x-filament::section class="col-span-full">
        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1rem; align-items: center;">

            <!-- Show time by section -->
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

            <!-- Query by time range section -->
            <div>
                <form wire:submit.prevent="getTable" id="query-time-range">
                    <x-filament::input.wrapper>
                        <x-filament::input.select wire:model="getTable" wire:change="getTable">
                            <option value="thisMonth">This month</option>
                            <option value="thisYear">This year</option>
                            <option value="thisWeek">This week</option>
                            <option value="custom">custom</option>
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </form>
            </div>

            <!-- Custom time range section -->
{{--            <div id="custom-time-range" style="display: {{ $getTable === 'custom' ? 'grid' : 'none' }}; grid-column: span {{ $widget->getColumnSpan()['md'] ?? 1 }};">--}}
{{--                <div style="display: grid; grid-template-columns: auto 1fr; gap: 1rem; align-items: center;">--}}
{{--                    <label for="from">From</label>--}}
{{--                    <x-filament::input.wrapper>--}}
{{--                        <x-filament::input type="date" id="from" wire:model="fromDate"/>--}}
{{--                    </x-filament::input.wrapper>--}}

{{--                    <label for="to">To</label>--}}
{{--                    <x-filament::input.wrapper>--}}
{{--                        <x-filament::input type="date" id="to" wire:model="toDate"/>--}}
{{--                    </x-filament::input.wrapper>--}}
{{--                </div>--}}
{{--            </div>--}}
        </div>

    </x-filament::section>
</div>
