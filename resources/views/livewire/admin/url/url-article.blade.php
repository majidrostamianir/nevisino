<div class="w-full bg-pars-100 rounded-2xl shadow-md">

    <div class="p-4">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            {{-- Title Tag --}}
            <div>
                <label class="block mb-1 mr-2 text-sm font-medium text-gray-700">
                    تگ عنوان
                </label>

                <input
                        type="text"
                        wire:model.live="title_tag"
                        placeholder="عنوان"
                        class="w-full border border-gray-300 rounded-xl px-3 py-2 bg-white
                           focus:outline-none focus:ring-2 focus:ring-pars-400
                           focus:border-transparent"
                >

                @error('title_tag')
                <span class="block text-xs text-red-500 mt-1">
                        {{ $message }}
                    </span>
                @enderror
            </div>


            {{-- URL --}}
            <div>
                <label class="block mb-1 mr-2 text-sm font-medium text-gray-700">
                    آدرس
                </label>

                <input
                        type="text"
                        wire:model.live="dashed_url"
                        placeholder="example-url"
                        class="w-full border border-gray-300 rounded-xl px-3 py-2 bg-white
                           focus:outline-none focus:ring-2 focus:ring-pars-400
                           focus:border-transparent"
                >

                @error('dashed_url')
                <span class="block text-xs text-red-500 mt-1">
                        {{ $message }}
                    </span>
                @enderror
            </div>


            {{-- H1 --}}
            <div>
                <label class="block mb-1 mr-2 text-sm font-medium text-gray-700">
                    عنوان H1
                </label>

                <input
                        type="text"
                        wire:model.live="title_h1"
                        placeholder="عنوان H1"
                        class="w-full border border-gray-300 rounded-xl px-3 py-2 bg-white
                           focus:outline-none focus:ring-2 focus:ring-pars-400
                           focus:border-transparent"
                >

                @error('title_h1')
                <span class="block text-xs text-red-500 mt-1">
                        {{ $message }}
                    </span>
                @enderror
            </div>


            {{-- Meta Description --}}
            <div>
                <label class="block mb-1 mr-2 text-sm font-medium text-gray-700">
                    متا دیسکریپشن
                </label>

                <input
                        type="text"
                        wire:model.live="meta_description"
                        placeholder="متا دیسکریپشن"
                        class="w-full border border-gray-300 rounded-xl px-3 py-2 bg-white
                           focus:outline-none focus:ring-2 focus:ring-pars-400
                           focus:border-transparent"
                >

                @error('meta_description')
                <span class="block text-xs text-red-500 mt-1">
                        {{ $message }}
                    </span>
                @enderror
            </div>

        </div>

    </div>


    {{-- =========================================================
         MINI ARTICLE
         ========================================================= --}}

    <div class="px-4 pb-5">

        <div class="bg-white rounded-2xl shadow-md p-4">

            <div class="flex items-center justify-between mb-3">

                <label class="text-sm font-semibold text-gray-700">
                    مقاله کوتاه - بالا
                </label>

                <span class="text-xs text-gray-400">
                    HTML
                </span>

            </div>


            {{-- Toolbar --}}
            <div class="flex flex-wrap gap-2 mb-3 p-2 bg-gray-100 border border-gray-300 rounded-xl">

                <button type="button"
                        onclick="insertHtmlTag('mini_article', 'p')"
                        class="editor-btn">
                    P
                </button>

                <button type="button"
                        onclick="insertHtmlTag('mini_article', 'h1')"
                        class="editor-btn">
                    H1
                </button>

                <button type="button"
                        onclick="insertHtmlTag('mini_article', 'h2')"
                        class="editor-btn">
                    H2
                </button>

                <button type="button"
                        onclick="insertHtmlTag('mini_article', 'h3')"
                        class="editor-btn">
                    H3
                </button>

                <button type="button"
                        onclick="insertHtmlTag('mini_article', 'h4')"
                        class="editor-btn">
                    H4
                </button>

                <button type="button"
                        onclick="insertImageTag('mini_article')"
                        class="editor-btn">
                    IMG
                </button>

                <button type="button"
                        onclick="insertHtmlTag('mini_article', 'strong')"
                        class="editor-btn">
                    STRONG
                </button>

                <button type="button"
                        onclick="insertHtmlTag('mini_article', 'span')"
                        class="editor-btn">
                    SPAN
                </button>

                {{-- BR --}}
                <button type="button"
                        onclick="insertBrTag('mini_article')"
                        class="editor-btn">
                    BR
                </button>

                {{-- TABLE --}}
                <button type="button"
                        onclick="insertTableTag('mini_article')"
                        class="editor-btn">
                    TABLE
                </button>

            </div>


            <textarea
                    id="mini_article"
                    wire:model.live="mini_article"
                    rows="10"
                    spellcheck="false"
                    class="html-editor"
                    placeholder="مقاله کوتاه را اینجا بنویسید..."
            >{{ $mini_article }}</textarea>


            @error('mini_article')
            <span class="block text-xs text-red-500 mt-1">
                    {{ $message }}
                </span>
            @enderror

        </div>

    </div>


    {{-- =========================================================
         MAIN ARTICLE
         ========================================================= --}}

    <div class="px-4 pb-5">

        <div class="bg-white rounded-2xl shadow-md p-4">

            <div class="flex items-center justify-between mb-3">

                <label class="text-sm font-semibold text-gray-700">
                    مقاله بلند - پایین
                </label>

                <span class="text-xs text-gray-400">
                    HTML
                </span>

            </div>


            {{-- Toolbar --}}
            <div class="flex flex-wrap gap-2 mb-3 p-2 bg-gray-100 border border-gray-300 rounded-xl">

                <button type="button"
                        onclick="insertHtmlTag('article', 'p')"
                        class="editor-btn">
                    P
                </button>

                <button type="button"
                        onclick="insertHtmlTag('article', 'h1')"
                        class="editor-btn">
                    H1
                </button>

                <button type="button"
                        onclick="insertHtmlTag('article', 'h2')"
                        class="editor-btn">
                    H2
                </button>

                <button type="button"
                        onclick="insertHtmlTag('article', 'h3')"
                        class="editor-btn">
                    H3
                </button>

                <button type="button"
                        onclick="insertHtmlTag('article', 'h4')"
                        class="editor-btn">
                    H4
                </button>

                <button type="button"
                        onclick="insertImageTag('article')"
                        class="editor-btn">
                    IMG
                </button>

                <button type="button"
                        onclick="insertHtmlTag('article', 'strong')"
                        class="editor-btn">
                    STRONG
                </button>

                <button type="button"
                        onclick="insertHtmlTag('article', 'span')"
                        class="editor-btn">
                    SPAN
                </button>

                {{-- BR --}}
                <button type="button"
                        onclick="insertBrTag('article')"
                        class="editor-btn">
                    BR
                </button>

                {{-- TABLE --}}
                <button type="button"
                        onclick="insertTableTag('article')"
                        class="editor-btn">
                    TABLE
                </button>

            </div>


            <textarea
                    id="article"
                    wire:model.live="article"
                    rows="10"
                    spellcheck="false"
                    class="html-editor"
                    placeholder="مقاله اصلی را اینجا بنویسید..."
            >{{ $article }}</textarea>


            @error('article')
            <span class="block text-xs text-red-500 mt-1">
                    {{ $message }}
                </span>
            @enderror

        </div>

    </div>


    {{-- =========================================================
         BOTTOM
         ========================================================= --}}

    <div class="px-4 pb-6">

        <div class="flex flex-col md:flex-row gap-4 items-end">

            {{-- Category --}}
            <div class="flex-1 w-full">

                <label class="block mb-1 mr-2 text-sm font-medium text-gray-700">
                    دسته‌بندی
                </label>

                <select
                        wire:model.live="categoryId"
                        class="w-full border border-gray-300 rounded-xl px-3 py-2 bg-white
                           focus:outline-none focus:ring-2 focus:ring-pars-400
                           focus:border-transparent"
                >

                    <option value="0">
                        انتخاب دسته‌بندی
                    </option>

                    @foreach($categories as $category)

                        <option value="{{ $category->id }}">
                            {{ $category->title }}
                        </option>

                    @endforeach

                </select>


                @error('categoryId')
                <span class="block text-xs text-red-500 mt-1">
                        {{ $message }}
                    </span>
                @enderror

            </div>


            {{-- Save --}}
            <button
                    type="button"
                    wire:click="save"
                    wire:loading.attr="disabled"
                    class="w-full md:w-auto min-w-[150px] cursor-pointer
                       bg-gradient-to-r from-pars-500 to-pars-800
                       hover:from-pars-600 hover:to-pars-900
                       text-white px-8 py-2.5 rounded-xl
                       shadow-md transition-all
                       disabled:opacity-50"
            >

                <span wire:loading.remove>
                    ذخیره
                </span>

                <span wire:loading>
                    در حال ذخیره...
                </span>

            </button>

        </div>

    </div>


    {{-- SUCCESS --}}

    @if(session()->has('success'))

        <div class="mx-4 mb-6">

            <div class="bg-green-100 text-green-700 rounded-xl px-4 py-3">
                {{ session('success') }}
            </div>

        </div>

    @endif


    {{-- =========================================================
         STYLE
         ========================================================= --}}

    <style>

        .editor-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;

            min-width: 42px;
            height: 34px;

            padding: 0 10px;

            border: 1px solid #d1d5db;
            border-radius: 8px;

            background: #ffffff;
            color: #374151;

            font-size: 12px;
            font-weight: 700;

            cursor: pointer;

            transition: all 0.15s ease;
        }

        .editor-btn:hover {
            background: #f3f4f6;
            border-color: #9ca3af;
        }

        .editor-btn:active {
            transform: scale(0.96);
        }


        .html-editor {
            width: 100%;

            padding: 16px;

            border: 1px solid #d1d5db;
            border-radius: 12px;

            background: #f9fafb;
            color: #1f2937;

            font-family:
                    Consolas,
                    Monaco,
                    "Courier New",
                    monospace;

            font-size: 14px;
            line-height: 1.8;

            resize: vertical;
            outline: none;

            transition: all 0.15s ease;
        }

        .html-editor:focus {
            border-color: transparent;

            box-shadow: 0 0 0 2px rgb(196 181 253);
        }

    </style>


    {{-- =========================================================
         JAVASCRIPT
         ========================================================= --}}

    <script>

        /*
        |--------------------------------------------------------------------------
        | Insert normal HTML tag
        |--------------------------------------------------------------------------
        */

        function insertHtmlTag(editorId, tag) {

            const editor = document.getElementById(editorId);

            if (!editor) {
                return;
            }

            const start = editor.selectionStart;
            const end = editor.selectionEnd;

            const selectedText =
                editor.value.substring(start, end);

            let html;


            if (selectedText.length > 0) {

                html =
                    '<' + tag + '>' +
                    selectedText +
                    '</' + tag + '>';

            } else {

                html =
                    '<' + tag + '></' + tag + '>';

            }


            editor.setRangeText(
                html,
                start,
                end,
                'end'
            );


            editor.dispatchEvent(
                new Event('input', {
                    bubbles: true
                })
            );


            editor.focus();
        }


        /*
        |--------------------------------------------------------------------------
        | Insert IMG
        |--------------------------------------------------------------------------
        */

        function insertImageTag(editorId) {

            const editor = document.getElementById(editorId);

            if (!editor) {
                return;
            }

            const start = editor.selectionStart;
            const end = editor.selectionEnd;

            const selectedText =
                editor.value.substring(start, end);

            let html;

            if (selectedText.length > 0) {

                html =
                    '<img src="' +
                    selectedText +
                    '" alt="" class="article-image">';

            } else {

                html =
                    '<img src="" alt="" class="article-image">';

            }

            editor.setRangeText(
                html,
                start,
                end,
                'end'
            );

            editor.dispatchEvent(
                new Event('input', {
                    bubbles: true
                })
            );

            editor.focus();
        }


        /*
        |--------------------------------------------------------------------------
        | Insert BR
        |--------------------------------------------------------------------------
        */

        function insertBrTag(editorId) {

            const editor = document.getElementById(editorId);

            if (!editor) {
                return;
            }

            const start = editor.selectionStart;
            const end = editor.selectionEnd;

            editor.setRangeText(
                '<br>',
                start,
                end,
                'end'
            );


            editor.dispatchEvent(
                new Event('input', {
                    bubbles: true
                })
            );


            editor.focus();
        }


        /*
        |--------------------------------------------------------------------------
        | Insert TABLE
        |--------------------------------------------------------------------------
        */

        function insertTableTag(editorId) {

            const editor = document.getElementById(editorId);

            if (!editor) {
                return;
            }

            const start = editor.selectionStart;
            const end = editor.selectionEnd;

            const selectedText =
                editor.value.substring(start, end);


            let html;


            if (selectedText.length > 0) {

                html =
                    `<table>
    <thead>
        <tr>
            <th>${selectedText}</th>
            <th>عنوان</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>مقدار</td>
            <td>مقدار</td>
        </tr>
        <tr>
            <td>مقدار</td>
            <td>مقدار</td>
        </tr>
    </tbody>
</table>`;

            } else {

                html =
                    `<table>
    <thead>
        <tr>
            <th>عنوان ۱</th>
            <th>عنوان ۲</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>مقدار</td>
            <td>مقدار</td>
        </tr>
        <tr>
            <td>مقدار</td>
            <td>مقدار</td>
        </tr>
    </tbody>
</table>`;

            }


            editor.setRangeText(
                html,
                start,
                end,
                'end'
            );


            editor.dispatchEvent(
                new Event('input', {
                    bubbles: true
                })
            );


            editor.focus();
        }

    </script>

</div>
