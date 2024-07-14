<div style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: center;">
    <div style="flex: 1;">
        <form wire:submit.prevent="create" id="show-time">
            <x-filament::input.wrapper style="margin-bottom: 0;">
                <x-filament::input.select wire:model="status" wire:change="create">
                    <option value="year">Year</option>
                    <option value="month">Month</option>
                    <option value="week">Week</option>
                    <option value="day">Day</option>
                </x-filament::input.select>
            </x-filament::input.wrapper>
        </form>
    </div>

    <div style="flex: 1;">
        <form wire:submit.prevent="createTimeRange" id="show-time2">
            <x-filament::input.wrapper style="margin-bottom: 0;">
                <x-filament::input.select wire:model="timeRange" id="timeRangeSelect" wire:change="createTimeRange">
                    <option value="last 7 days">Last 7 days</option>
                    <option value="last 30 days">Last 30 days</option>
                    <option value="last 4 weeks">Last 4 weeks</option>
                    <option value="last 7 weeks">Last 7 weeks</option>
                    <option value="last 3 months">Last 3 months</option>
                    <option value="last 6 months">Last 6 months</option>
                    <option value="last 3 years">Last 3 years</option>
                    <option value="last 6 years">Last 6 years</option>
                </x-filament::input.select>
            </x-filament::input.wrapper>
        </form>
    </div>

    <!-- New Date Range Search Form -->
    <div style="flex: 1; display: flex; gap: 1rem; align-items: center;">
        <form wire:submit.prevent="searchByDateRange" style="flex: 1; display: flex; gap: 1rem; align-items: center;">
            <x-filament::input.wrapper style="margin-bottom: 0; flex: 1; display: flex; align-items: center;">
                <x-filament::input type="date" wire:model="startDate" id="startDate" style="flex: 1;" />
            </x-filament::input.wrapper>
            <x-filament::input.wrapper style="margin-bottom: 0; flex: 1; display: flex; align-items: center;">
                <x-filament::input type="date" wire:model="endDate" id="endDate" style="flex: 1;" />
            </x-filament::input.wrapper>
            <x-filament::button type="submit" wire:submit="searchByDateRange" style="flex: 0;">Search</x-filament::button>
        </form>
    </div>
</div>
