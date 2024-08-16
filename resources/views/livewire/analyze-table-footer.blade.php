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
        id="paginationComponent"
    />
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const paginationComponent = document.getElementById('paginationComponent');

        if (paginationComponent) {
            paginationComponent.addEventListener('click', function (event) {
                const target = event.target;
                console.log(target)
                const isIcon = target.closest('svg');
                const text = target.textContent.trim();
                console.log(text);
                if ((!isNaN(text) && text.length > 0) || isIcon) {
                    // TODO: It works but I don't like this depending on the lot of stuff it might get slower of faster
                    //  destroying this functionality.
                    setTimeout(function() {
                        location.reload();
                    }, 600);
                }
            });
        }
    });
</script>
