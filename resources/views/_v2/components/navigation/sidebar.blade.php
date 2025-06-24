<div class="sidebar-component">
    @include('_v2.components.logo')
    <div class="good-types">
        @foreach($goodTypes as $goodType)
            @include(
                '_v2.components.menu.menu-item',
                [
                    'code' => $goodType->code,
                    'icon' => '/img/types/' . $goodType->icon . '.svg'
                ]
            )
        @endforeach
    </div>
</div>

<style>
    .sidebar-component {
        padding: 70px 50px;
    }
    .good-types {
        margin-top: 50px;
    }
</style>
