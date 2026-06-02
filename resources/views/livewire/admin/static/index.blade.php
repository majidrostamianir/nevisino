<div>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 p-4">
        {{-- مبلغ فروخته شده --}}
        <div class="bg-white shadow rounded p-4 text-center">
            <h5 class="font-bold text-green-500">مبلغ فروخته شده</h5>
            <h5 class="mt-2">{{ english_to_persian_num(number_format(\App\Models\Order::query()->where('status' , 'paid')->sum('total_price'))) }}</h5>
        </div>
        
        {{-- تعداد کاربران --}}
        <div class="bg-white shadow rounded p-4 text-center">
            <h5 class="font-bold">تعداد کاربران</h5>
            <h5 class="mt-2">{{ english_to_persian_num(\App\Models\User::where('type','client')->count()) }}</h5>
        </div>
        
        {{-- تعداد سفارشات در انتظار --}}
        <div class="bg-white shadow rounded p-4 text-center">
            <h5 class="font-bold text-orange-300">
                تعداد سفارشات در انتظار
                <span class="text-xs text-red-400 cursor-pointer" wire:click="cancelOrders()">لغو</span>
            </h5>
            <h5 class="mt-2">{{ english_to_persian_num(\App\Models\Order::query()->where('status' , 'pending')->count()) }}</h5>
        </div>
        
        {{-- تعداد سفارشات موفق --}}
        <div class="bg-white shadow rounded p-4 text-center">
            <h5 class="font-bold text-green-300">تعداد سفارشات موفق</h5>
            <h5 class="mt-2">{{ english_to_persian_num(\App\Models\Order::query()->where('status' , 'paid')->count()) }}</h5>
        </div>
        
        {{-- تعداد سفارشات در مسیر --}}
        <div class="bg-white shadow rounded p-4 text-center">
            <h5 class="font-bold text-green-400">تعداد سفارشات در مسیر</h5>
            <h5 class="mt-2">{{ english_to_persian_num(\App\Models\Order::query()->where('shipping_status' , 'shipped')->count()) }}</h5>
        </div>
        
        {{-- تعداد سفارشات تحویل داده شده --}}
        <div class="bg-white shadow rounded p-4 text-center">
            <h5 class="font-bold text-green-500">تعداد سفارشات تحویل داده شده</h5>
            <h5 class="mt-2">{{ english_to_persian_num(\App\Models\Order::query()->where('shipping_status' , 'delivered')->count()) }}</h5>
        </div>
        
        {{-- تعداد سفارشات لغو شده --}}
        <div class="bg-white shadow rounded p-4 text-center">
            <h5 class="font-bold text-gray-500">تعداد سفارشات لغو شده</h5>
            <h5 class="mt-2">{{ english_to_persian_num(\App\Models\Order::query()->where('status' , 'canceled')->count()) }}</h5>
        </div>
        
        {{-- تعداد کل اقلام موجود --}}
        <div class="bg-white shadow rounded p-4 text-center">
            <h5 class="font-bold text-gray-500">تعداد کل اقلام موجود</h5>
            <h5 class="mt-2">{{ english_to_persian_num(number_format($total_stock)) }}</h5>
        </div>
        
        {{-- قیمت کل اقلام موجود --}}
        <div class="bg-white shadow rounded p-4 text-center">
            <h5 class="font-bold text-gray-500">قیمت کل اقلام موجود</h5>
            <h5 class="mt-2">{{ english_to_persian_num(number_format($total_value)) }} تومان</h5>
        </div>
    </div>

    {{-- جداول با استایل مشابه جدول بازدیدها --}}
    <div class="sm:flex gap-4 mt-4">
        {{-- جدول کوئری‌ها --}}
        <div class="overflow-x-scroll rounded-lg shadow sm:w-1/2">
            <table class="min-w-full text-right bg-pars-100">
                <thead>
                <tr class="bg-gradient-to-r from-pars-500 to-pars-800 text-white shadow-md">
                    <th class="px-4 py-3">
                        <input wire:model.live.debounce.500ms="query"
                               class="rounded-lg border-0 bg-white/20 text-white placeholder-white/70 focus:ring-2 focus:ring-white/50 h-9 px-3 text-sm w-full"
                               placeholder="🔍 جستجوی کوئری ...">
                    </th>
                    <th class="px-4 py-3">
                        <input wire:model.live.debounce.500ms="ip"
                               class="rounded-lg border-0 bg-white/20 text-white placeholder-white/70 focus:ring-2 focus:ring-white/50 h-9 px-3 text-sm w-full"
                               placeholder="🔍 جستجوی آیپی ...">
                    </th>
                    <th class="px-4 py-3">
                        <input wire:model.live.debounce.500ms="user"
                               class="rounded-lg border-0 bg-white/20 text-white placeholder-white/70 focus:ring-2 focus:ring-white/50 h-9 px-3 text-sm w-full"
                               placeholder="👤 جستجوی کاربر ...">
                    </th>
                    <th class="px-4 py-3 text-sm font-semibold">زمان</th>
                </tr>
                </thead>
                <tbody>
                @foreach($queries as $item)
                    <tr class="border-b border-gray-200 transition-colors hover:bg-gray-50 {{ $loop->even ? 'bg-gray-50/50' : 'bg-white' }}">
                        <td class="px-4 py-3 text-sm text-gray-800">{{ $item->query }}</td>
                        <td class="px-4 py-3 text-sm text-gray-800">{{ english_to_persian_num($item->ip) }}</td>
                        <td class="px-4 py-3 text-sm">
                            @php $user = \App\Models\User::find($item->user_id); @endphp
                            @if($user)
                                <span class="{{ $user->mobile_verified_at ? 'text-emerald-400 font-semibold' : 'text-gray-600' }}">
                                    {{ $user->name ?? english_to_persian_num($user->mobile) ?? '—' }}
                                </span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ english_to_persian_num(verta($item->created_at)) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            @if($queries->isEmpty())
                <div class="text-center py-12 bg-gray-50 rounded-xl">
                    <p class="text-gray-500">هیچ کوئری یافت نشد</p>
                </div>
            @endif
        </div>

        {{-- جدول محصولات فروخته شده --}}
        <div class="overflow-x-scroll rounded-lg shadow sm:w-1/2 sm:mr-2">
            <table class="min-w-full text-right bg-pars-100">
                <thead>
                <tr class="bg-gradient-to-r from-pars-500 to-pars-800 text-white shadow-md">
                    <th class="px-4 py-3 text-sm font-semibold">شناسه محصول</th>
                    <th class="px-4 py-3 text-sm font-semibold">نام محصول</th>
                    <th class="px-4 py-3 text-sm font-semibold">واریانت</th>
                    <th class="px-4 py-3 text-sm font-semibold">تعداد فروخته شده</th>
                </tr>
                </thead>
                <tbody>
                @foreach($soldProducts as $item)
                    <tr class="border-b border-gray-200 transition-colors hover:bg-gray-50 {{ $loop->even ? 'bg-gray-50/50' : 'bg-white' }}">
                        <td class="px-4 py-3 text-sm text-gray-800">{{ $item->product->id }}</td>
                        <td class="px-4 py-3 text-sm text-gray-800">{{ $item->product->title }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            {{ $item->variant ? $item->variant->name : '-' }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-800 font-medium">{{ english_to_persian_num($item->total_sold) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            @if($soldProducts->isEmpty())
                <div class="text-center py-12 bg-gray-50 rounded-xl">
                    <p class="text-gray-500">هیچ محصولی فروخته نشده است</p>
                </div>
            @endif
        </div>
    </div>
</div>
