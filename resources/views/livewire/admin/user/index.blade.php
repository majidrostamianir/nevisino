<div>
    <div class="overflow-x-scroll rounded-lg shadow">
        <table class="min-w-full text-right bg-pars-100">
            <thead>
            <tr class="shadow">
                <th class="px-4 py-2">شناسه</th>
                <th class="px-4 py-2">
                    <input wire:model.live="queryName" class="rounded-2xl bg-white h-8 px-2" placeholder="جستجوی نام ...">
                </th>
                <th class="px-4 py-2">
                    <input wire:model.live="queryMobile" class="rounded-2xl bg-white h-8 px-2" placeholder="جستجوی موبایل ...">

                </th>
                <th class="px-4 py-2">تاریخ ثبت نام</th>
                <th class="px-4 py-2">ورود از</th>
                <th class="px-4 py-2">سفارشات</th>
                <th class="px-4 py-2">لاگین</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($users as $index => $user)
                <tr class="odd:bg-white even:bg-gray-100">
                    <td class="px-4 py-2">{{ $user->id }}</td>
                    <td class="px-4 py-2">{{ $user->name }}</td>
                    <td class="px-4 py-2">{{ english_to_persian_num($user->mobile) }}</td>
                    <td class="px-4 py-2">{{ english_to_persian_num(verta($user->created_at)) }}</td>
                    <td class="px-4 py-2">{{ $user->referrer }}</td>
                    <td class="px-4 py-2">
                        <a href="{{ route('admin.user.order' , ['user' => $user]) }}"> مشاهده({{ $user->orders()->count() }})</a>
                    </td>
                    <td class="px-4 py-2">
                        <span class="cursor-pointer" wire:click="loginUser('{{ $user->id }}')">ورود</span>
                    </td>

                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
