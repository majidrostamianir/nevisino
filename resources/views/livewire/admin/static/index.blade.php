<div>
    <div class="flex flex-wrap w-full justify-between p-4">
        <div class="bg-white shadow rounded p-4 text-center w-full sm:w-fit">
            <h5 class="font-bold text-green-500">مبلغ فروخته شده</h5>
            <h5 class="mt-2">{{ english_to_persian_num(number_format(\App\Models\Order::query()->where('status' , 'paid')->sum('total_price'))) }}</h5>
        </div>
        <div class="bg-white shadow rounded p-4 text-center w-full sm:w-fit">
            <h5 class="font-bold">تعداد کاربران</h5>
            <h5 class="mt-2">{{ english_to_persian_num(\App\Models\User::where('type','client')->count()) }}</h5>
        </div>
        <div class="bg-white shadow rounded p-4 text-center w-full sm:w-fit">
            <h5 class="font-bold text-orange-300">تعداد سفارشات در انتظار <span class="text-xs text-red-400 cursor-pointer" wire:click="cancelOrders()">لغو</span></h5>
            <h5 class="mt-2">{{ english_to_persian_num(\App\Models\Order::query()->where('status' , 'pending')->count()) }}</h5>
        </div>
        <div class="bg-white shadow rounded p-4 text-center w-full sm:w-fit">
            <h5 class="font-bold text-green-300">تعداد سفارشات موفق</h5>
            <h5 class="mt-2">{{ english_to_persian_num(\App\Models\Order::query()->where('status' , 'paid')->count()) }}</h5>
        </div>
        <div class="bg-white shadow rounded p-4 text-center w-full sm:w-fit">
            <h5 class="font-bold text-green-400">تعداد سفارشات در مسیر</h5>
            <h5 class="mt-2">{{ english_to_persian_num(\App\Models\Order::query()->where('shipping_status' , 'shipped')->count()) }}</h5>
        </div>
        <div class="bg-white shadow rounded p-4 text-center w-full sm:w-fit">
            <h5 class="font-bold text-green-500">تعداد سفارشات تحویل داده شده</h5>
            <h5 class="mt-2">{{ english_to_persian_num(\App\Models\Order::query()->where('shipping_status' , 'delivered')->count()) }}</h5>
        </div>
        <div class="bg-white shadow rounded p-4 text-center w-full sm:w-fit">
            <h5 class="font-bold text-gray-500">تعداد سفارشات لغو شده</h5>
            <h5 class="mt-2">{{ english_to_persian_num(\App\Models\Order::query()->where('status' , 'canceled')->count()) }}</h5>
        </div>
    </div>
    <div class="sm:flex">
        <div class="overflow-hidden overflow-x-scroll rounded-lg shadow sm:w-1/2">
            <table class="min-w-full text-right bg-pars-100">
                <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 ">
                        <input wire:model.live.debounce.500ms="query" class="w-32 rounded-2xl bg-white h-6 text-xs px-2" placeholder="جستجوی کوئری ...">
                    </th>
                    <th class="px-4 py-2 ">
                        <input wire:model.live.debounce.500ms="ip" class="w-32 rounded-2xl bg-white h-6 text-xs px-2" placeholder="جستجوی آیپی ...">
                    </th>
                    <th class="px-4 py-2 ">
                        <input wire:model.live.debounce.500ms="user" class="w-32 rounded-2xl bg-white h-6 text-xs px-2" placeholder="جستجوی آیپی ...">
                    </th>
                    <th class="px-4 py-2 ">زمان</th>
                </tr>
                </thead>
                <tbody>
                @foreach($queries as $item)
                    <tr class="odd:bg-white even:bg-gray-100">
                        <td class="px-4 py-2 ">{{ $item->query }}</td>
                        <td class="px-4 py-2 ">{{ $item->ip }}</td>
                        <td class="px-4 py-2 ">{{ \App\Models\User::find($item->user_id)->name ?? \App\Models\User::find($item->user_id)->mobile ?? null }}</td>
                        <td class="px-4 py-2 ">{{ english_to_persian_num(verta($item->created_at)) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="overflow-hidden overflow-x-scroll rounded-lg shadow sm:w-1/2 sm:mr-2">
            <table class="min-w-full text-right bg-pars-100">
                <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 ">شناسه محصول</th>
                    <th class="px-4 py-2 ">نام محصول</th>
                    <th class="px-4 py-2 ">واریانت</th>
                    <th class="px-4 py-2 ">تعداد فروخته شده</th>
                </tr>
                </thead>
                <tbody>
                @foreach($soldProducts as $item)
                    <tr class="odd:bg-white even:bg-gray-100">
                        <td class="px-4 py-2 ">{{ $item->product->id }}</td>
                        <td class="px-4 py-2 ">{{ $item->product->title }}</td>
                        <td class="px-4 py-2 ">
                            {{ $item->variant ? $item->variant->name : '-' }}
                        </td>
                        <td class="px-4 py-2 ">{{ $item->total_sold }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
