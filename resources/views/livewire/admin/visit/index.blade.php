<div>
    <div class="overflow-x-scroll rounded-lg shadow">
        <table class="min-w-full text-right bg-pars-100">
            <thead>
            <tr class="bg-gradient-to-r from-pars-500 to-pars-800 text-white shadow-md">
                <th class="px-4 py-3">
                    <input wire:model.live.debounce.500ms="searchQuery"
                           class="w-fit rounded-lg border-0 bg-white/20 text-white placeholder-white/70 focus:ring-2 focus:ring-white/50 h-9 px-1 text-sm"
                           placeholder="👤 جستجو ...">
                </th>
                <th class="px-4 py-3 text-sm font-semibold">صفحه / صفحه قبل</th>
                <th class="px-4 py-3 text-sm font-semibold">ایجنت</th>
            </tr>
            </thead>
            <tbody>
            @foreach($visits as $v)
                <tr wire:key="visit-{{ $v['id'] }}"
                    class="border-b border-gray-200 transition-colors hover:bg-gray-50 {{ $loop->even ? 'bg-gray-50/50' : 'bg-white' }}">
                    <td class="px-4 py-3 text-sm">
                        <div class="flex flex-col items-start gap-1">
                            @if($v['user'])
                                <a href="{{ $v['user']['href'] }}"
                                   class="{{ $v['user']['class'] }} hover:underline">
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
                            <span class="text-xs text-gray-500">{{ $v['ip'] }}</span>
                            <span class="text-xs font-medium">{{ $v['created_at'] }}</span>
                        </div>
                    </td>

                    <td class="px-4 py-3 text-xs">
                        <div class="flex flex-col gap-1 max-w-[320px]">
                            <a target="_blank" href="{{ $v['current_href'] }}"
                               class="block  whitespace-nowrap {{ $v['link_color'] }}"
                               title="{{ $v['current_href'] }}">
                                {{ $v['clean_url'] }}
                            </a>

                            @if($v['has_ref'])
                                <a target="_blank" href="{{ $v['ref_href'] }}"
                                   class="block  whitespace-nowrap text-xs {{ $v['ref_color'] }}"
                                   title="{{ $v['ref_href'] }}">
                                    {{ $v['clean_ref'] }}
                                </a>
                            @else
                                <span class="block  whitespace-nowrap text-xs text-gray-400">—</span>
                            @endif
                        </div>
                    </td>

                    <td class="px-4 py-3 text-sm">
                        <div class="flex items-center gap-2">
                            <span class="text-xl">{{ $v['ua']['icon'] }}</span>
                            <div class="flex flex-col">
                                <span class="text-gray-800 font-medium">{{ $v['ua']['browser'] }}</span>
                                <span class="text-xs text-gray-500">{{ $v['ua']['os'] }}</span>
                            </div>
                        </div>
                        @if(request()->has('debug'))
                            <div class="text-xs text-gray-400 mt-1  max-w-[200px]" title="{{ $v['ua']['full'] }}">
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