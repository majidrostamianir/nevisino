<div class="mt-10 py-8 bg-pars-100 shadow-sm lg:pr-16 pb-16 lg:pb-8">
    <div class="w-fit mx-auto">
        <div class="w-full py-8">
            <div class="flex justify-center text-center">
                <img class="w-6 h-auto" src="{{ asset('images/support.png') }}">
                <span class="mr-1">تلفن شرایط اضطراری:</span>
            </div>
            <div class="justify-center text-center">

                <a href="https://wa.me/+989169889759" target="_blank">{{ english_to_persian_num('09169889759') }}</a>
            </div>
        </div>
        <div class="flex w-full justify-around">
            <a href="{{ route('trust') }}" wire:navigate>
                <img src="{{ asset('images/samandehi.png') }}"
                     class="w-fit h-28 mx-2 hover:scale-105 transition-all cursor-pointer">
            </a>
            <a href="{{ route('trust') }}" wire:navigate>
                <img src="{{ asset('images/enamad.png') }}"
                     class="w-fit h-28 mx-2 hover:scale-105 transition-all cursor-pointer">
            </a>
            <a href="{{ route('trust') }}" wire:navigate>
                <img src="{{ asset('images/zibal.png') }}"
                     class="w-fit h-28 mx-4 hover:scale-105 transition-all cursor-pointer">
            </a>
        </div>
    </div>
</div>
