<div x-data="{
        value: '',
        enValue: '',
        remainingSeconds: {{ $remainingSeconds }},
        timerInterval: null,
        convert(val) {
            const fa = {'0':'۰','1':'۱','2':'۲','3':'۳','4':'۴','5':'۵','6':'۶','7':'۷','8':'۸','9':'۹'};
            const en = {'۰':'0','۱':'1','۲':'2','۳':'3','۴':'4','۵':'5','۶':'6','۷':'7','۸':'8','۹':'9',
                        '٠':'0','١':'1','٢':'2','٣':'3','٤':'4','٥':'5','٦':'6','٧':'7','٨':'8','٩':'9'};
            const onlyDigits = val.replace(/[^0-9۰-۹٠-٩]/g, '');
            this.value = onlyDigits.replace(/[0-9]/g, d => fa[d]);
            this.enValue = onlyDigits.replace(/[۰-۹٠-٩]/g, d => en[d]);
        },
        // تابع تبدیل عدد به فارسی
        toPersianNumber(num) {
            const persianDigits = '۰۱۲۳۴۵۶۷۸۹';
            return num.toString().replace(/\d/g, d => persianDigits[parseInt(d)]);
        },
        submit() {
            $wire.set('enOtp', this.enValue);
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
                        this.convert(otp.code);
                    }
                }).catch(err => console.log('WebOTP error:', err));
            }
        },
        startTimer(seconds) {
            if (this.timerInterval) clearInterval(this.timerInterval);
            this.remainingSeconds = seconds;

            if (this.remainingSeconds > 0) {
                this.timerInterval = setInterval(() => {
                    this.remainingSeconds--;
                    if (this.remainingSeconds <= 0) {
                        clearInterval(this.timerInterval);
                        this.timerInterval = null;
                    }
                }, 1000);
            }
        },
        resendOtp() {
            if (this.remainingSeconds > 0) return;
            $wire.sendOtp();
            this.startTimer(60);
        },
        init() {
            this.initWebOtp();
            if (this.remainingSeconds > 0) this.startTimer(this.remainingSeconds);

            // گوش‌دهی به رویداد ارسال کد
            window.addEventListener('otp-sent', (event) => {
                this.startTimer(event.detail.seconds);
            });

            // گوش‌دهی به رویداد انقضای کد
            window.addEventListener('otp-expired', () => {
                this.value = '';
                this.enValue = '';
            });

            // واچ کردن تغییرات isOtpExpired
            $watch('$wire.isOtpExpired', (value) => {
                if (value) {
                    this.value = '';
                    this.enValue = '';
                }
            });
        },
        destroy() {
            if (this.timerInterval) {
                clearInterval(this.timerInterval);
                this.timerInterval = null;
            }
        }
    }"
     x-init="init()"
     x-on:otp-sent.window="startTimer($event.detail.seconds)"
     x-on:otp-expired.window="value = ''; enValue = '';"
>
    <div class="flex justify-around items-center mt-20">
        <div class="w-full">
            <strong class="text-gray-800">کاربر گرامی؛</strong>
            <div class="w-full mt-2 text-gray-600">
                کد ارسال شده به شماره <strong>{{ english_to_persian_num($mobile) }}</strong> را وارد نمایید:
            </div>

            <input
                    inputmode="numeric"
                    type="text"
                    x-model="value"
                    @keydown.enter.prevent="submit()"
                    @input="convert($event.target.value)"
                    @focus="$event.target.select()"
                    dir="auto"
                    autofocus
                    placeholder="کد تایید"
                    :class="{
                    'border-red-500 focus:border-red-500 focus:ring-red-200': $wire.isOtpExpired,
                    'border-gray-200 focus:border-pars-500 focus:ring-pars-200': !$wire.isOtpExpired
                }"
                    class="mt-8 w-full rounded-2xl placeholder:text-gray-400 placeholder:text-center text-center border-2 focus:outline-none focus:ring-2 bg-white p-3 transition-all duration-200"
                    maxlength="4"
            >

            <div class="text-right mt-2 text-xs text-gray-400 flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>کد ۴ رقمی ارسال شده</span>
            </div>

            <!-- پیام انقضای کد -->
            @if($isOtpExpired)
                <div class="text-red-500 text-xs text-center mt-3 animate-pulse bg-red-50 p-2 rounded-lg border border-red-200">
                    ⚠️ کد شما منقضی شده است. برای دریافت کد جدید، روی "ارسال مجدد کد" کلیک کنید.
                </div>
            @endif
        </div>
    </div>

    <div class="flex flex-col items-center mt-8 mb-4">
        <button
                @click.prevent="submit()"
                :disabled="$wire.isSubmitting || $wire.isOtpExpired"
                class="w-full text-center cursor-pointer rounded-2xl p-1.5 bg-pars-500 hover:bg-pars-600 transition-all text-white mb-2 disabled:opacity-50 disabled:cursor-not-allowed"
        >
            <span x-show="!$wire.isSubmitting">تایید و ورود</span>
            <span x-show="$wire.isSubmitting">در حال بررسی...</span>
        </button>

        <div class="w-full flex justify-between items-center mt-2">
            <button
                    @click="resendOtp()"
                    :disabled="remainingSeconds > 0"
                    :class="{
                    'opacity-50 cursor-not-allowed': remainingSeconds > 0,
                    'hover:bg-pars-400 active:bg-pars-500 active:text-white': remainingSeconds <= 0
                }"
                    class="text-xs bg-pars-300 hover:bg-pars-400 py-2 px-4 rounded-2xl cursor-pointer transition-all"
            >
                <span x-show="remainingSeconds <= 0">
                    <span x-show="!$wire.isResending">ارسال مجدد کد</span>
                    <span x-show="$wire.isResending">در حال ارسال...</span>
                </span>
                <span x-show="remainingSeconds > 0">
                    ارسال مجدد (<span x-text="toPersianNumber(remainingSeconds)"></span> ثانیه)
                </span>
            </button>

            <button
                    wire:click="edit"
                    class="text-xs text-pars-500 hover:text-pars-600 cursor-pointer"
            >
                ویرایش شماره موبایل
            </button>
        </div>
    </div>

    @if (session('otp'))
        <p class="{{ session('color') }} text-xs text-center mt-2">{{ session('otp') }}</p>
    @endif
    @error('enOtp')
    <p class="text-red-500 text-xs text-center mt-2">{{ $message }}</p>
    @enderror
</div>