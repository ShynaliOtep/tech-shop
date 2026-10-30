<div class="sidebar-component">
    @include('_v2.components.logo')
    <div class="good-types">
        @foreach($goodTypes as $goodType)
            @include(
                '_v2.components.menu.menu-item',
                [
                    'code' => $goodType->code,
                    'name' => $goodType->name,
                    'icon' => $goodType->iconO?->svg
                ]
            )
        @endforeach
    </div>
</div>

<style>
    /* Меню остаётся на экране при прокрутке страницы */
    .sidebar-component {
        padding: 70px 50px;
        position: sticky;
        top: 0;
        max-height: 100vh;
        overflow-y: auto;
        scrollbar-width: none;
    }
    .sidebar-component::-webkit-scrollbar {
        display: none;
    }
    .good-types {
        margin-top: 50px;
    }
    @media (max-width: 1440px) {
        .sidebar-component {
            padding: 50px 30px;
        }
    }
</style>
