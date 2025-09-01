<div>
    <div class="sm:flex pt-8">
        <div class="w-7/12 ">
            <div>
                <label class="mr-4 text-sm" for="name">نام و نام خانوادگی:</label>
                <input id="name" class="mt-1 w-full rounded-full px-4">
            </div>
            <div class="mt-4">
                <label class="mr-4 text-sm" for="name">استان:</label>
                <select class="mt-1 w-full rounded-full px-4">
                    @foreach(\App\Models\Province::all() as $province)
                        <option>{{ $province->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mt-4">
                <label class="mr-4 text-sm" for="name">شهر:</label>
                <select class="mt-1 w-full rounded-full px-4">
                    @foreach(\App\Models\Province::all() as $province)
                        <option>{{ $province->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mt-4">
                <label class="mr-4 text-sm" for="address">آدرس کامل و دقیق:</label>
                <input id="address" class="mt-1 w-full rounded-full px-4">
            </div>
            <div class="mt-4">
                <label class="mr-4 text-sm" for="zipCode">کد پستی:</label>
                <input id="zipCode" class="mt-1 w-full rounded-full px-4">
            </div>
            <button class="mt-4 w-full rounded-2xl bg-pars-700 text-white">پرداخت</button>
        </div>
{{--        <div class="w-5/12 bg-gray-400 sm:mr-2">--}}
{{--قیمتا--}}
{{--        </div>--}}
    </div>
</div>
