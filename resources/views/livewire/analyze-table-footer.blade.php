@php
    $defaultPageOptions = [5, 10, 20, 50, 100];

    $pageOptions = [$perPage];
    foreach ($defaultPageOptions as $option) {
        if ($option !== $perPage) {
            $pageOptions[] = $option;
        }
    }

     $shouldRelod = session('reloadPage');
    if($shouldRelod  === 'true') {
         session(['reloadPage' => 'false']);
@endphp

<meta http-equiv="refresh" content="0">

@php
    }
@endphp

<div class="p-4">
    <x-filament::pagination
        wire:model="perPage"
        wire:change="changeInPerPage"
        :paginator="$paginatedData"
        :page-options="$pageOptions"
    />
</div>
