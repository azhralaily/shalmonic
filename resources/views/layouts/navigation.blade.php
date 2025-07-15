<div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
        {{ __('Dashboard') }}
    </x-nav-link>

    @if(auth()->user()->isAdmin() || auth()->user()->isOperator())
        <x-nav-link :href="route('controls')" :active="request()->routeIs('controls')">
            {{ __('Controls') }}
        </x-nav-link>
    @endif

    <x-nav-link :href="route('dataoverview')" :active="request()->routeIs('dataoverview')">
        {{ __('Data Overview') }}
    </x-nav-link>

    @if(auth()->user()->isAdmin())
         <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.index')">
            {{ __('User Management') }}
        </x-nav-link>
    @endif
</div>