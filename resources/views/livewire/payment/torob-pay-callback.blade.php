<div>
    <div class="w-full sm:w-96 bg-pars-100 p-4 rounded-2xl mx-auto text-center shadow">
        @if($success)
            <div class="mb-6">
                <span class="text-green-500 text-lg font-bold">پرداخت اقساطی با موفقیت انجام شد</span>
            </div>
            <div class="mb-8 space-y-2 text-right">
                <p class="flex items-center justify-between">
                    <span>وضعیت تراکنش</span>
                    <span class="flex-1 border-b border-dotted border-gray-400 mx-2"></span>
                    <span class="font-bold text-green-400">موفق</span>
                </p>
                <p class="flex items-center justify-between">
                    <span>شماره سفارش</span>
                    <span class="flex-1 border-b border-dotted border-gray-400 mx-2"></span>
                    <span class="font-bold">{{ english_to_persian_num($data['order_number']) }}</span>
                </p>
                <p class="flex items-center justify-between">
                    <span>مبلغ سفارش</span>
                    <span class="flex-1 border-b border-dotted border-gray-400 mx-2"></span>
                    <span class="font-bold">{{ english_to_persian_num(number_format($data['total_price'])) }} تومان</span>
                </p>
                <p class="flex items-center justify-between">
                    <span>حمل و نقل</span>
                    <span class="flex-1 border-b border-dotted border-gray-400 mx-2"></span>
                    <span class="font-bold">{{ english_to_persian_num(number_format($data['shipping_price'])) }} تومان</span>
                </p>
                <p class="flex items-center justify-between">
                    <span>مبلغ کل پرداختی</span>
                    <span class="flex-1 border-b border-dotted border-gray-400 mx-2"></span>
                    <span class="font-bold">{{ english_to_persian_num(number_format($data['amount'])) }} تومان</span>
                </p>
            </div>
        @else
            <div class="mb-6">
                <span class="text-red-500 text-lg font-bold">پرداخت ناموفق بود</span>
                <p class="text-gray-500 text-sm mt-2">در صورت کسر وجه، مبلغ حداکثر تا ۷۲ ساعت عودت داده می‌شود</p>
            </div>
        @endif
        <div>
            <a href="/dashboard/order"
               wire:navigate
               class="bg-pars-700 hover:bg-pars-800 rounded-2xl text-white py-1 px-4 cursor-pointer">
                مشاهده سفارشات
            </a>
        </div>
    </div>
</div>