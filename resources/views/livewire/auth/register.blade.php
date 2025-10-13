<div>
    <div class="flex justify-around items-center mt-20">
        <div class="w-full">
            <div>
                <strong>کاربر گرامی؛</strong>
                <div class="w-full mt-2">لطفا شماره موبایل خود را وارد کنید:</div>
            </div>
            <input inputmode="numeric"
                   type="text"
                   wire:model="faMobile"
                   wire:blur="syncMobile"
                   wire:keydown.enter="submit()"
                   autofocus
                   dir="auto"
                   placeholder="{{ english_to_persian_num('09123456789') }} "
                   class="mt-8 w-full rounded-2xl placeholder:text-gray-300 placeholder:text-center text-center"
                   oninput="justPersianDigits(this)">
            <input type="hidden" wire:model="enMobile">
        </div>
    </div>
    <div class="flex flex-col items-center mt-8 mb-4">
        <a class="w-full text-center cursor-pointer rounded-2xl  p-1.5 bg-pars-500 hover:bg-pars-600  transition-all text-white mb-2"
           wire:click.prevent="submit()">ورود</a>
        <a class="text-xs cursor-pointer self-end mt-1" href="{{ route('forget') }}" wire:navigate.hover>فراموشی رمز
            عبور</a>

    </div>
    @error('enMobile')
    <span class="text-xs text-red-500">{{ $message }}</span>
    @enderror
</div>






