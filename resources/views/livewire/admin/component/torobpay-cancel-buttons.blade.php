<div class="flex gap-3">
    @if($canRevert)
        <button
            wire:click="revert"
            wire:loading.attr="disabled"
            class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded-lg transition disabled:opacity-50"
            onclick="return confirm('آیا از برگشت آنی این سفارش اطمینان دارید؟ مبلغ بلافاصله به کاربر برگشت می‌خورد.')"
        >
            <span wire:loading.remove>↩️ برگشت آنی (Revert)</span>
            <span wire:loading>⏳ در حال برگشت...</span>
        </button>
    @endif
    
    @if($canCancel)
        <button
            wire:click="cancel"
            wire:loading.attr="disabled"
            class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition disabled:opacity-50"
            onclick="return confirm('آیا از لغو این سفارش اطمینان دارید؟ مبلغ از طریق پایا به کاربر برگشت می‌خورد.')"
        >
            <span wire:loading.remove>❌ لغو سفارش (Cancel)</span>
            <span wire:loading>⏳ در حال لغو...</span>
        </button>
    @endif
</div>