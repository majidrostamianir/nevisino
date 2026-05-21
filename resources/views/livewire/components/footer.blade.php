<div class="mt-10 py-8 bg-pars-100 shadow-sm pb-16 lg:pb-2">
    <div class="max-w-5xl mx-auto px-6">
        <div class="grid grid-cols-2 lg:grid-cols-4 justify-between gap-8 py-8 border-b border-pars-300">
            <div class="flex flex-col gap-3">
                <span class="font-bold text-pars-700 mb-1">ارتباط با ما</span>
                <a href="https://rubika.ir/nevisino" target="_blank"
                   class="flex items-center gap-2 text-sm hover:text-pars-600 transition-colors">
                    <img src="{{ asset('images/rubika.png') }}" class="w-5 h-5" alt="کانال روبیکا نویسینو">
                    <span>کانال روبیکا</span>
                </a>
                <a href="https://rubika.ir/nevisinoAdmin" target="_blank"
                   class="flex items-center gap-2 text-sm hover:text-pars-600 transition-colors">
                    <img src="{{ asset('images/rubika.png') }}" class="w-5 h-5" alt="پشتیبانی نویسینو در روبیکا">
                    <span>پشتیبانی در روبیکا</span>
                </a>
                <a href="https://instagram.com/nevisino.ir" target="_blank"
                   class="flex items-center gap-2 text-sm hover:text-pars-600 transition-colors">
                    <img src="{{ asset('images/instagram.png') }}" class="w-5 h-5" alt="پیج اینستاگرام نویسینو">
                    <span>اینستاگرام</span>
                </a>
                <a href="https://wa.me/+989169889759" target="_blank"
                   class="flex items-center gap-2 text-sm hover:text-pars-600 transition-colors">
                    <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                    <span>واتساپ</span>
                </a>
                <a href="tel:+989169889759" target="_blank"
                   class="flex items-center gap-2 text-sm hover:text-pars-600 transition-colors">
                    <img src="{{ asset('images/call.png') }}" class="w-5 h-5" alt="تماس با پشتیبان">
                    <span>۰۹۱۶۹۲۱۵۳۰۰</span>
                </a>
            </div>
            <div class="flex flex-col gap-3">
                <span class="font-bold text-pars-700 mb-1">محصولات پرفروش</span>
                <a href="{{ route('category-page',['dashed' => 'دفتر-مشق']) }}" wire:navigate class="text-sm hover:text-pars-600 transition-colors">دفتر مشق</a>
                <a href="{{ route('category-page',['dashed' => 'مداد-رنگی-اتودی']) }}" wire:navigate class="text-sm hover:text-pars-600 transition-colors">مداد رنگی اتودی</a>
                <a href="{{ route('category-page',['dashed' => 'مداد-رنگی-فلزی']) }}" wire:navigate class="text-sm hover:text-pars-600 transition-colors">مداد رنگی فلزی</a>
                <a href="{{ route('category-page',['dashed' => 'مداد-رنگی-آریا-آرتیست']) }}" wire:navigate class="text-sm hover:text-pars-600 transition-colors">مداد رنگی آریا آرتیست</a>
                <a href="{{ route('category-page',['dashed' => 'مداد-رنگی-50-رنگ']) }}" wire:navigate class="text-sm hover:text-pars-600 transition-colors">مداد رنگی ۵۰ رنگ</a>
            </div>
            
            <div class="flex flex-col gap-3">
                <span class="font-bold text-pars-700 mb-1">لینک‌های مفید</span>
                <a href="#" wire:navigate class="text-sm hover:text-pars-600 transition-colors">درباره ما</a>
                <a href="#" wire:navigate class="text-sm hover:text-pars-600 transition-colors">تماس با ما</a>
                <a href="#" wire:navigate class="text-sm hover:text-pars-600 transition-colors">شرایط مرجوعی</a>
                <a href="#" wire:navigate class="text-sm hover:text-pars-600 transition-colors">فروش اقساطی</a>
                <a href="#" wire:navigate class="text-sm hover:text-pars-600 transition-colors">نحوه بسته‌بندی و
                    ارسال</a>
            </div>
            
            <div class="flex flex-col gap-3">
                <span class="font-bold text-pars-700 mb-1">مجوزهای ما</span>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('trust') }}" wire:navigate>
                        <img src="{{ asset('images/samandehi.png') }}"
                             class="h-16 hover:scale-105 transition-all cursor-pointer">
                    </a>
                    <a href="{{ route('trust') }}" wire:navigate>
                        <img src="{{ asset('images/enamad.png') }}"
                             class="h-16 hover:scale-105 transition-all cursor-pointer">
                    </a>
                    <a href="{{ route('trust') }}" wire:navigate>
                        <img src="{{ asset('images/zibal.png') }}"
                             class="h-16 hover:scale-105 transition-all cursor-pointer">
                    </a>
                </div>
            </div>
        
        </div>
        
        <div class="pt-4 text-center text-xs text-gray-400">
            تمامی حقوق این سایت متعلق به نویسینو می‌باشد
        </div>
    
    </div>
</div>