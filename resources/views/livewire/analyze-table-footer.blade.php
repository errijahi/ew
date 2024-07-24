<div class="p-4">
    <x-filament::pagination wire:model="perPage" wire:change="changeInPerPage" id="pg"
        :paginator="$paginatedData"
        :page-options="[5, 10, 20, 50, 100]"
        :current-page-option-property=10
    />
</div>
