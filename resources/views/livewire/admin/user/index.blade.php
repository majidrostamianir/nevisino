<div>
    <div class="overflow-hidden rounded-lg shadow">
        <table class="min-w-full text-right bg-pars-100">
            <thead>
            <tr class="shadow">
                <th class="px-4 py-2">شناسه</th>
                <th class="px-4 py-2">نام</th>
                <th class="px-4 py-2">شماره موبایل</th>
                <th class="px-4 py-2">تاریخ ثبت نام</th>
                <th class="px-4 py-2">ورود از</th>
                <th class="px-4 py-2">سفارشات</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($users as $index => $user)
                <tr class="">
                    <td class="px-4 py-2">{{ $user->id }}</td>
                    <td class="px-4 py-2">{{ $user->name }}</td>
                    <td class="px-4 py-2">{{ english_to_persian_num($user->mobile) }}</td>
                    <td class="px-4 py-2">{{ english_to_persian_num(verta($user->created_at)) }}</td>
                    <td class="px-4 py-2">{{ $user->referre }}</td>
                    <td class="px-4 py-2">
                        <a href="{{ route('admin.user.order' , ['user' => $user]) }}"> مشاهده</a>
                    </td>

                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="w-full place-items-center mt-4 mb-2">
            {{ $users->links() }}
        </div>
    </div></div>
