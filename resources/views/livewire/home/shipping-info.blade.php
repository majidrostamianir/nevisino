<div class="min-h-screen bg-gray-50 py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-yellow-200 to-yellow-500 text-white px-6 py-8">
                <h1 class="text-3xl font-bold mb-2">نحوه بسته‌بندی و ارسال</h1>
                <p class="text-amber-100">سالم رسیدن محصولات به دست شما، اولویت ماست</p>
            </div>

            <div class="p-6 md:p-8 space-y-8">
                {{-- بسته‌بندی --}}
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800">نحوه بسته‌بندی</h2>
                    </div>
                    <div class="space-y-3 text-gray-600 pr-4">
                        <p>✅ تک‌تک محصولات پیش از بسته‌بندی <strong>از لحاظ سلامت فیزیکی</strong> بررسی می‌شوند.</p>
                        <p>✅ کلیه سفارش‌ها در <strong>کارتن‌های پستیِ سه‌لایه و با کیفیت بالا</strong> بسته‌بندی می‌شوند.</p>
                        <p>✅ فضاهای خالی داخل کارتن با <strong>ضربه‌گیر</strong> پر می‌شود تا از برخورد محصولات با یکدیگر جلوگیری شود.</p>
                        <p>✅ کارتن پستی از چند جهت با  <strong>چسب مخصوص</strong>  محکم می‌شود تا بسته بودن کارتن در طول مسیر ارسال تضمین گردد.</p>
                    </div>
                </div>

                {{-- روش‌های ارسال --}}
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800">روش‌های ارسال</h2>
                    </div>

                    <div class="grid gap-4">

                        <div class="border rounded-xl p-4">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="font-bold text-gray-800">📮 ارسال با پست پیشتاز</h3>
                                <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full">اقتصادی</span>
                            </div>
                            <p class="text-gray-500 text-sm">زمان تحویل: {{ english_to_persian_num('۲ تا ۴ روز کاری') }}</p>
                            <p class="text-gray-500 text-sm">قابل پیگیری با کد رهگیری</p>
                        </div>
                        <div class="border rounded-xl p-4">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="font-bold text-gray-800">🚚 ارسال با تیپاکس</h3>
                                <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">سریع‌تر</span>
                            </div>
                            <p class="text-gray-500 text-sm">زمان تحویل: {{ english_to_persian_num('۱ تا ۲ روز کاری') }}</p>
                            <p class="text-gray-500 text-sm">قابل پیگیری آنلاین</p>
                        </div>

                    </div>
                </div>

                {{-- زمان ارسال --}}
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800">زمان ارسال</h2>
                    </div>
                    <div class="space-y-3 text-gray-600 pr-4">
                        <p>⏰ سفارش‌هایی که تا ساعت <strong>{{ english_to_persian_num('۱۰ صبح') }}</strong> روزهای کاری ثبت شوند، همان روز ارسال می‌گردند.</p>
                        <p>⏰ سفارش‌های بعد از این ساعت، در <strong>روز کاری بعد</strong> ارسال می‌شوند.</p>
                    </div>
                </div>

                {{-- سوالات متداول --}}
                <div class="bg-gray-50 rounded-xl p-5">
                    <h3 class="font-bold text-gray-800 mb-3">سوالات متداول</h3>
                    <div class="space-y-3 text-sm">
                        <div>
                            <p class="font-semibold text-gray-800">❓ آیا می‌توانم سفارش را پیگیری کنم؟</p>
                            <p class="text-gray-500">بله، بعد از ثبت سفارش، در پیش‌خوان و بخش سفارش‌ها می‌توانید با استفاده از دکمه رهگیری موقعیت دقیق مرسوله خود را مشاهده کنید.</p>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">❓ هزینه ارسال چقدر است؟</p>
                            <p class="text-gray-500">در روش پس‌کرایه هزینه ارسال بر اساس وزن و مقصد محاسبه می‌شود و در زمان تحویل مرسوله از شما اخذ می‌گردد. در روش پیش‌کرایه مبلغ ثابتی که در صفحه تکمیل سفارش به شما نمایش داده می‌شود به فاکتور شما افزوده می‌شود و دیگر در هنگام تحویل گرفتن مرسوله هیچ‌گونه وجهی پرداخت نمی‌کنید.</p>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">❓ آیا ارسال به تمام ایران انجام می‌شود؟</p>
                            <p class="text-gray-500">بله، ارسال به تمام نقاط ایران از طریق پست و تیپاکس انجام می‌شود.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>