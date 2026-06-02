<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-5 lg:p-6 space-y-6">
        {{-- ردیف اول: اطلاعات شخصی --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <p class="text-sm text-gray-500">نام و نام خانوادگی</p>
                </div>
                <p class="text-gray-800 font-bold text-lg">{{ $user->name }}</p>
            </div>
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-sm text-gray-500">شماره موبایل</p>
                    <span class="text-xs rounded-full bg-green-100 text-green-600 px-2 py-0.5 mr-1 font-medium">تایید شده</span>
                </div>
                <p class="text-gray-800 font-bold text-lg">{{ english_to_persian_num($user->mobile) }}</p>
            </div>
        </div>

        {{-- خط جداکننده --}}
        <div class="border-t border-gray-100"></div>

        {{-- ردیف دوم: آمار سفارشات --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-4 h-4 text-pars-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    <p class="text-sm text-gray-500">تعداد سفارشات موفق</p>
                </div>
                <p class="text-gray-800 font-bold text-2xl">
                    {{ english_to_persian_num($user->orders->where('status','paid')->count()) }}
                    <span class="text-sm font-normal text-gray-400">سفارش</span>
                </p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-4 h-4 text-pars-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm text-gray-500">مبلغ پرداختی تا کنون</p>
                </div>
                <p class="text-gray-800 font-bold text-2xl">
                    {{ english_to_persian_num(number_format($user->orders()->where('status','paid')->sum('amount'))) }}
                    <span class="text-sm font-normal text-gray-400">تومان</span>
                </p>
            </div>
        </div>

        {{-- خط جداکننده --}}
        <div class="border-t border-gray-100"></div>

        {{-- ردیف سوم: خروج --}}
        <div class="flex justify-end">
            <button wire:click="logout()"
                    class="cursor-pointer flex items-center gap-2 bg-red-50 hover:bg-red-100 text-red-600 px-5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                خروج از حساب کاربری
            </button>
        </div>
    </div>
</div>