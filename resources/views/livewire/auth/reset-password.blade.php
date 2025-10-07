<div>
    <div class="mt-10">
        <div class="w-full">
            <strong>کاربر گرامی؛</strong>
            <div>کد ارسال شده به  <strong>{{ english_to_persian_num($mobile) }}</strong> را وارد نموده و یک رمز
                عبور تنظیم نمایید:
            </div>
        </div>
        <div class="relative">
            <input type="text"
                   inputmode="numeric"
                   oninput="convertToPersianDigits(this)"
                   wire:model="otp"
                   placeholder="کد تایید"
                   dir="auto"
                   class="mt-8 w-full rounded-2xl placeholder:text-gray-300 placeholder:text-center text-center">
            <a wire:click.prevent="sendMobileOtp()"
               class="text-xs absolute top-8 left-0 bg-pars-200  py-3 w-3/12 text-center rounded-l-2xl hover:bg-pars-400 cursor-pointer">ارسال
                مجدد</a>
        </div>
        <div class="relative">
            <input type="password"
                   wire:model="password"
                   id="passwordInput"
                   wire:keydown.enter="submit()"
                   placeholder="رمز عبور جدید"
                   dir="auto"
                   class="mt-2 w-full rounded-2xl placeholder:text-gray-300 placeholder:text-center text-center">
            <button type="button" id="togglePassword" class="absolute bottom-0 right-3 -translate-y-1/2">
                <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor">
                    <!-- چشم -->
                    <path id="eye" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path id="eyeOutline" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    <!-- خط روی چشم (حالت بسته) -->
                    <line id="eyeLine" x1="3" y1="3" x2="21" y2="21" stroke="currentColor" stroke-width="2"
                          stroke-linecap="round" class="hidden"/>
                </svg>
            </button> </div>
        <div class="mt-8 mb-4">
            <button wire:click="submit"
                    class="w-full text-center cursor-pointer rounded-2xl  p-1.5 bg-pars-500 hover:bg-pars-600  transition-all text-white">
                تایید و ورود
            </button>
            <div class="w-full p-2 flex justify-between">
                <div class="text-xs cursor-pointer" wire:click="edit">ورود / ثبت نام</div>
            </div>
        </div>

        @if (session('otp'))
            <p class="{{ session('color') }} text-xs">{{ session('otp') }}</p>
        @endif
        @error('otp') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
        @error('password') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
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
    <script>
        const passwordInput = document.getElementById('passwordInput');
        const toggleBtn = document.getElementById('togglePassword');
        const eyeLine = document.getElementById('eyeLine');

        toggleBtn.addEventListener('click', () => {
            if (passwordInput.type === 'password') {
                // نمایش پسورد -> چشم بسته (خط روی چشم)
                passwordInput.type = 'text';
                eyeLine.classList.remove('hidden');
            } else {
                // مخفی کردن پسورد -> چشم باز
                passwordInput.type = 'password';
                eyeLine.classList.add('hidden');
            }
        });
    </script>
</div>
