@push('editor')
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill-rtl@1.0.0/dist/quill-rtl.min.js"></script>

    <style>
        .ql-editor {
            direction: rtl;
            text-align: right;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .ql-toolbar.ql-snow {
            direction: rtl;
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
            <div class="flex-1 min-w-[220px] flex flex-col">
                <input
                    type="text"
                    class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pars-400"
                    placeholder="عنوان"
                    wire:model="title">
                @error('title')
                <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex-1 min-w-[220px] flex flex-col">
                <input
                    type="text"
                    class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pars-400"
                    placeholder="آدرس"
                    wire:model="dashed_title">
                @error('dashed_title')
                <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                @enderror
            </div>
            <div class="flex-1 min-w-[220px] flex flex-col">
                <input
                    type="text"
                    class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pars-400"
                    placeholder="متا دیسکریپشن"
                    wire:model="description">
                @error('description')
                <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="w-full flex flex-wrap gap-4 items-start">
            <div class="flex-1 w-3/4 flex flex-col" wire:ignore>
                <div id="editor">
                   {!! english_to_persian_num($article) !!}
                </div>
                @error('article')
                <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                @enderror
            </div>
            <div class="w-full sm:w-1/4  flex flex-col">
                <select
                    class="w-full my-4 border border-gray-300 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pars-400"
                    wire:model="categoryId">
                    <option value="0">انتخاب دسته‌بندی</option>
                    @foreach($categories as $value)
                        <option value="{{ $value->id }}" wire:key="{{ $value->id }}">{{ $value->title }}</option>
                    @endforeach
                </select>
                @error('categoryId')
                <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                @enderror
                <button
                    class="bg-pars-500 hover:bg-pars-600 text-white text-md cursor-pointer px-6 py-2 rounded-xl transition-all"
                    wire:click.prevent="save()">
                    ثبت
                </button>
            </div>
        </div>
    </div>

    <!-- جدول داده‌ها -->
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full border-collapse text-sm text-right">
            <thead class="bg-pars-400 text-white">
            <tr>
                <th class="px-4 py-3 font-semibold">#</th>
                <th class="px-4 py-3 font-semibold">عنوان</th>
                <th class="px-4 py-3 font-semibold">وضعیت منو</th>
                <th class="px-4 py-3 font-semibold">ایندکس</th>
                <th class="px-4 py-3 font-semibold">محصولات</th>
            </tr>
            </thead>
            <tbody>
            @foreach($urls as $index => $value)
                <tr
                    wire:key="{{ $value->id }}"
                    class=" hover:bg-pars-50 border-b border-gray-100 transition-colors">
                    <td class="px-4 py-3 text-gray-600">{{ $index + 1 }}</td>
                    <td class="px-4 py-3 font-medium cursor-pointer
                            @if($value->in_menu) text-pars-900 @else text-pars-500 @endif"
                        wire:click.prevent="setUrl({{ $value->id }})">
                        {{ $value->title }}
                    </td>
                    <td class="px-4 py-3">
                        @if($value->in_menu)
                            <span
                                class="inline-block px-2.5 py-1 text-xs bg-green-100 text-green-700 rounded-md">در منو</span>
                        @else
                            <span class="inline-block px-2.5 py-1 text-xs bg-gray-100 text-gray-600 rounded-md">خارج از منو</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 cursor-pointer" wire:click="toggleIndexing('{{ $value->id }}')">
                        @if($value->indexing)
                            <span
                                class="inline-block px-2.5 py-1 text-xs bg-green-100 text-green-700 rounded-md">ایندکس</span>
                        @else
                            <span class="inline-block px-2.5 py-1 text-xs bg-gray-100 text-gray-600 rounded-md">نو ایندکس</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.url.product' , ['url'=>$value->id]) }}">مدیریت</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const quill = new Quill('#editor', {
                theme: 'snow',
                direction: 'rtl',
                modules: {
                    toolbar: [
                        [{ 'header': [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline'],
                        ['link'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        [{ 'color': [] }, { 'background': [] }],
                        [{ 'align': [] }],
                    ]
                }
            });

            // مقداردهی اولیه ادیتور با محتوای موجود
            quill.root.innerHTML = `{!! $article !!}`;

            // ارسال تغییرات به لایووایر
            quill.on('text-change', function() {
                const content = quill.root.innerHTML;
                @this.set('article', content);
            });

            // گوش دادن به تغییرات از سمت لایووایر
            Livewire.on('articleUpdated', (content) => {
                if (quill.root.innerHTML !== content) {
                    quill.root.innerHTML = content;
                }
            });
        });
    </script>
</div>
