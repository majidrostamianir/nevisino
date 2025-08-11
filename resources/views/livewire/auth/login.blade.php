<div>
    <div class="flex justify-around items-center mt-16">
        <div class="w-full">
            <strong>کاربر گرامی؛</strong>
            <div class="w-full mt-2">رمز عبور حساب <strong>{{ $mobile }}</strong> را وارد کنید:</div>
            <input type="password"
                   wire:keydown.enter="submit()"
                   autofocus
                   dir="auto"
                   placeholder="رمز عبور"
                   class="mt-6 w-full rounded-2xl placeholder:text-gray-300 placeholder:text-center text-center"
                   wire:model="password">
        </div>
    </div>
    <div class="flex flex-col items-center mt-8 mb-4">
        <a class="w-full text-center cursor-pointer rounded-2xl  p-1.5 bg-pars-500 hover:bg-pars-600  transition-all text-white"
           wire:click.prevent="submit">ورود</a>
        <div class="w-full p-1 flex justify-between">
            <div class="text-xs cursor-pointer" wire:click="edit">ویرایش شماره موبایل</div>
            <a class="text-xs cursor-pointer" href="{{ route('forget') }}" wire:navigate.hover>فراموشی رمز عبور</a>
        </div>
    </div>

    @error('password')
    <span class="text-xs text-red-500">{{ $message }}</span>
    @enderror
</div>






