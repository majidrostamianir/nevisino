<div>
    <div class="overflow-x-scroll rounded-lg shadow">
        <table class="min-w-full text-right ">
            <thead>
            <tr class="shadow ">
                <th class="px-4 py-2">شناسه</th>
                <th class="px-4 py-2">نام</th>
                <th class="px-4 py-2">شماره موبایل</th>
                <th class="px-4 py-2">تاریخ ثبت سفارش</th>
                <th class="px-4 py-2">وضعیت سفارش</th>
                <th class="px-4 py-2">وضعیت حمل</th>
                <th class="px-4 py-2">مبلغ</th>
                <th class="px-4 py-2">مشاهده</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($orders as $index => $order)
                <tr class="border-b border-b-pars-400 odd:bg-white even:bg-gray-100">
                    <td class="px-4 py-2">{{ $order->id }}</td>
                    <td class="px-4 py-2">{{ $order->user->name }}</td>
                    <td class="px-4 py-2">{{ english_to_persian_num($order->user->mobile) }}</td>
                    <td class="px-4 py-2">{{ english_to_persian_num(verta($order->created_at)) }}</td>
                    <td class="px-4 py-2">
                        @switch($order->status)
                            @case('pending')
                                <span class="text-orange-300">در انتظار پرداخت</span>
                                @break
                            @case('paid')
                                <span class="text-green-500 font-bold">پرداخت شده</span>
                                @break
                            @case('canceled')
                                <span class="text-red-500">لغو شده</span>
                                @break
                        @endswitch
                    </td>
                    <td class="px-4 py-2">
                        @switch($order->shipping_status)
                            @case('pending')
                                <span class="text-orange-300">در انتظار پرداخت</span>
                                @break
                            @case('processing')
                                <span class="text-green-200">در حال پردازش سفارش</span>
                                @break
                            @case('preparing')
                                <span class="text-green-300">در حال بسته بندی</span>
                                @break
                            @case('shipped')
                                <span class="text-green-400">تحویل پست شده</span>
                                @break
                            @case('delivered')
                                <span class="text-green-500 font-bold">تحویل مشتری شده</span>
                                @break
                            @case('returned')
                                <span class="text-red-500">عودت شده</span>
                                @break
                        @endswitch
                    </td>
                    <td class="px-4 py-2">{{ english_to_persian_num(number_format($order->amount)) }}</td>
                    <td class="px-4 py-2"><a href="{{ route('admin.user.order' , ['user'=>$order->user_id]) }}">مشاهده</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {{--        <div class="w-full place-items-center mt-4 mb-2">--}}
        {{--            {{ $orders->links() }}--}}
        {{--        </div>--}}
    </div>
</div>
