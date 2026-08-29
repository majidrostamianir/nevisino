<div class="h-[86vh] w-full bg-pars-100 rounded-2xl shadow-md overflow-y-auto">
    {{-- جدول داده‌ها با تم جدید --}}
    <div class="rounded-xl shadow-lg overflow-hidden">
        <table class="min-w-full text-right bg-pars-100">
            <thead>
            <tr class="bg-gradient-to-r from-pars-500 to-pars-800 text-white shadow-md">
                <th class="px-4 py-3 text-sm font-semibold">#</th>
                <th class="px-4 py-3 text-sm font-semibold">عنوان</th>
                <th class="px-4 py-3 text-sm font-semibold">وضعیت منو</th>
                <th class="px-4 py-3 text-sm font-semibold">ایندکس</th>
                <th class="px-4 py-3 text-sm font-semibold">فالو</th>
                <th class="px-4 py-3 text-sm font-semibold">محصولات</th>
                <th class="px-4 py-3 text-sm font-semibold">مقالات</th>
            </tr>
            </thead>
            <tbody>
            @foreach($urls as $index => $value)
                <tr wire:key="{{ $value->id }}"
                    class="border-b border-gray-200 transition-colors hover:bg-gray-50 {{ $loop->even ? 'bg-gray-50/50' : 'bg-white' }}">
                    <td class="px-4 py-3 text-sm text-gray-600">{{ english_to_persian_num($index + 1) }}</td>
                    <td class="px-4 py-3 text-sm">
                        <span class="cursor-pointer hover:text-pars-600 transition-colors font-medium
                            @if($value->in_menu) text-pars-700 @else text-pars-500 @endif"
                              wire:click.prevent="setUrl({{ $value->id }})">
                            {{ $value->title_tag }}
                        </span>
                        <a target="_blank" href="{{ route('category-page' , ['dashed' => $value->dashed_url]) }}"
                           class="inline-flex items-center text-yellow-600 hover:text-yellow-700 transition-colors mr-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                                <path d="M18 13v6a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                                <polyline points="15 3 21 3 21 9"/>
                                <line x1="10" y1="14" x2="21" y2="3"/>
                            </svg>
                        </a>
                    </td>
                    <td class="px-4 py-3">
                        @if($value->in_menu)
                            <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-green-100 text-green-700">✓ در منو</span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-gray-100 text-gray-600">✗ خارج از منو</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 cursor-pointer" wire:click="toggleIndexing('{{ $value->id }}')">
                        @if($value->indexing)
                            <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-green-100 text-green-700 cursor-pointer hover:bg-green-200 transition-colors">✓ ایندکس</span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-gray-100 text-gray-600 cursor-pointer hover:bg-gray-200 transition-colors">✗ نو ایندکس</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 cursor-pointer" wire:click="toggleFollowing('{{ $value->id }}')">
                        @if($value->following)
                            <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-green-100 text-green-700 cursor-pointer hover:bg-green-200 transition-colors">✓ فالو</span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-gray-100 text-gray-600 cursor-pointer hover:bg-gray-200 transition-colors">✗ نو فالو</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.url.product' , ['url'=>$value->id]) }}"
                           class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium bg-pars-500 text-white hover:bg-pars-600 transition-colors">
                            مدیریت محصولات
                        </a>
                    </td>
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.url.article' , ['url'=>$value->id]) }}"
                           class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium bg-pars-500 text-white hover:bg-pars-600 transition-colors">
                            مدیریت مقاله
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        @if($urls->isEmpty())
            <div class="text-center py-12 bg-gray-50">
                <p class="text-gray-500">هیچ مقاله‌ای یافت نشد</p>
            </div>
        @endif
    </div>
</div>
