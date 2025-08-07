<div>
    <div class="mt-10">
        <div class="w-full">
            <strong>کاربر گرامی؛</strong>
            <div>کد ارسال شده به  <strong>{{ $mobile }}</strong> را وارد نموده و یک رمز
                عبور تنظیم نمایید:
            </div>
        </div>
        <div class="relative">
            <input type="number" wire:model="otp" placeholder="کد تایید"
                   dir="auto"
                   class="mt-8 w-full rounded-2xl placeholder:text-gray-300 placeholder:text-center text-center">
            <a wire:click.prevent="sendMobileOtp()"
               class="text-xs absolute top-8 left-0 bg-pars-200  py-3 w-3/12 text-center rounded-l-2xl hover:bg-pars-400 cursor-pointer">ارسال
                مجدد</a>
        </div>
        <div>
            <input type="password" wire:model="password" placeholder="رمز عبور جدید"
                   dir="auto"
                   class="mt-2 w-full rounded-2xl placeholder:text-gray-300 placeholder:text-center text-center">
        </div>
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
</div>
