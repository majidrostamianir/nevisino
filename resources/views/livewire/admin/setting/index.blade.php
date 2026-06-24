<div>
    {{-- ============================================ --}}
    {{-- پیغام شناور در پایین صفحه --}}
    {{-- ============================================ --}}
    @if($message)
        <div x-data="{ show: true }"
             x-init="setTimeout(() => show = false, 5000)"
             x-show="show"
             x-transition:enter.duration.300ms
             x-transition:leave.duration.300ms
             class="fixed bottom-6 right-6 z-50 max-w-md w-full">
            <div class="rounded-2xl shadow-2xl border p-4 flex items-center justify-between
                {{ $messageType === 'success' ? 'bg-green-600 border-green-700 text-white' : '' }}
                {{ $messageType === 'error' ? 'bg-red-600 border-red-700 text-white' : '' }}
                {{ $messageType === 'info' ? 'bg-blue-600 border-blue-700 text-white' : '' }}
                {{ $messageType === 'warning' ? 'bg-yellow-600 border-yellow-700 text-white' : '' }}">

                <span class="flex items-center gap-3">
                    {{-- آیکون --}}
                    @if($messageType === 'success')
                        <svg class="w-6 h-6 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    @elseif($messageType === 'error')
                        <svg class="w-6 h-6 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    @elseif($messageType === 'info')
                        <svg class="w-6 h-6 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    @elseif($messageType === 'warning')
                        <svg class="w-6 h-6 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    @endif

                    {{-- متن پیغام --}}
                    <span class="text-sm font-medium">{{ $message }}</span>
                </span>

                {{-- دکمه بستن --}}
                <button @click="show = false"
                        class="text-white/80 hover:text-white transition-colors text-xl font-bold leading-none">
                    ×
                </button>
            </div>
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- هدر --}}
    {{-- ============================================ --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">⚙️ مدیریت تنظیمات</h1>
            <p class="text-sm text-gray-500 mt-1">مدیریت قیمت‌ها، تنظیمات سیستم و تولید نقشه سایت</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm">
                📦 {{ english_to_persian_num($totalProducts) }} محصول
            </span>
            <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">
                ⚙️ {{ english_to_persian_num($settingsCount) }} تنظیمات
            </span>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- بخش اصلی با ارتفاع هماهنگ --}}
    {{-- ============================================ --}}
    <div class="lg:flex lg:items-stretch gap-6">
        {{-- ستون تنظیمات (1/3) --}}
        <div class="w-full lg:w-1/3 mb-6 lg:mb-0 flex">
            <div class="w-full bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden flex flex-col">
                {{-- هدر ستون --}}
                <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-5 py-4 flex-shrink-0">
                    <h3 class="text-white font-bold text-lg flex items-center gap-2">
                        <span>🎛️</span> تنظیمات سیستم
                    </h3>
                </div>

                {{-- محتوا --}}
                <div class="p-5 flex-1 overflow-y-auto">
                    <form wire:submit="save" class="space-y-5">
                        {{-- قیمت‌های پایه --}}
                        <div>
                            <h5 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                <span class="w-1 h-5 bg-blue-500 rounded-full"></span>
                                💰 قیمت‌های پایه
                            </h5>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">📮 کرایه پست</label>
                                    <div class="relative">
                                        <input type="number"
                                               x-on:wheel.prevent
                                               wire:model="settings.post_price"
                                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm pr-4"
                                               placeholder="۰">
                                        <span class="absolute left-3 top-2.5 text-xs text-gray-400">تومان</span>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">📦 کرایه تیپاکس</label>
                                    <div class="relative">
                                        <input type="number"
                                               x-on:wheel.prevent
                                               wire:model="settings.tipax_price"
                                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm pr-4"
                                               placeholder="۰">
                                        <span class="absolute left-3 top-2.5 text-xs text-gray-400">تومان</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- قیمت کارتن‌ها --}}
                        <div>
                            <h5 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                <span class="w-1 h-5 bg-green-500 rounded-full"></span>
                                📦 قیمت کارتن‌ها
                            </h5>
                            <div class="grid grid-cols-3 gap-2">
                                @for($i=1; $i<=9; $i++)
                                    <div>
                                        <label class="block text-[10px] text-gray-500 text-center mb-1">سایز {{ english_to_persian_num($i) }}</label>
                                        <input type="number"
                                               x-on:wheel.prevent
                                               wire:model="settings.packaging_{{$i}}"
                                               class="w-full px-2 py-1.5 border border-gray-200 rounded-lg text-center text-xs focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                               placeholder="۰">
                                    </div>
                                @endfor
                            </div>
                        </div>

                        {{-- تنظیمات هزینه کارتن --}}
                        <div>
                            <h5 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                <span class="w-1 h-5 bg-yellow-500 rounded-full"></span>
                                📦 تنظیمات کارتن
                            </h5>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">دریافت هزینه کارتن</label>
                                    <select wire:model="settings.charge_packaging"
                                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 transition text-sm">
                                        <option value="1">✅ بله</option>
                                        <option value="0">❌ خیر</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">از چه مبلغی به بالا کارتن رایگان باشد؟</label>
                                    <div class="relative">
                                        <input type="number"
                                               x-on:wheel.prevent
                                               wire:model="settings.free_packaging_threshold"
                                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm pr-4"
                                               placeholder="۰">
                                        <span class="absolute left-3 top-2.5 text-xs text-gray-400">تومان</span>
                                    </div>
                                    <p class="text-[10px] text-gray-400 mt-1">اگر مبلغ سفارش از این مقدار بیشتر باشد، کارتن رایگان می‌شود</p>
                                </div>
                            </div>
                        </div>

                        {{-- تنظیمات هزینه کرایه --}}
                        <div>
                            <h5 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                <span class="w-1 h-5 bg-purple-500 rounded-full"></span>
                                🚚 تنظیمات کرایه
                            </h5>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">دریافت هزینه کرایه</label>
                                    <select wire:model="settings.charge_shipping"
                                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 transition text-sm">
                                        <option value="1">✅ بله</option>
                                        <option value="0">❌ خیر</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">از چه مبلغی به بالا کرایه رایگان باشد؟</label>
                                    <div class="relative">
                                        <input type="number"
                                               x-on:wheel.prevent
                                               wire:model="settings.free_shipping_threshold"
                                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm pr-4"
                                               placeholder="۰">
                                        <span class="absolute left-3 top-2.5 text-xs text-gray-400">تومان</span>
                                    </div>
                                    <p class="text-[10px] text-gray-400 mt-1">اگر مبلغ سفارش از این مقدار بیشتر باشد، کرایه رایگان می‌شود</p>
                                </div>
                            </div>
                        </div>

                        {{-- تنظیمات متفرقه --}}
                        <div>
                            <h5 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                <span class="w-1 h-5 bg-pink-500 rounded-full"></span>
                                ⚙️ تنظیمات تکمیلی
                            </h5>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">درصد سود سیستم</label>
                                    <div class="relative">
                                        <input type="number"
                                               x-on:wheel.prevent
                                               wire:model="settings.profit_percent"
                                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm pr-4"
                                               placeholder="۲۰">
                                        <span class="absolute left-3 top-2.5 text-xs text-gray-400">%</span>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">زمان انقضای سفارش (دقیقه)</label>
                                    <input type="number"
                                           x-on:wheel.prevent
                                           wire:model="settings.expire_order_time_minutes"
                                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm"
                                           placeholder="۳۰">
                                </div>
                            </div>
                        </div>

                        {{-- دکمه ذخیره --}}
                        <div class="pt-3 border-t">
                            <button type="submit"
                                    wire:loading.attr="disabled"
                                    class="w-full bg-gradient-to-r from-blue-600 to-indigo-700 text-white rounded-xl px-4 py-3 hover:shadow-lg transition-all duration-200 font-medium text-sm flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                                <span wire:loading.remove>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                                    </svg>
                                </span>
                                <span wire:loading>
                                    <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </span>
                                💾 ذخیره تنظیمات
                            </button>
                        </div>
                    </form>

                    {{-- بخش SiteMap --}}
                    <div class="mt-5 pt-4 border-t">
                        <h5 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                            <span class="w-1 h-5 bg-purple-500 rounded-full"></span>
                            🗺️ نقشه سایت
                        </h5>
                        <livewire:admin.setting.site-map />
                    </div>
                </div>
            </div>
        </div>

        {{-- ستون اصلی (2/3) با ارتفاع هماهنگ --}}
        <div class="w-full lg:w-2/3 flex">
            <div class="w-full bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden flex flex-col">
                {{-- هدر --}}
                <div class="bg-gradient-to-r from-yellow-600 to-orange-400 px-5 py-4 flex-shrink-0">
                    <h3 class="font-bold text-white">📊 مدیریت قیمت محصولات</h3>

                </div>

                {{-- محتوای PriceChanger --}}
                <div class="flex-1 overflow-y-auto p-1">
                    <livewire:admin.setting.price-changer />
                </div>
            </div>
        </div>
    </div>
</div>