<div class="w-full pb-3 overflow-x-auto">
    <div class="flex">
        @foreach($this->stories as $story)
            <div
                wire:click="openStory({{ $story['id'] }})"
                class="w-24 shrink-0 text-center cursor-pointer select-none">
                <div class="mx-auto w-[78px] h-[78px] rounded-full
                            bg-gradient-to-tr from-pink-500 via-red-500 to-orange-400
                            flex items-center justify-center">

                    <div class="w-[72px] h-[72px] rounded-full bg-pars-200 flex items-center justify-center">

                        <div class="w-[70px] h-[70px] rounded-full overflow-hidden border border-white">
                            <img
                                src="{{ $story['cover'] }}"
                                alt="{{ $story['title'] }}"
                                class="w-full h-full object-cover"
                            >
                        </div>

                    </div>
                </div>
                <p class="mt-2 text-xs text-gray-700 line-clamp-1">
                    {{ $story['title'] }}
                </p>
            </div>
        @endforeach
    </div>
    @if($activeData)
        <div
            class="fixed inset-0 z-50 bg-black/80"
            wire:click="closeStory"
            wire:ignore.self
            wire:key="story-slider"
            x-init="init()"
            x-data="storySlider(@entangle('currentIndex'))"
            x-on:keydown.left.window="$wire.prevItem()"
            x-on:keydown.right.window="$wire.nextItem()"
            x-on:keydown.escape.window="$wire.closeStory()"
        >
            <div class="relative w-full h-full flex items-center justify-center" wire:click.stop>
                <div class="absolute top-0 left-0 right-0 flex gap-1 p-4 z-30">
                    @foreach($activeData['items'] as $index => $item)
                        <div class="flex-1 h-1 bg-gray-600 rounded-full overflow-hidden">
                            <div
                                class="h-full bg-white"
                            :style="`width: ${currentIndex === {{ $index }} ? progress : (currentIndex > {{ $index }} ? 100 : 0)}%`"
                            ></div>
                </div>
                @endforeach
            </div>
                <button
                    wire:click.stop="closeStory"
                    class="absolute cursor-pointer z-20 top-8 font-semibold left-4 text-white text-3xl hover:text-gray-300"
                >&times;
                </button>
                <div class="absolute top-8 right-4 z-20 text-white font-semibold">
                    {{ $activeData['items'][$currentIndex]['product_name'] ?? '' }}
                </div>
                <div class="relative text-center">
                    <div
                        x-show="loading"
                        class="absolute bg-gray-900/30 inset-0 flex items-center justify-center z-20"
                    >
                        <div class="w-12 h-12 border-4 border-white/40 border-t-white rounded-full animate-spin"></div>
                    </div>
                    <img
                        :key="currentIndex"
                        src="{{ $activeData['items'][$currentIndex]['image'] ?? '' }}"
                        @load="imageLoaded"
                        x-on:error="imageLoaded"
                        class="relative lg:h-screen object-contain select-none pointer-events-none z-10"
                        alt="">

                    <div class="absolute justify-between inset-0 flex">
                        <div class="w-5/12 h-full" wire:click.stop="prevItem"></div>
                        <div class="w-2/12 h-full"></div>
                        <div class="w-5/12 h-full" wire:click.stop="nextItem"></div>
                    </div>

                    <a
                        href="{{ $activeData['items'][$currentIndex]['link'] ?? '#' }}"
                        class="absolute bottom-4 right-2 z-30"
                    >
                        <div
                            class="group flex items-center gap-3 bg-gradient-to-l from-red-600 to-orange-400  text-white font-bold text-lg px-4 py-2 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
                            <span>مشاهده محصول</span>
                            <svg class="w-6 h-6 transition-transform group-hover:-translate-x-1" fill="none"
                                 stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                      d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </div>
                    </a>
                </div>

            </div>
        </div>
    @endif
</div>
