<div>
    <div class="overflow-x-scroll rounded-lg shadow">
        <table class="min-w-full text-right bg-pars-100">
            <thead>
            <tr class="bg-gradient-to-r from-pars-500 to-pars-800 text-white shadow-md">
                <th class="px-4 py-3 text-sm font-semibold">شناسه</th>
                <th class="px-4 py-3 text-sm font-semibold">نام</th>
                <th class="px-4 py-3 text-sm font-semibold">مبلغ</th>
                <th class="px-4 py-3 text-sm font-semibold">وضعیت</th>
                <th class="px-4 py-3 text-sm font-semibold">تاریخ ثبت سفارش</th>
                <th class="px-4 py-3 text-sm font-semibold">مشاهده</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($orders as $index => $order)
                <tr class="border-b border-gray-200 transition-colors hover:bg-gray-50 {{ $loop->even ? 'bg-gray-50/50' : 'bg-white' }}">
                    <td class="px-4 py-3 text-sm text-gray-800">{{ english_to_persian_num($order->id) }}</td>
                    <td class="px-4 py-3 text-sm text-gray-800">
                        <div class="block">{{ $order->user->name }}</div>
                    <div class="block pt-2">{{ english_to_persian_num($order->user->mobile) }}</div>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-800 font-medium">{{ english_to_persian_num(number_format($order->amount)) }}
                        تومان
                    </td>
                    <td class="px-4 py-3 text-sm">
                        @switch($order->status)
                            @case('pending')
                                @if($order->transactions()->where('payment_gateway', 'card')->where('status','pending')->exists())
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-blue-100 text-blue-700">
                                        ⏳ در انتظار تایید
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-orange-100 text-orange-700">
                                        🕐 در انتظار پرداخت
                                    </span>
                                @endif
                                @break
                            
                            @case('paid')
                                <div class="flex flex-col gap-1">
                                    <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-green-100 text-green-700">
                                        ✓ پرداخت شده
                                    </span>
                                    
                                    @switch($order->shipping_status)
                                        @case('pending')
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-orange-100 text-orange-700">
                                                ⏳ در انتظار تایید ارسال
                                            </span>
                                            @break
                                        
                                        @case('processing')
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-purple-100 text-purple-700">
                                                🔄 در حال پردازش سفارش
                                            </span>
                                            @break
                                        
                                        @case('preparing')
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-indigo-100 text-indigo-700">
                                                📦 در حال بسته‌بندی
                                            </span>
                                            @break
                                        
                                        @case('shipped')
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span
                                                    class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-blue-100 text-blue-700">
                                                    🚚 تحویل پست شده
                                                </span>
                                                <a href="https://tracking.post.ir/?id={{ $order->tracking_code }}"
                                                   target="_blank"
                                                   class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-semibold bg-cyan-500 text-white hover:bg-cyan-600 shadow-sm transition-all duration-200">
                                                    📍 رهگیری
                                                </a>
                                            </div>
                                            @break
                                        
                                        @case('delivered')
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span
                                                    class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-green-100 text-green-700">
                                                    ✓ تحویل مشتری شده
                                                </span>
                                                <a href="https://tracking.post.ir/?id={{ $order->tracking_code }}"
                                                   target="_blank"
                                                   class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-semibold bg-gray-400 text-white hover:bg-gray-500 shadow-sm transition-all duration-200">
                                                    🔍 مشاهده مسیر
                                                </a>
                                            </div>
                                            @break
                                        
                                        @case('returned')
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span
                                                    class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-red-100 text-red-700">
                                                    ↩️ عودت شده به فروشنده
                                                </span>
                                                <a href="https://tracking.post.ir/?id={{ $order->tracking_code }}"
                                                   target="_blank"
                                                   class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-semibold bg-red-600 text-white hover:bg-red-700 shadow-sm transition-all duration-200">
                                                    📮 پیگیری عودت
                                                </a>
                                            </div>
                                            @break
                                    @endswitch
                                </div>
                                @break
                            
                            @case('canceled')
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-red-100 text-red-700">
                                    ✗ لغو شده
                                </span>
                                @break
                        @endswitch
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ english_to_persian_num(verta($order->created_at)) }}</td>
                    <td class="px-4 py-3 text-sm">
                        <a href="{{ route('admin.user.order' , ['user'=>$order->user_id]) }}"
                           class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium bg-pars-500 text-white hover:bg-pars-600 transition-colors">
                            🔍 مشاهده
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        
        @if($orders->isEmpty())
            <div class="text-center py-12 bg-gray-50 rounded-xl">
                <p class="text-gray-500">هیچ سفارشی یافت نشد</p>
            </div>
        @endif
    </div>
</div>
