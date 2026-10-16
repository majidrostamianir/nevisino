<div x-data="{
        filtersOpen: false,
        isMobile: window.innerWidth < 768
     }"
     @resize.window.debounce.100ms="isMobile = window.innerWidth < 768">
    {{-- ═══ فیلتر وضعیت ═══ --}}
    <div class="mb-4 bg-white rounded-lg shadow">
        {{-- دکمه موبایل برای باز/بسته کردن فیلترها --}}
        <button type="button"
                x-show="isMobile"
                @click="filtersOpen = !filtersOpen"
                class="w-full flex items-center justify-between px-4 py-3 text-sm font-semibold text-gray-700">
            <span class="flex items-center gap-2">
                🎯 فیلتر وضعیت
            </span>
            <svg class="w-5 h-5 transition-transform duration-300"
                 :class="filtersOpen ? 'rotate-180' : ''"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        {{-- محتوای فیلتر با Grid Trick --}}
        <div class="grid transition-[grid-template-rows] duration-300 ease-out"
             :class="(!isMobile || filtersOpen) ? 'grid-rows-[1fr]' : 'grid-rows-[0fr]'">
            <div class="overflow-hidden">
                <div class="flex flex-wrap items-center gap-3 p-4 border-t border-gray-100 md:border-t-0">
                    <button wire:click="toggleAllStatuses"
                            class="cursor-pointer text-xs px-3 py-1.5 rounded-lg bg-pars-500 text-white hover:bg-pars-600 transition-colors">
                        🔄 انتخاب همه / هیچ‌کدام
                    </button>

                    <div class="border-r border-gray-300 h-6"></div>

                    <label class="cursor-pointer flex items-center gap-2 px-3 py-1.5 rounded-lg {{ $statusFilter['pending'] ? 'bg-orange-100' : 'bg-gray-100' }}">
                        <input type="checkbox" wire:model.live="statusFilter.pending" class="rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                        <span class="text-sm font-medium {{ $statusFilter['pending'] ? 'text-orange-700' : 'text-gray-500' }}">🕐 در انتظار</span>
                    </label>

                    <label class="cursor-pointer flex items-center gap-2 px-3 py-1.5 rounded-lg {{ $statusFilter['paid'] ? 'bg-green-100' : 'bg-gray-100' }}">
                        <input type="checkbox" wire:model.live="statusFilter.paid" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                        <span class="text-sm font-medium {{ $statusFilter['paid'] ? 'text-green-700' : 'text-gray-500' }}">✓ پرداخت شده</span>
                    </label>

                    <label class="cursor-pointer flex items-center gap-2 px-3 py-1.5 rounded-lg {{ $statusFilter['shipped'] ? 'bg-blue-100' : 'bg-gray-100' }}">
                        <input type="checkbox" wire:model.live="statusFilter.shipped" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm font-medium {{ $statusFilter['shipped'] ? 'text-blue-700' : 'text-gray-500' }}">🚚 ارسال شده</span>
                    </label>

                    <label class="cursor-pointer flex items-center gap-2 px-3 py-1.5 rounded-lg {{ $statusFilter['delivered'] ? 'bg-emerald-100' : 'bg-gray-100' }}">
                        <input type="checkbox" wire:model.live="statusFilter.delivered" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                        <span class="text-sm font-medium {{ $statusFilter['delivered'] ? 'text-emerald-700' : 'text-gray-500' }}">✓ تحویل شده</span>
                    </label>

                    <label class="cursor-pointer flex items-center gap-2 px-3 py-1.5 rounded-lg {{ $statusFilter['returned'] ? 'bg-red-100' : 'bg-gray-100' }}">
                        <input type="checkbox" wire:model.live="statusFilter.returned" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                        <span class="text-sm font-medium {{ $statusFilter['returned'] ? 'text-red-700' : 'text-gray-500' }}">↩️ عودت شده</span>
                    </label>

                    <label class="cursor-pointer flex items-center gap-2 px-3 py-1.5 rounded-lg {{ $statusFilter['canceled'] ? 'bg-red-100' : 'bg-gray-100' }}">
                        <input type="checkbox" wire:model.live="statusFilter.canceled" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                        <span class="text-sm font-medium {{ $statusFilter['canceled'] ? 'text-red-700' : 'text-gray-500' }}">✗ لغو شده</span>
                    </label>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ جدول ═══ --}}
    <div class="overflow-x-scroll rounded-lg shadow">
        <table class="min-w-full text-right bg-pars-100">
            <thead>
            <tr class="bg-gradient-to-r from-pars-500 to-pars-800 text-white shadow-md">
                <th class="px-4 py-3 text-sm font-semibold">نام</th>
                <th class="px-4 py-3 text-sm font-semibold">مبلغ</th>
                <th class="px-4 py-3 text-sm font-semibold">وضعیت</th>
                <th class="px-4 py-3 text-sm font-semibold">تاریخ ثبت سفارش</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($orders as $order)
                <tr class="border-b border-gray-200 transition-colors hover:bg-gray-50 {{ $loop->even ? 'bg-gray-50/50' : 'bg-white' }}">
                    <td class="px-4 py-3 text-sm text-gray-800">
                        <a href="{{ route('admin.user.order', ['user' => $order->user_id]) }}">
                            <div class="block">{{ $order->user->name ?? '—' }}</div>
                            <div class="block pt-2">{{ english_to_persian_num($order->user->mobile ?? '') }}</div>
                        </a>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-800 font-medium">
                        {{ english_to_persian_num(number_format($order->amount)) }} تومان
                    </td>
                    <td class="px-4 py-3 text-sm">
                        @switch($order->status)
                            @case('pending')
                                @if($order->transactions()->where('payment_gateway', 'card')->where('status','pending')->exists())
                                    <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-blue-100 text-blue-700">در انتظار تایید</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-orange-100 text-orange-700">در انتظار پرداخت</span>
                                @endif
                                @break

                            @case('paid')
                                <div class="flex flex-col gap-1">
                                    <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-green-100 text-green-700">پرداخت شده</span>

                                    @switch($order->shipping_status)
                                        @case('pending')
                                            <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-orange-100 text-orange-700">در انتظار تایید ارسال</span>
                                            @break

                                        @case('processing')
                                            <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-purple-100 text-purple-700">در حال پردازش</span>
                                            @break

                                        @case('preparing')
                                            <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-indigo-100 text-indigo-700">در حال بسته‌بندی</span>
                                            @break

                                        @case('shipped')
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-blue-100 text-blue-700">تحویل پست شده</span>
                                                <a href="https://tracking.post.ir/?id={{ $order->tracking_code }}" target="_blank"
                                                   class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-semibold bg-cyan-100 text-gray-500 hover:bg-cyan-600 shadow-sm transition-all">
                                                    رهگیری
                                                </a>
                                            </div>
                                            @break

                                        @case('delivered')
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-green-100 text-green-700">تحویل مشتری شده</span>
                                                <a href="https://tracking.post.ir/?id={{ $order->tracking_code }}" target="_blank"
                                                   class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-semibold bg-gray-400 text-white hover:bg-gray-500 shadow-sm transition-all">
                                                    مشاهده مسیر
                                                </a>
                                            </div>
                                            @break

                                        @case('returned')
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-red-100 text-red-700">عودت شده به فروشنده</span>
                                                <a href="https://tracking.post.ir/?id={{ $order->tracking_code }}" target="_blank"
                                                   class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-semibold bg-red-600 text-white hover:bg-red-700 shadow-sm transition-all">
                                                    پیگیری عودت
                                                </a>
                                            </div>
                                            @break
                                    @endswitch
                                </div>
                                @break

                            @case('canceled')
                                <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-red-100 text-red-700">✗ لغو شده</span>
                                @break
                        @endswitch
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ english_to_persian_num(verta($order->created_at)) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-12 bg-gray-50">
                        <p class="text-gray-500">هیچ سفارشی یافت نشد</p>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>