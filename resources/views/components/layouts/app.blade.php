<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'نویسینو' }}</title>
    <link rel="icon" type="image/png" sizes="16x16" href="/images/logo.png">

    @vite('resources/css/app.css')

    @livewireStyles

</head>
<body>


<livewire:components.header/>
<livewire:components.toast />

<div class="mx-4 sm:mr-20 sm:ml-4 mt-32 sm:mt-20  min-h-[65vh]">
    {{ $slot }}
</div>

<livewire:components.footer />

<script>
    function justPersianDigits(el) {
        const map = {
            '0': '۰', '1': '۱', '2': '۲', '3': '۳', '4': '۴', '5': '۵', '6': '۶', '7': '۷', '8': '۸', '9': '۹',
            '٠': '۰', '١': '۱', '٢': '۲', '٣': '۳', '٤': '۴', '٥': '۵', '٦': '۶', '٧': '۷', '٨': '۸', '٩': '۹'
        };

        // حذف تمام کاراکترهای غیر از اعداد فارسی، عربی و انگلیسی
        let cleanedValue = el.value.replace(/[^0-9٠-٩۰-۹]/g, '');

        // تبدیل اعداد به فارسی
        let newValue = cleanedValue.replace(/[0-9٠-٩]/g, d => map[d] || d);

        if (newValue !== el.value) {
            el.value = newValue;
        }
    }
</script>

@livewireScripts


</body>
</html>
