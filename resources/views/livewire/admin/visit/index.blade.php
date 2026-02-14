<div>
    <div class="overflow-x-scroll rounded-lg shadow ">
        <table class="min-w-full text-right bg-pars-100">
            <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 ">
                    <input wire:model.live.debounce.500ms="queryIp" class="rounded-2xl bg-white h-8 px-2"
                           placeholder="جستجوی آیپی ...">
                </th>
                <th class="px-4 py-2 ">
                    <input wire:model.live.debounce.500ms="queryUser" class="rounded-2xl bg-white h-8 px-2"
                           placeholder="جستجوی کاربر ...">
                </th>
                <th class="px-4 py-2 ">صفحه</th>
                <th class="px-4 py-2 ">بات</th>
                <th class="px-4 py-2 ">رفرر</th>
                <th class="px-4 py-2 ">ایجنت</th>
                <th class="px-4 py-2 ">زمان</th>
            </tr>
            </thead>
            <tbody>
            @foreach($visits as $item)
                <tr class="odd:bg-white even:bg-gray-100">
                    <td class="px-4 py-2 ">{{ $item->ip }}</td>
                    <td class="px-4 py-2 ">{{ \App\Models\User::find($item->user_id)->name ?? \App\Models\User::find($item->user_id)->mobile ?? null }}</td>
                    <td class="px-4 py-2 ">{{ \App\Models\Url::find($item->url_id)->title ?? $item->url }}</td>
                    <td class="px-4 py-2 ">{{ $item->is_bot }}</td>
                    <td class="px-4 py-2 w-8">{{ $item->referrer }}</td>
                    <td class="px-4 py-2 ">{{ $item->user_agent }}</td>
                    <td class="px-4 py-2 ">{{ english_to_persian_num(verta($item->created_at)) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
