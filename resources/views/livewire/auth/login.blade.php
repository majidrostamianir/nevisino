<div x-data="{
        showPassword: false,
        value: '',
        enValue: '',
        convert(val) {
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
        submit() {
            $wire.set('enPassword', this.enValue);
            $wire.submit();
        },
        togglePassword() {
            this.showPassword = !this.showPassword;
        }
}">
    <div class="flex justify-around items-center mt-16">
        <div class="w-full">
            <strong>کاربر گرامی؛</strong>
            <div class="w-full mt-2">رمز عبور حساب <strong>{{ english_to_persian_num($mobile) }}</strong> را وارد کنید:
            </div>
            <div class="w-full relative mt-6">
                <input :type="showPassword ? 'text' : 'password'"
                       @keydown.enter.prevent="submit()"
                       @input="convert($event.target.value)"
                       x-model="value"
                       autofocus
                       dir="auto"
                       placeholder="رمز عبور"
                       class="mt-6 w-full rounded-2xl placeholder:text-gray-300 placeholder:text-center text-center">
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
        </div>
    </div>
    <div class="flex flex-col items-center mt-8 mb-4">
        <a class="w-full text-center cursor-pointer rounded-2xl  p-1.5 bg-pars-500 hover:bg-pars-600  transition-all text-white"
           @click.prevent="submit()">ورود</a>
        <div class="w-full p-1 flex justify-between">
            <div class="text-xs cursor-pointer" wire:click="edit">ویرایش شماره موبایل</div>
            <a class="text-xs cursor-pointer" href="{{ route('forget') }}" wire:navigate.hover>فراموشی رمز عبور</a>
        </div>
    </div>
    @error('enPassword')
    <span class="text-xs text-red-500">{{ $message }}</span>
    @enderror
</div>






