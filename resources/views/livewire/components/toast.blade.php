<div>
    <style>
        /* استایل‌های نوتیفیکیشن */
        .notification-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
            max-width: 350px;
            width: 100%;
        }

        .notification {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            padding: 16px;
            margin-bottom: 10px;
            border-left: 4px solid;
            display: flex;
            align-items: flex-start;
            animation: slideIn 0.3s ease-out, shake 0.5s ease-in-out 2;
        }

        .notification-success {
            border-left-color: #10b981;
        }

        .notification-error {
            border-left-color: #ef4444;
        }

        .notification-warning {
            border-left-color: #f59e0b;
        }

        .notification-info {
            border-left-color: #3b82f6;
        }

        .notification-primary {
            border-left-color: #8b5cf6;
        }

        .notification-secondary {
            border-left-color: #6b7280;
        }

        .notification-icon {
            flex-shrink: 0;
            margin-right: 12px;
            width: 24px;
            height: 24px;
        }

        .notification-content {
            flex: 1;
            padding-top: 2px;
        }

        .notification-message {
            font-size: 14px;
            font-weight: 500;
            color: #1f2937;
            margin: 0;
        }

        .notification-close {
            background: none;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            padding: 4px;
            margin-left: 8px;
            flex-shrink: 0;
        }

        .notification-close:hover {
            color: #6b7280;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }

        @keyframes shake {
            0%, 100% {
                transform: translateX(0);
            }
            10%, 30%, 50%, 70%, 90% {
                transform: translateX(-5px);
            }
            20%, 40%, 60%, 80% {
                transform: translateX(5px);
            }
        }

        .notification-leave {
            animation: slideOut 0.3s ease-in forwards;
        }
    </style>

    @if($show)
        <div class="notification-container" wire:key="notification-{{ $notificationId }}">
            <div id="notification-{{ $notificationId }}" class="notification notification-{{ $type }}">
                <!-- محتوا -->
                <div class="notification-content">
                    <p class="notification-message">{!! $message !!}</p>
                </div>
                <!-- دکمه بستن با wire:click مستقیم -->
                <button class="notification-close" wire:click="hide">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                              d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                              clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        </div>
    @endif

{{--        <script>--}}
{{--            document.addEventListener('livewire:init', () => {--}}
{{--                Livewire.on('start-notification-timer', (event) => {--}}
{{--                    setTimeout(() => {--}}
{{--                        const notification = document.getElementById('notification-' + event.notificationId);--}}
{{--                        if (notification) {--}}
{{--                            notification.classList.add('notification-leave');--}}
{{--                            setTimeout(() => {--}}
{{--                                @this.call('hide');--}}
{{--                            }, 300);--}}
{{--                        }--}}
{{--                    }, 3000);--}}
{{--                });--}}
{{--            });--}}
{{--        </script>--}}
</div>
