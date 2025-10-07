<div>
    <div class="flex justify-around items-center mt-20">
        <div class="w-full">
            <div>
                <strong>کاربر گرامی؛</strong>
                <div class="w-full mt-2">لطفا شماره موبایل خود را وارد کنید:</div>
            </div>
            <input inputmode="numeric"
                   type="text"
                   wire:keydown.enter="submit()"
                   autofocus
                   dir="auto"
                   placeholder="{{ english_to_persian_num('09123456789') }} "
                   class="mt-8 w-full rounded-2xl placeholder:text-gray-300 placeholder:text-center text-center"
                   wire:model="mobile"
                   oninput="convertToPersianDigits(this)">
        </div>
    </div>
    <div class="flex flex-col items-center mt-8 mb-4">
        <a class="w-full text-center cursor-pointer rounded-2xl  p-1.5 bg-pars-500 hover:bg-pars-600  transition-all text-white mb-2"
           wire:click.prevent="submit()">ورود</a>
        <a class="text-xs cursor-pointer self-end mt-1" href="{{ route('forget') }}" wire:navigate.hover>فراموشی رمز
            عبور</a>

    </div>
    @error('mobile')
    <span class="text-xs text-red-500">{{ $message }}</span>
    @enderror
    <script>
        function convertToPersianDigits(el) {
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

</div>






