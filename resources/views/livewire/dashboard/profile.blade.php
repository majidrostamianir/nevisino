<div>

    <div class="flex w-full">
        <div class="w-1/2">
            <p class="text-sm">نام و نام خانوادگی</p>
            <p class="mt-2 font-bold">{{ $user->name }}</p>
        </div>
        <div class="w-1/2">
            <p class="text-sm">شماره موبایل <span class="text-xs rounded-2xl bg-green-400 px-2 py-0.5 mr-1 text-white">تایید شده</span></p>
            <p class="mt-2 font-bold">{{ english_to_persian_num($user->mobile) }}</p>
        </div>
    </div>

</div>
