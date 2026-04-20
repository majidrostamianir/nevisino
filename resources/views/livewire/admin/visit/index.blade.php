<div>
    <div class="overflow-x-scroll rounded-lg shadow">
        <table class="min-w-full text-right bg-pars-100">
            <thead>
            <tr class="bg-gradient-to-r from-pars-500 to-pars-800 text-white shadow-md">
                <th class="px-4 py-3 text-sm font-semibold">زمان</th>
                <th class="px-4 py-3">
                    <input wire:model.live.debounce.500ms="queryIp"
                           class="w-fit rounded-lg border-0 bg-white/20 text-white placeholder-white/70 focus:ring-2 focus:ring-white/50 h-9 px-3 text-sm"
                           placeholder="🔍 جستجوی آیپی ...">
                </th>
                <th class="px-4 py-3">
                    <input wire:model.live.debounce.500ms="queryUser"
                           class="w-fit rounded-lg border-0 bg-white/20 text-white placeholder-white/70 focus:ring-2 focus:ring-white/50 h-9 px-3 text-sm"
                           placeholder="👤 جستجوی کاربر ...">
                </th>
                <th class="px-4 py-3 text-sm font-semibold">صفحه</th>
                <th class="px-4 py-3 text-sm font-semibold">صفحه قبل</th>
                <th class="px-4 py-3 text-sm font-semibold">بات</th>
                <th class="px-4 py-3 text-sm font-semibold">ایجنت</th>
            </tr>
            </thead>
            <tbody>
            @foreach($visits as $item)
                <tr class="border-b border-gray-200 transition-colors hover:bg-gray-50 {{ $loop->even ? 'bg-gray-50/50' : 'bg-white' }}">
                    <td class="px-4 py-3 text-sm text-gray-600">{{ english_to_persian_num(verta($item->created_at)) }}</td>
                    <td class="px-4 py-3 text-sm text-gray-800">{{ english_to_persian_num($item->ip )  }}</td>
                    <td class="px-4 py-3 text-sm">
                        @php
                            $user = \App\Models\User::find($item->user_id);
                        @endphp
                        @if($user)
                            <span class="{{ $user->mobile_verified_at ? 'text-emerald-400 font-semibold' : 'text-gray-600' }}">
                                {{ $user->name ?? english_to_persian_num($user->mobile ) ?? '—' }}
                            </span>
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-800">
                        <a target="_blank" href="/{{  $item->url }}">{{  $item->url }}</a>

                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600 max-w-[200px] overflow-hidden whitespace-nowrap" dir="ltr" title="{{ $item->referrer }}">
                        {{ $item->referrer ?? '—' }}
                    </td>
                    <td class="px-4 py-3 text-sm">
                        @if($item->is_bot)
                            <span class="inline-flex text-nowrap items-center px-2 py-1 rounded-lg text-xs font-medium bg-red-100 text-red-700">🤖 بله</span>
                        @else
                            <span class="inline-flex text-nowrap items-center px-2 py-1 rounded-lg text-xs font-medium bg-green-100 text-green-700">✓ خیر</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm">
                        @php $ua = \App\Helpers\UserAgentParser::parse($item->user_agent); @endphp
                        <div class="flex items-center gap-2">
                            <span class="text-xl">{{ $ua['icon'] }}</span>
                            <div class="flex flex-col">
                                <span class="text-gray-800 font-medium">{{ $ua['browser'] }}</span>
                                <span class="text-xs text-gray-500">{{ $ua['os'] }}</span>
                            </div>
                        </div>
                        @if(request()->has('debug'))
                            <div class="text-xs text-gray-400 mt-1 truncate max-w-[200px]" title="{{ $ua['full'] }}">
                                {{ Str::limit($ua['full'], 60) }}
                            </div>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    @if($visits->isEmpty())
        <div class="text-center py-12 bg-gray-50 rounded-xl mt-4">
            <p class="text-gray-500">هیچ بازدیدی یافت نشد</p>
        </div>
    @endif
</div>
