<div>
    @if($canSettle)
        <button
            wire:click="settle"
            wire:loading.attr="disabled"
            class="cursor-pointer bg-orange-400 text-white font-bold py-2 px-4 rounded-lg transition disabled:opacity-50"
        >
            <span wire:loading.remove class="flex">
                <img src="{{ asset('images/torobpay.png') }}" class="h-6 w-auto ml-4">
                تسویه نهایی با ترب پی
            </span>
            <span wire:loading>⏳ در حال تسویه...</span>
        </button>
    @else
        <div class="text-gray-500 text-sm">
            @if($order->torobpay_status)
                <span class="bg-gray-400 text-white font-bold py-2 px-4 rounded-lg transition disabled:opacity-50">
                وضعیت فعلی: {{ $order->torobpay_status->toSimpleStatus() }}
                </span>
            @else
                <span class="bg-gray-400 text-white font-bold py-2 px-4 rounded-lg transition disabled:opacity-50">
 این سفارش با ترب پی پرداخت نشده است
                </span>
            @endif
        </div>
    @endif
</div>