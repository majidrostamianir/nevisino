<div class="min-h-screen bg-gray-50 py-12" x-data="{ search: @entangle('searchTerm') }">
    <div class="container mx-auto px-4">

        {{-- هدر --}}
        <div class="text-center mb-10">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-3">
                سوالات <span class="text-amber-600">متداول</span>
            </h1>
            <div class="w-20 h-1 bg-amber-500 mx-auto rounded-full"></div>
            <p class="text-gray-500 mt-4 max-w-2xl mx-auto">
                پاسخ سوالات رایج شما درباره خرید، ارسال، مرجوعی و ...
            </p>
        </div>

        <div class="max-w-3xl mx-auto">

            <div class="mb-8">
                <div class="relative">
                    <input type="text"
                           wire:model.live.debounce.300ms="searchTerm"
                           placeholder="جستجو در سوالات متداول..."
                           class="w-full border  border-gray-200 rounded-xl pl-12  pr-10 py-3 focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition-all">
                    <svg class="absolute left-4 top-3.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    @if($searchTerm)
                        <button wire:click="$set('searchTerm', '')" class="absolute right-4 top-3.5 text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    @endif
                </div>
            </div>
            <div class="space-y-3">
                @forelse($faqs as $faq)
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-200"
                         x-data="{ open: @entangle('openIndex') === {{ $faq['id'] }} }"
                         @open-changed.window="if($event.detail !== {{ $faq['id'] }}) open = false">

                        <button @click="open = !open; $wire.toggleQuestion({{ $faq['id'] }})"
                                class="w-full text-right px-6 py-4 flex justify-between items-center hover:bg-gray-50 transition-colors duration-200 cursor-pointer group">
                            <span class="font-bold text-gray-800 group-hover:text-amber-600 transition-colors duration-200">{{ $faq['question'] }}</span>
                            <svg class="w-5 h-5 text-gray-400 transition-all duration-300"
                                 :class="open ? 'rotate-180 text-amber-600' : ''"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="open"
                             x-collapse.duration.300ms
                             x-cloak
                             class="px-6 pb-4 text-gray-600 border-t border-gray-100 pt-3 leading-relaxed text-justify ">
                            {!! $faq['answer'] !!}
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-xl p-12 text-center border border-gray-200">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h3 class="text-xl font-bold text-gray-700 mb-2">سوالی یافت نشد!</h3>
                        <p class="text-gray-500">سوال مورد نظر خود را با عبارت دیگری جستجو کنید.</p>
                        <button wire:click="$set('searchTerm', '')" class="mt-4 px-6 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition">
                            پاک کردن جستجو
                        </button>
                    </div>
                @endforelse
            </div>

            {{-- اگر سوالی پاسخ داده نشد --}}
            <div class="mt-10 text-center bg-gradient-to-r from-amber-50 to-amber-100 rounded-xl p-6 border border-amber-200">
                <p class="text-gray-700 font-medium">سوالی دارید که در اینجا پاسخ داده نشده؟</p>
                <p class="text-gray-500 text-sm mt-1">تیم پشتیبانی نویسینو آماده پاسخگویی به شماست</p>
                <a href="{{ route('contact') }}" wire:navigate class="inline-block mt-4 bg-amber-600 hover:bg-amber-700 text-white px-8 py-2.5 rounded-xl font-bold transition-all duration-200 shadow-md hover:shadow-lg">
                    ارتباط با پشتیبانی
                </a>
            </div>

        </div>
    </div>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</div>
