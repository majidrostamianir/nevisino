<div class="bg-white rounded shadow p-4">
    <div class="flex w-full" >
        <div class="w-1/2">
            <p class="text-sm">نام و نام خانوادگی</p>
            <p class="mt-2 font-bold">{{ $user->name }}</p>
        </div>
        <div class="w-1/2">
            <p class="text-sm">شماره موبایل <span class="text-xs rounded-2xl bg-green-400 px-2 py-0.5 mr-1 text-white">تایید شده</span></p>
            <p class="mt-2 font-bold">{{ english_to_persian_num($user->mobile) }}</p>
        </div>
    </div>
    <div class="flex w-full mt-6" >
        <div class="w-1/2">
            <p class="text-sm">تعداد سفارشات موفق</p>
            <p class="mt-2 font-bold">{{ english_to_persian_num($user->orders->where('status','paid')->count())  }} سفارش</p>
        </div>
        <div class="w-1/2">
            <p class="text-sm">مبلغ پرداختی تا کنون </p>
            <p class="mt-2 font-bold">{{ english_to_persian_num(number_format($user->orders()->where('status','paid')->sum('amount'))) }} تومان </p>
        </div>
    </div>
    <div class="flex w-full mt-6" >
        <div class="w-1/2">
            <p class="text-sm">حساب کاربری</p>
            <p class="mt-2 font-bold text-red-500 cursor-pointer" wire:click="logout()">خروج</p>
        </div>
        <div class="w-1/2">
{{--            <p class="text-sm">مبلغ پرداختی تا کنون </p>--}}
{{--            <p class="mt-2 font-bold">{{ english_to_persian_num(number_format($user->orders()->where('status','paid')->sum('amount'))) }} تومان </p>--}}
        </div>
    </div>

</div>
