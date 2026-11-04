<div>
    <div class="overflow-x-auto rounded-lg shadow">
        <table class="min-w-full w-full table-fixed text-right bg-pars-100">
            <thead>
            <tr class="bg-gradient-to-r from-pars-500 to-pars-800 text-white shadow-md">
                <th class="px-4 py-3 w-[32%]">
                    <input wire:model.live.debounce.500ms="searchQuery"
                           class="w-full max-w-[200px] rounded-lg border-0 bg-white/20 text-white placeholder-white/70 focus:ring-2 focus:ring-white/50 h-9 px-2 text-sm"
                           placeholder="👤 جستجو ...">
                </th>
                <th class="px-4 py-3 text-sm font-semibold w-[48%] flex">
                    <span>صفحه / صفحه قبل</span>
                    {{-- سوییچ نمایش ربات‌ها --}}
                    <div class="flex pr-8">
                        <label class="inline-flex items-center gap-2 cursor-pointer select-none text-sm text-white">
                            <input type="checkbox"
                                   wire:model.live="showBots"
                                   class="rounded border-gray-300 text-pars-600 focus:ring-pars-500">
                            <span>🤖 نمایش ربات‌ها</span>
                        </label>
                    </div>
                </th>
                <th class="px-4 py-3 text-sm font-semibold w-[20%]">ایجنت</th>
            </tr>
            </thead>
            <tbody>
            @foreach($visits as $v)
                <tr wire:key="visit-{{ $v['id'] }}"
                    class="border-b border-gray-200 transition-colors hover:bg-gray-50 {{ $loop->even ? 'bg-gray-50/50' : 'bg-white' }}">
                    <td class="px-4 py-3 text-sm align-top">
                        <div class="flex flex-col items-start gap-1 min-w-0">
                            @if($v['user'])
                                <a href="{{ $v['user']['href'] }}"
                                   class="max-w-full truncate {{ $v['user']['class'] }} hover:underline">
                                    {{ $v['user']['name'] }}
                                </a>
                            @elseif($v['is_bot'])
                                <span class="inline-flex text-nowrap items-center px-2 py-1 rounded-lg text-xs font-medium bg-red-100 text-red-700">
                                    🤖 بات
                                </span>
                            @else
                                <span class="inline-flex text-nowrap items-center px-2 py-1 rounded-lg text-xs font-medium bg-orange-100 text-orange-500">
                                    👤 مهمان
                                </span>
                            @endif
                            <span class="text-xs text-gray-500 truncate max-w-full">{{ $v['ip'] }}</span>
                            <span class="text-xs font-medium truncate max-w-full">{{ $v['created_at'] }}</span>
                        </div>
                    </td>

                    <td class="px-4 py-3 text-xs align-top">
                        <div class="flex flex-col gap-1 min-w-0">
                            <a target="_blank" rel="noopener" href="{{ $v['current_href'] }}"
                               class="block truncate {{ $v['link_color'] }}"
                               title="{{ $v['current_href'] }}">
                                {{ $v['clean_url'] }}
                            </a>

                            @if($v['has_ref'])
                                <a target="_blank" rel="noopener" href="{{ $v['ref_href'] }}"
                                   class="block truncate text-xs {{ $v['ref_color'] }}"
                                   title="{{ $v['ref_href'] }}">
                                    {{ $v['clean_ref'] }}
                                </a>
                            @else
                                <span class="block text-xs text-gray-400">—</span>
                            @endif
                        </div>
                    </td>

                    <td class="px-4 py-3 text-sm align-top">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="text-xl shrink-0">{{ $v['ua']['icon'] }}</span>
                            <div class="flex flex-col min-w-0">
                                <span class="text-gray-800 font-medium truncate">{{ $v['ua']['browser'] }}</span>
                                <span class="text-xs text-gray-500 truncate">{{ $v['ua']['os'] }}</span>
                            </div>
                        </div>
                        @if(request()->has('debug'))
                            <div class="text-xs text-gray-400 mt-1 truncate" title="{{ $v['ua']['full'] }}">
                                {{ Str::limit($v['ua']['full'], 60) }}
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

    <div class="mt-4">
        {{ $visits->links() }}
    </div>
</div>