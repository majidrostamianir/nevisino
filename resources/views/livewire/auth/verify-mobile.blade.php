<div x-data="{
    showPassword: false,
    value: '',
    enValue: '',
    otpValue: '',
    enOtpValue: '',
    convertPassword(val) {
        const fa = {'0':'۰','1':'۱','2':'۲','3':'۳','4':'۴','5':'۵','6':'۶','7':'۷','8':'۸','9':'۹'};
        const en = {'۰':'0','۱':'1','۲':'2','۳':'3','۴':'4','۵':'5','۶':'6','۷':'7','۸':'8','۹':'9',
                    '٠':'0','١':'1','٢':'2','٣':'3','٤':'4','٥':'5','٦':'6','٧':'7','٨':'8','٩':'9'};
        const allowed = val.replace(/[^0-9۰-۹٠-٩A-Za-z!@#$%&]/g, '');
        this.value = allowed
            .replace(/[0-9]/g, d => fa[d])
            .replace(/[٠-٩]/g, d => fa[en[d]])
            .replace(/[۰-۹]/g, d => d);
        this.enValue = allowed.replace(/[۰-۹٠-٩]/g, d => en[d]);
    },
    convertOtp(val) {
        const fa = {'0':'۰','1':'۱','2':'۲','3':'۳','4':'۴','5':'۵','6':'۶','7':'۷','8':'۸','9':'۹'};
        const en = {'۰':'0','۱':'1','۲':'2','۳':'3','۴':'4','۵':'5','۶':'6','۷':'7','۸':'8','۹':'9',
                    '٠':'0','١':'1','٢':'2','٣':'3','٤':'4','٥':'5','٦':'6','٧':'7','٨':'8','٩':'9'};
        const onlyDigits = val.replace(/[^0-9۰-۹٠-٩]/g, '');
        this.otpValue = onlyDigits.replace(/[0-9]/g, d => fa[d]);
        this.enOtpValue = onlyDigits.replace(/[۰-۹٠-٩]/g, d => en[d]);
    },
    submit() {
        $wire.set('enPassword', this.enValue);
        $wire.set('enOtp', this.enOtpValue);
        $wire.submit();
    },
    togglePassword() {
        this.showPassword = !this.showPassword;
    }
}">

<div class="mt-10">
        <div class="w-full">
            <strong>کاربر گرامی؛</strong>
            <div>کد ارسال شده به شماره <strong>{{ english_to_persian_num($mobile) }}</strong> را وارد نموده و یک رمز
                عبور تنظیم نمایید:
            </div>
        </div>
        <div class="relative">
            <input inputmode="numeric"
                   type="text"
                   x-model="otpValue"
                   @keydown.enter.prevent="submit()"
                   @input="convertOtp($event.target.value)"
                   dir="auto"
                   autofocus
                   placeholder="کد تایید"
                   class="mt-8 w-full rounded-2xl placeholder:text-gray-300 placeholder:text-center text-center">
            <a @click.prevent="sendOtp()"
               class="text-xs absolute top-8 left-0 bg-pars-300 hover:bg-pars-400 hover:text-pars-500  py-3 w-3/12 text-center rounded-l-2xl cursor-pointer">ارسال
                مجدد</a>
        </div>
        <div class="w-full relative">
            <input :type="showPassword ? 'text' : 'password'"
                   @keydown.enter.prevent="submit()"
                   @input="convertPassword($event.target.value)"
                   x-model="value"
                   autofocus
                   dir="auto"
                   placeholder="رمز عبور"
                   class="mt-2 w-full rounded-2xl placeholder:text-gray-300 placeholder:text-center text-center">
            <button
                type="button"
                @click="togglePassword"
                class="absolute bottom-0 right-3 -translate-y-1/2"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    <line x1="3" y1="3" x2="21" y2="21"
                          stroke="currentColor" stroke-width="2" stroke-linecap="round"
                          x-show="showPassword"/>
                </svg>
            </button>
        </div>
        <div class="mt-8 mb-4">
            <button @click.prevent="submit()"
                    class="w-full text-center cursor-pointer rounded-2xl  p-1.5 bg-pars-500 hover:bg-pars-600  transition-all text-white">
                تایید و ورود
            </button>
            <div class="w-full p-2 flex justify-between">
                <div class="text-xs cursor-pointer" wire:click="edit">ویرایش شماره موبایل</div>
                <a class="text-xs cursor-pointer" href="{{ route('forget') }}" wire:navigate.hover>فراموشی رمز
                    عبور</a>
            </div>
        </div>

        @if (session('otp'))
            <p class="{{ session('color') }} text-xs">{{ session('otp') }}</p>
        @endif
        @error('enOtp') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
        @error('enPassword') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
    </div>
</div>
