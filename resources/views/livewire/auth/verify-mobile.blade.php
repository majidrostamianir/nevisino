<div
    x-data="{
        otpValue: '',
        enOtpValue: '',
        convertOtp(val) {
            const fa = {'0':'۰','1':'۱','2':'۲','3':'۳','4':'۴','5':'۵','6':'۶','7':'۷','8':'۸','9':'۹'};
            const en = {'۰':'0','۱':'1','۲':'2','۳':'3','۴':'4','۵':'5','۶':'6','۷':'7','۸':'8','۹':'9',
                        '٠':'0','١':'1','٢':'2','٣':'3','٤':'4','٥':'5','٦':'6','٧':'7','٨':'8','٩':'9'};
            const onlyDigits = val.replace(/[^0-9۰-۹٠-٩]/g, '');
            this.otpValue = onlyDigits.replace(/[0-9]/g, d => fa[d]);
            this.enOtpValue = onlyDigits.replace(/[۰-۹٠-٩]/g, d => en[d]);
        },
        submit() {
            $wire.set('enOtp', this.enOtpValue);
            $wire.submit();
        },
        initWebOtp() {
            if ('OTPCredential' in window) {
                const ac = new AbortController();
                window.addEventListener('beforeunload', () => ac.abort());

                navigator.credentials.get({
                    otp: { transport: ['sms'] },
                    signal: ac.signal
                }).then(otp => {
                    if (otp?.code) {
                        this.convertOtp(otp.code);
                    }
                }).catch(err => console.log('WebOTP error:', err));
            }
        }
    }"
    x-init="initWebOtp()"
>
    <div class="mt-10">
        <div class="w-full">
            <strong>کاربر گرامی؛</strong>
            <div>کد ارسال شده به شماره <strong>{{ english_to_persian_num($mobile) }}</strong> را وارد نمایید:</div>
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
            <a wire:click.prevent="sendOtp()"
               class="text-xs absolute top-8 left-0 bg-pars-300 hover:bg-pars-400 hover:text-pars-500 active:bg-pars-500 active:text-white  py-3 w-3/12 text-center rounded-l-2xl cursor-pointer">
                ارسال مجدد
            </a>
        </div>

        <div class="mt-8 mb-4">
            <button @click.prevent="submit()"
                    class="w-full text-center cursor-pointer rounded-2xl p-1.5 bg-pars-500 hover:bg-pars-600 transition-all text-white">
                تایید و ورود
            </button>
            <div class="w-full p-2 flex justify-between">
                <div class="text-xs cursor-pointer" wire:click="edit">ویرایش شماره موبایل</div>
            </div>
        </div>

        @if (session('otp'))
            <p class="{{ session('color') }} text-xs">{{ session('otp') }}</p>
        @endif
        @error('enOtp') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
    </div>
</div>
