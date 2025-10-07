<div>
    <div class="mt-20">
        <div class="w-full">
            <strong>کاربر گرامی؛</strong>
            <div>
                شماره موبایلی که با آن در سایت ثبت نام کرده اید را وارد نمایید:
            </div>
        </div>
        <div>
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
        <div class="mt-6 mb-4">
            <button wire:click="submit"
                    class="w-full text-center cursor-pointer rounded-2xl  p-1.5 bg-pars-500 hover:bg-pars-600  transition-all text-white">
                تایید
            </button>
            <div class="w-full p-2 flex justify-between">
                <div class="text-xs cursor-pointer" ></div>
                <a class="text-xs cursor-pointer" href="/register" wire:navigate >ورود / ثبت نام</a>
            </div>
        </div>
        @error('mobile') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror

    </div>

    <script>
        function convertToPersianDigits(el) {
            const map = {
                '0': '۰', '1': '۱', '2': '۲', '3': '۳', '4': '۴', '5': '۵', '6': '۶', '7': '۷', '8': '۸', '9': '۹',
                '٠': '۰', '١': '۱', '٢': '۲', '٣': '۳', '٤': '۴', '٥': '۵', '٦': '۶', '٧': '۷', '٨': '۸', '٩': '۹'
            };

            let cleanedValue = el.value.replace(/[^0-9٠-٩۰-۹]/g, '');

            let newValue = cleanedValue.replace(/[0-9٠-٩]/g, d => map[d] || d);

            if (newValue !== el.value) {
                el.value = newValue;
            }
        }
    </script>
</div>



