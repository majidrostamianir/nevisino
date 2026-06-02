<div class="w-full">
    {{-- منوی تب‌ها --}}
    <div class="border-b border-gray-100 bg-white rounded-2xl">
        <div class="flex flex-wrap gap-1 px-4 sm:px-6">
            @foreach($menu as $slug => $label)
                <a href="{{ route('dashboard', ['page' => $slug]) }}" wire:navigate
                   class="relative flex items-center gap-2 px-4 py-3 text-sm font-medium transition-all duration-200
                          {{ $page === $slug
                              ? 'text-pars-600 border-b-2 border-pars-600'
                              : 'text-gray-500 hover:text-pars-500 hover:bg-gray-50 rounded-t-lg' }}">
                    {{-- آیکون‌ها --}}
                    @switch($slug)
                        @case('overview')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            @break
                        @case('address')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            @break
                        @case('order')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                            @break
                        @case('profile')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            @break
                    @endswitch
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>

    {{-- محتوای صفحه --}}
    <div class="mt-6 px-4 sm:px-6">
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
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-gray-500">صفحه مورد نظر پیدا نشد.</p>
                </div>
        @endswitch
    </div>
</div>