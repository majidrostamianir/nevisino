<div>
    <div class="overflow-x-scroll rounded-lg shadow">
        <table class="min-w-full text-right bg-pars-100">
            <thead>
            <tr class="bg-gradient-to-r from-pars-500 to-pars-800 text-white shadow-md">
                <th class="px-4 py-3 text-sm font-semibold">شناسه</th>
                <th class="px-4 py-3">
                    <input wire:model.live="queryName"
                           class="w-fit rounded-lg border-0 bg-white/20 text-white placeholder-white/70 focus:ring-2 focus:ring-white/50 h-9 px-3 text-sm"
                           placeholder="🔍 جستجوی نام ...">
                </th>
                <th class="px-4 py-3">
                    <input wire:model.live="queryMobile"
                           class="w-fit rounded-lg border-0 bg-white/20 text-white placeholder-white/70 focus:ring-2 focus:ring-white/50 h-9 px-3 text-sm"
                           placeholder="📱 جستجوی موبایل ...">
                </th>
                <th class="px-4 py-3 text-sm font-semibold">تاریخ ثبت نام</th>
                <th class="px-4 py-3 text-sm font-semibold">تعداد otp</th>
                <th class="px-4 py-3 text-sm font-semibold">سبد خرید</th>
                <th class="px-4 py-3 text-sm font-semibold">سفارشات</th>
                <th class="px-4 py-3 text-sm font-semibold">لاگین</th>
                <th class="px-4 py-3 text-sm font-semibold">رفرر</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($users as $index => $user)
                <tr class="border-b border-gray-200 transition-colors hover:bg-gray-50 {{ $loop->even ? 'bg-gray-50/50' : 'bg-white' }}">
                    <td class="px-4 py-3 text-sm text-gray-700 font-medium">{{ english_to_persian_num( $user->id ) }}</td>
                    <td class="px-4 py-3 text-sm text-gray-800">{{ $user->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-sm">
                            <span
                                class="{{ $user->mobile_verified_at ? 'text-emerald-400 font-semibold' : 'text-gray-600' }}">
                                {{ english_to_persian_num($user->mobile) }}
                            </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ english_to_persian_num(verta($user->created_at)) }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">
                        {{ english_to_persian_num($user->mobile_otp_sent_count) }}
                        <span class="text-xs text-red-500 cursor-pointer" wire:click.prevent="resetMobileOtpSentCount({{ $user->id }})">reset</span>
                    </td>
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.user.cart' , ['user' => $user]) }}"
                           class="inline-flex items-center text-2xl relative">
                            🛒
                                @if($user->cart && $user->cart->items->count() > 0)
                                <span class="text-green-500  absolute -top-2 -left-2 text-sm">
                                        {{ english_to_persian_num($user->cart->items->sum('quantity')) }}
                                    </span>
                                @endif
                        </a>
                    </td>
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.user.order' , ['user' => $user]) }}"
                           class="inline-flex items-center  text-2xl relative">
                            📦
                            <span class="text-black absolute -top-2 -left-2 text-sm">
                                {{ english_to_persian_num($user->orders()->count()) }}
                            </span>
                            <span class="text-green-500 absolute bottom-0 -left-2 text-sm">
                                {{ english_to_persian_num($user->orders()->where('status','paid')->count()) }}
                            </span>
                        </a>
                    </td>
                    <td class="px-4 py-3">
                        <button wire:click="loginUser('{{ $user->id }}')"
                                class="inline-flex items-center text-nowrap cursor-pointer gap-1 px-3 py-1.5 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow">
                            🔑 ورود
                        </button>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600 max-w-[200px] overflow-hidden whitespace-nowrap" dir="ltr" title="{{ $user->referrer }}">
                        {{ $user->referrer ?? '—' }}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    @if($users->isEmpty())
        <div class="text-center py-12 bg-gray-50 rounded-xl mt-4">
            <p class="text-gray-500">هیچ کاربری یافت نشد</p>
        </div>
    @endif
</div>
