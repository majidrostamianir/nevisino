<div class="py-6 space-y-4">

    <style>
        @keyframes about-floatUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes about-penDraw {
            from { stroke-dashoffset: 300; }
            to   { stroke-dashoffset: 0; }
        }
        @keyframes about-countUp {
            from { opacity: 0; transform: scale(0.7); }
            to   { opacity: 1; transform: scale(1); }
        }
        .about-anim-1 { animation: about-floatUp 0.5s ease both 0.1s; }
        .about-anim-2 { animation: about-floatUp 0.5s ease both 0.25s; }
        .about-anim-3 { animation: about-floatUp 0.5s ease both 0.4s; }
        .about-anim-4 { animation: about-floatUp 0.5s ease both 0.55s; }
        .about-anim-5 { animation: about-floatUp 0.5s ease both 0.7s; }
        .about-stat   { animation: about-countUp 0.6s cubic-bezier(0.34,1.56,0.64,1) both 0.65s; }
        .about-pencil { stroke-dasharray: 300; stroke-dashoffset: 300; animation: about-penDraw 1.4s ease forwards 0.3s; }
        .about-value-card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .about-value-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,0.08); }
    </style>

    <div class="about-anim-1 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden grid grid-cols-2 min-h-[200px]">
        <div class="p-5 md:p-6 lg:p-7 flex flex-col justify-between border-b md:border-b-0 md:border-l border-gray-100 text-right min-h-[280px] md:min-h-[300px]">
            <span class="inline-block text-[11px] font-semibold bg-blue-50 text-blue-800 px-3 py-1.5 rounded-full w-fit">از سال ۱۴۰۳</span>

            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 leading-relaxed">
                ما عاشق
                <br>
                <span class="text-blue-600 pr-8">نوشتنیم</span>
            </h1>

            <p class="text-sm text-gray-500 leading-relaxed text-justify">
                نویسینو جایی است که لوازم‌تحریر با دقت انتخاب می‌شوند تا شما هم عاشق نوشتن شوید.
            </p>
        </div>
        <div class="flex items-center justify-center bg-gray-50">
            <img src="{{ asset('images/pen.png') }}" class="object-contain rotate-12 lg:rotate-70 lg:w-1/3">
        </div>
    </div>

    <div class="about-anim-2 bg-white rounded-2xl border border-gray-100 shadow-sm py-6 px-5 md:p-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-1.5 h-full bg-gradient-to-b from-blue-600 to-blue-600/30 rounded-r-full"></div>

        <p class="text-2xl font-semibold text-[#0060E0] text-center mb-2 tracking-wide">چرا نویسینو ؟</p>
        <p class="text-xs text-gray-500 text-center mb-10">ما به ۶ اصل پایبندیم</p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div class="flex items-start gap-4">
                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-base font-bold text-gray-800 mb-1">محصولات با کیفیت و اصل</p>
                    <p class="text-sm text-gray-500 leading-relaxed">محصولات برندهای معتبر را مستقیماً از شرکت و بدون واسطه تهیه می‌کنیم که تضمین اصالت کالا و قیمت مناسب‌تر را سبب می‌شود. هر کالا قبل از قرار گرفتن در فروشگاه، از نظر سلامت بررسی می‌شود تا خیالتان از بابت خرید راحت باشد.</p>
                </div>
            </div>

            <div class="flex items-start gap-4">
                <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-base font-bold text-gray-800 mb-1">ارسال دقیق کالا مطابق با سایت</p>
                    <p class="text-sm text-gray-500 leading-relaxed">به منظور شفافیت کامل در فرآیند فروش، عکس‌برداری از محصولات توسط تیم نویسینو و مستقیماً از نمونه کالا انجام می‌گردد. بنابراین تطابق ۱۰۰٪ بین کالای دریافتی و تصویر موجود در سایت تضمین می‌شود.</p>
                </div>
            </div>

            <div class="flex items-start gap-4">
                <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div>
                    <p class="text-base font-bold text-gray-800 mb-1">بسته‌بندی محکم و ضربه‌گیری</p>
                    <p class="text-sm text-gray-500 leading-relaxed">کلیه سفارش‌ها در کارتن‌های پستیِ سه‌لایه و استاندارد، با دقت بالا بسته‌بندی شده و فضاهای خالی داخل کارتن با ضربه‌گیر پر می‌شود تا محصول در سلامت کامل به دست شما برسد.</p>
                </div>
            </div>

            <div class="flex items-start gap-4">
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2L3 14h7l-2 8 10-12h-7l2-8z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-base font-bold text-gray-800 mb-1">ارسال سریع</p>
                    <p class="text-sm text-gray-500 leading-relaxed">سفارش‌های ثبت شده تا قبل از ساعت ۱۰ صبحِ روزهای کاری، در همان روز و سفارش‌های ثبت شده بعد از این ساعت، در روز کاری بعد ارسال می‌گردند.</p>
                </div>
            </div>

            <div class="flex items-start gap-4">
                <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-base font-bold text-gray-800 mb-1">رهگیری محصولات تا رسیدن به مقصد</p>
                    <p class="text-sm text-gray-500 leading-relaxed">شما می‌توانید با استفاده از دکمه رهگیری موجود در بخش سفارشات، سفارش خود را از لحظه خروج از انبار تا رسیدن به درب منزل، لحظه به لحظه رهگیری کنید و از وضعیت آن باخبر باشید.</p>
                </div>
            </div>

            <div class="flex items-start gap-4">
                <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-base font-bold text-gray-800 mb-1">پشتیبانی به موقع و سریع</p>
                    <p class="text-sm text-gray-500 leading-relaxed">تیم پشتیبانی ما هفت روز هفته آماده پاسخگویی به سوالات و مشکلات شماست. هر سوالی دارید، در سریعترین زمان ممکن پاسخ را دریافت می‌کنید.</p>
                </div>
            </div>

        </div>
    </div>
    {{-- ارزش‌ها --}}
    <div class="about-anim-3 grid grid-cols-1 md:grid-cols-2 gap-3">

        {{-- ضمانت بازگشت کالا --}}
        <div class="about-value-card bg-white rounded-2xl shadow-sm border border-green-100 row-span-2 flex flex-col items-center justify-center text-center p-6 md:p-8">
            <svg width="48" height="48" viewBox="0 0 48 48" fill="none" class="mb-3">
                <circle cx="24" cy="24" r="22" fill="#dcfce7" stroke="#16a34a" stroke-width="1.5"/>
                <path d="M16 24l6 6 10-10" stroke="#16a34a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <p class="text-sm font-bold text-black mb-1">۷ روز ضمانت مرجوعی</p>
            <p class="text-[11px] text-gray-500 mb-3">در صورت عدم رضایت، کالا را برگردانید</p>
            <a href="{{ route('return.policy') }}" wire:navigate class="text-[10px]  transition-colors">شرایط مرجوعی ›</a>
        </div>
        <div class="about-value-card bg-white rounded-2xl border border-gray-100 shadow-sm p-3 flex items-center gap-3 group hover:border-blue-200 transition-all">
            <div class="w-9 h-9 bg-blue-50 rounded-lg flex items-center justify-center shrink-0 group-hover:bg-blue-100 transition-colors">
                <svg class="w-4 h-4 text-[#0060E0]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <div class="flex-1">
                <p class="text-sm font-bold text-gray-800">پرداخت امن</p>
                <p class="text-[10px] text-gray-400">پرداخت وجه در نویسینو از طریق درگاه امن زیبال انجام می‌شود</p>
            </div>
        </div>
        <div class="about-value-card bg-white rounded-2xl border border-gray-100 shadow-sm p-3 flex items-center gap-3 group hover:border-purple-200 transition-all">
            <div class="w-9 h-9 bg-purple-50 rounded-lg flex items-center justify-center shrink-0 group-hover:bg-purple-100 transition-colors">
                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <div class="flex-1">
                <p class="text-sm font-bold text-gray-800">مجوزهای قانونی ما</p>
                <p class="text-[10px] text-gray-400">دارای پروانه کسب، نماد اعتماد و سایر مجوزهای قانونی</p>
            </div>
            <a href="{{ route('trust') }}" wire:navigate class="text-[10px] text-purple-600 hover:text-purple-700 transition-colors ml-1">مشاهده مجوزها ›</a>
        </div>


    </div>

    <div class="about-anim-5 rounded-2xl py-6 px-5 text-center bg-gradient-to-r from-blue-700 via-teal-500 to-amber-500 shadow-lg bg-[length:300%_100%] animate-gradient">
        <style>
            @keyframes gradient-shift {
                0% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }
            .animate-gradient {
                background-size: 300% 100%;
                animation: gradient-shift 5s ease infinite;
            }
        </style>
        <p class="text-base font-bold text-white mb-1.5 drop-shadow-md">✨ آماده خرید هستید؟ ✨</p>
        <p class="text-xs text-white/90 mb-3">بهترین لوازم‌تحریر با قیمت مناسب</p>
        <a href="{{ route('shop') }}" wire:navigate
           class="inline-block bg-gradient-to-r from-amber-400 to-amber-500 text-white font-bold py-2.5 px-8 rounded-xl text-sm no-underline transition-all duration-200 hover:scale-105 hover:shadow-md">
            🚀 رفتن به فروشگاه ←
        </a>
    </div>
</div>