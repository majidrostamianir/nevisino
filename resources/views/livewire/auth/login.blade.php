<div>
    <div class="flex justify-around items-center mt-16">
        <div class="w-full">
            <strong>کاربر گرامی؛</strong>
            <div class="w-full mt-2">رمز عبور حساب <strong>{{ english_to_persian_num($mobile) }}</strong> را وارد کنید:
            </div>
            <div class="w-full relative mt-6">
                <input type="password"
                       id="passwordInput"
                       wire:keydown.enter="submit()"
                       autofocus
                       dir="auto"
                       placeholder="رمز عبور"
                       class="mt-6 w-full rounded-2xl placeholder:text-gray-300 placeholder:text-center text-center"
                       wire:model="password">
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
                </button>
            </div>
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






