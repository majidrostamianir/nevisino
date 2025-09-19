<div>
    <div class="overflow-hidden rounded-lg shadow">
        <table class="min-w-full text-right bg-pars-100">
            <thead>
            <tr class="shadow bg-pars-400">
                <th class="px-4 py-2">شناسه</th>
                <th class="px-4 py-2">نام</th>
                <th class="px-4 py-2">شماره موبایل</th>
                <th class="px-4 py-2">تاریخ ثبت سفارش</th>
                <th class="px-4 py-2">وضعیت سفارش</th>
                <th class="px-4 py-2">وضعیت حمل</th>
                <th class="px-4 py-2">مبلغ</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($orders as $index => $order)
                <tr class="border-b border-b-pars-400 odd:bg-pars-100 even:bg-pars-2رربق00">
                    <td class="px-4 py-2">{{ $order->id }}</td>
                    <td class="px-4 py-2">{{ $order->user->name }}</td>
                    <td class="px-4 py-2">{{ english_to_persian_num($order->user->mobile) }}</td>
                    <td class="px-4 py-2">{{ english_to_persian_num(verta($order->created_at)) }}</td>
                    <td class="px-4 py-2">
                        @switch($order->status)
                            @case('pending')
                                <span class="text-orange-500">در انتظار پرداخت</span>
                                @break
                            @case('paid')
                                <span class="text-green-500">پرداخت شده</span>
                                @break
                            @case('pending')
                                <span class="text-red-500">لغو شده</span>
                                @break
                        @endswitch
                    </td>
                    <td class="px-4 py-2">
                        @switch($order->shipping_status)
                            @case('pending')
                                <span class="text-orange-500">در انتظار پرداخت</span>
                                @break
                            @case('processing')
                                <span class="text-green-500">در حال پردازش سفارش</span>
                                @break
                            @case('preparing')
                                <span class="text-red-500">در حال بسته بندی</span>
                                @break
                            @case('shipped')
                                <span class="text-red-500">تحویل پست شده</span>
                                @break
                            @case('delivered')
                                <span class="text-red-500">تحویل مشتری شده</span>
                                @break
                            @case('returned')
                                <span class="text-red-500">عودت شده</span>
                                @break
                        @endswitch
                    </td>
                    <td class="px-4 py-2">{{ english_to_persian_num(number_format($order->amount)) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {{--        <div class="w-full place-items-center mt-4 mb-2">--}}
        {{--            {{ $orders->links() }}--}}
        {{--        </div>--}}
    </div>
</div>
