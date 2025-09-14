<div class="w-full">
    <div class="w-full flex text-center">
        @foreach($menu as $slug => $label)
            <a href="{{ route('dashboard', ['page' => $slug]) }}" wire:navigate
               class="block mx-2 px-3 py-2
                      {{ $page === $slug ? 'border-b-2 border-b-pars-500 text-pars-500 font-bold' : 'text-pars-700 hover:border-b' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="w-full bg-pars-100 rounded-2xl shadow p-4  min-h-[80vh] ">
        @switch($page)
            @case('overview')
                <livewire:dashboard.overview wire:navigate="true"/>
                @break

            @case('address')
                <livewire:dashboard.address wire:navigate="true"/>
                @break

            @case('order')
                <livewire:dashboard.order wire:navigate="true"/>
                @break

            @case('profile')
                <livewire:dashboard.profile wire:navigate="true"/>
                @break

            @default
                <div>صفحه پیدا نشد.</div>
        @endswitch
    </div>
</div>
