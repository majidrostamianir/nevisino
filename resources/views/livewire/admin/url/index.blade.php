@push('editor')
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill-rtl@1.0.0/dist/quill-rtl.min.js"></script>

    <style>
        .ql-editor {
            direction: rtl;
            text-align: right;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 200px;
        }
        .ql-toolbar.ql-snow {
            direction: rtl;
            border-radius: 0.75rem 0.75rem 0 0;
        }
        .ql-container.ql-snow {
            border-radius: 0 0 0.75rem 0.75rem;
        }
        .ql-toolbar.ql-snow .ql-formats {
            margin-left: 15px;
            margin-right: 0;
        }
    </style>
@endpush

<div class="h-[86vh] w-full bg-pars-100 rounded-2xl shadow-md overflow-y-auto">
    <div class="flex flex-wrap gap-4 mb-6 p-4 bg-pars-100 rounded-2xl">
        <div class="flex flex-wrap w-full gap-4">
            <div class="flex-1 min-w-[400px] flex flex-col">
                <small class="mr-2 text-gray-700 font-medium">تگ عنوان</small>
                <input type="text"
                       class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pars-400 focus:border-transparent transition-all"
                       placeholder="عنوان"
                       wire:model="title_tag">
                @error('title_tag')
                <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                @enderror
            </div>
            <div class="flex-1 min-w-[400px] flex flex-col">
                <small class="mr-2 text-gray-700 font-medium">آدرس</small>
                <input type="text"
                       class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pars-400 focus:border-transparent transition-all"
                       placeholder="آدرس"
                       wire:model="dashed_url">
                @error('dashed_url')
                <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                @enderror
            </div>
            <div class="flex-1 min-w-[400px] flex flex-col">
                <small class="mr-2 text-gray-700 font-medium">عنوان h1</small>
                <input type="text"
                       class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pars-400 focus:border-transparent transition-all"
                       placeholder="عنوان h1"
                       wire:model="title_h1">
                @error('title_h1')
                <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                @enderror
            </div>
            <div class="flex-1 min-w-[400px] flex flex-col">
                <small class="mr-2 text-gray-700 font-medium">متا دیسکریپشن</small>
                <input type="text"
                       class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pars-400 focus:border-transparent transition-all"
                       placeholder="متا دیسکریپشن"
                       wire:model="meta_description">
                @error('meta_description')
                <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="w-full flex flex-wrap gap-4 items-start">
            <div class="flex-1 w-3/4 flex flex-col" wire:ignore>
                <small class="mr-2 text-gray-700 font-medium mb-1">مقاله بلند - پایین</small>
                <div id="editor" class="bg-white rounded-xl shadow-sm"></div>
                @error('article')
                <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                @enderror
            </div>
            <div class="flex-1 w-3/4 flex flex-col" wire:ignore>
                <small class="mr-2 text-gray-700 font-medium mb-1">مقاله کوتاه - بالا</small>
                <div id="editor2" class="bg-white rounded-xl shadow-sm"></div>
                @error('mini_article')
                <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                @enderror
            </div>
            <div class="w-full sm:w-1/4 flex flex-col gap-4">
                <select class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pars-400 focus:border-transparent transition-all"
                        wire:model="categoryId">
                    <option value="0">انتخاب دسته‌بندی</option>
                    @foreach($categories as $value)
                        <option value="{{ $value->id }}" wire:key="{{ $value->id }}">{{ $value->title }}</option>
                    @endforeach
                </select>
                @error('categoryId')
                <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                @enderror
                <button class="bg-gradient-to-r from-pars-500 to-pars-800 hover:from-pars-600 hover:to-pars-900 text-white text-md cursor-pointer px-6 py-2 rounded-xl transition-all shadow-md">
                    ثبت
                </button>
            </div>
        </div>
    </div>

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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ادیتور اول
        const quill = new Quill('#editor', {
            theme: 'snow',
            direction: 'rtl',
            modules: {
                toolbar: [
                    [{ 'header': [2, 3, false] }],
                    ['bold', 'italic', 'underline'],
                    ['link'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'color': [] }, { 'background': [] }],
                    [{ 'align': [] }],
                ]
            }
        });

        quill.root.innerHTML = `{!! $article !!}`;

        quill.on('text-change', function() {
            @this.set('article', quill.root.innerHTML);
        });

        Livewire.on('articleUpdated', (content) => {
            if (quill.root.innerHTML !== content) {
                quill.root.innerHTML = content;
            }
        });

        // ادیتور دوم
        const quill2 = new Quill('#editor2', {
            theme: 'snow',
            direction: 'rtl',
            modules: {
                toolbar: [
                    [{ 'header': [2, 3, false] }],
                    ['bold', 'italic', 'underline'],
                    ['link'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'color': [] }, { 'background': [] }],
                    [{ 'align': [] }],
                ]
            }
        });

        quill2.root.innerHTML = `{!! $mini_article !!}`;

        quill2.on('text-change', function() {
            @this.set('mini_article', quill2.root.innerHTML);
        });

        Livewire.on('miniArticleUpdated', (content) => {
            if (quill2.root.innerHTML !== content) {
                quill2.root.innerHTML = content;
            }
        });
    });
</script>
