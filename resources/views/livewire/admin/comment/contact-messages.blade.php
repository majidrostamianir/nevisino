<div>
    <div class="p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">📝 پیام‌های تماس با ما</h1>

        <div class="space-y-4">
            @forelse($messages as $message)
                <div class="bg-white rounded-xl shadow-sm border {{ $message->is_read ? 'border-gray-200' : 'border-[#0060E0] border-r-4' }} p-5">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-bold text-gray-800">{{ $message->name }}</h3>
                                @if($message->user_id)
                                    <span class="text-[10px] bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        عضو سایت
                                    </span>
                                @endif
                            </div>
                            <p class="text-gray-500 text-sm">{{ $message->contact }}</p>
                            @if($message->user_id)
                                <p class="text-gray-400 text-xs mt-1">🆔 آیدی کاربر: {{ $message->user_id }}</p>
                            @endif
                        </div>
                        <div class="text-left">
                            <p class="text-gray-400 text-xs">{{ $message->created_at->format('Y/m/d H:i') }}</p>
                            @if(!$message->is_read)
                                <span class="text-xs bg-[#0060E0] text-white px-2 py-0.5 rounded-full">جدید</span>
                            @endif
                        </div>
                    </div>
                    <p class="text-gray-600 text-sm leading-relaxed mb-4">{{ $message->message }}</p>
                    <div class="flex gap-2 justify-end">
                        @if(!$message->is_read)
                            <button wire:click="markAsRead({{ $message->id }})" class="text-green-600 text-sm hover:text-green-700 transition">
                                ✓ تایید شده
                            </button>
                        @endif
                        <button wire:click="delete({{ $message->id }})" onclick="confirm('آیا از حذف این پیام مطمئن هستید؟') || event.stopImmediatePropagation()" class="text-red-500 text-sm hover:text-red-700 transition">
                            🗑️ حذف
                        </button>
                    </div>
                </div>
            @empty
                <div class="bg-gray-50 rounded-xl p-8 text-center">
                    <p class="text-gray-400">📭 هیچ پیامی یافت نشد.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $messages->links() }}
        </div>
    </div>
</div>