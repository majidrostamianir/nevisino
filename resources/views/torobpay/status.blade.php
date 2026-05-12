<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>وضعیت پرداخت ترب پی</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">
<div class="max-w-md mx-auto mt-20 p-6">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="p-6">
            <div class="text-center">
                @php
                    $statusColors = [
                        'PENDING' => 'yellow',
                        'VERIFY' => 'blue',
                        'SETTLE' => 'green',
                        'REVERT' => 'red',
                    ];
                    $color = $statusColors[$status] ?? 'gray';
                @endphp
                
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-{{ $color }}-100 mb-4">
                    @if($status === 'PENDING')
                        <svg class="w-8 h-8 text-{{ $color }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    @elseif($status === 'VERIFY' || $status === 'SETTLE')
                        <svg class="w-8 h-8 text-{{ $color }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    @else
                        <svg class="w-8 h-8 text-{{ $color }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    @endif
                </div>
                
                <h2 class="text-2xl font-bold mb-2">وضعیت پرداخت</h2>
                <div class="text-{{ $color }}-600 font-semibold text-lg mb-4">{{ $status_fa }}</div>
                
                <div class="border-t border-b py-4 my-4">
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600">مبلغ:</span>
                        <span class="font-bold">{{ number_format($amount) }} ریال</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">شماره تراکنش:</span>
                        <span class="font-mono text-sm">{{ $transaction_id ?? 'نامشخص' }}</span>
                    </div>
                </div>
                
                <div class="text-sm text-gray-500">
                    وضعیت در دیتابیس: {{ $local_status_fa }}
                </div>
            </div>
            
            <div class="mt-6 flex gap-3">
                <a href="{{ url('/') }}" class="flex-1 bg-blue-500 text-white text-center py-2 rounded-lg hover:bg-blue-600 transition">
                    بازگشت به صفحه اصلی
                </a>
                <a href="{{ route('orders.track', $transaction_id ?? '') }}" class="flex-1 border border-gray-300 text-center py-2 rounded-lg hover:bg-gray-50 transition">
                    پیگیری سفارش
                </a>
            </div>
        </div>
    </div>
</div>
</body>
</html>