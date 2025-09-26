<div>
    <div class="w-full sm:w-96 bg-pars-100 p-4 rounded-2xl mx-auto text-center shadow">
        <div class="mb-8">
            {!! nl2br(e(english_to_persian_num($message))) !!}
        </div>
        @if($data)
            <div class="mb-8 space-y-2 text-right">
                <p class="flex items-center justify-between">
                    <span>وضعیت تراکنش</span>
                    <span class="flex-1 border-b border-dotted border-gray-400 mx-2"></span>
                    <span class="font-bold text-green-400">موفق</span>
                </p>
                <p class="flex items-center justify-between">
                    <span>شماره سفارش</span>
                    <span class="flex-1 border-b border-dotted border-gray-400 mx-2"></span>
                    <span class="font-bold">{{english_to_persian_num( $data['order_number']) }}</span>
                </p>
                <p class="flex items-center justify-between">
                    <span>شماره تراکنش</span>
                    <span class="flex-1 border-b border-dotted border-gray-400 mx-2"></span>
                    <span class="font-bold ">{{ english_to_persian_num($data['authority']) }}</span>
                </p>
                <p class="flex items-center justify-between">
                    <span>مبلغ سفارش</span>
                    <span class="flex-1 border-b border-dotted border-gray-400 mx-2"></span>
                    <span class="font-bold ">{{ number_format(english_to_persian_num($data['total_price'])) }}</span>
                </p>
                <p class="flex items-center justify-between">
                    <span>حمل و نقل</span>
                    <span class="flex-1 border-b border-dotted border-gray-400 mx-2"></span>
                    <span class="font-bold ">{{ number_format(english_to_persian_num($data['shipping_price'])) }}</span>
                </p>
                <p class="flex items-center justify-between">
                    <span>مبلغ کل پرداختی</span>
                    <span class="flex-1 border-b border-dotted border-gray-400 mx-2"></span>
                    <span class="font-bold ">{{ number_format(english_to_persian_num($data['amount'])) }}</span>
                </p>
            </div>
        @endif
        <div>
            <a href="/dashboard/order" wire:navigate
                class="bg-pars-700 hover:bg-pars-800 rounded-2xl text-white py-1 px-4 cursor-pointer">مشاهده سفارشات</a>
        </div>
    </div>
</div>
