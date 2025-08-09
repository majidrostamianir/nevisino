<div>
    <div class="w-full m-2">
        <div class="relative w-full h-64 border-2 p-2 border-pars-500 border-dashed rounded-lg  bg-pars-100 hover:bg-pars-200 transition-all">
            <label for="dropzone-picture"
                   class="flex flex-col items-center justify-center w-full cursor-pointer ">
                <div class="flex flex-col items-center justify-center ">
                    <svg class="w-8 h-8 mb-4 text-gray-500" aria-hidden="true"
                         xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"
                              d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                    </svg>
                    <p class="mb-2 text-sm text-gray-500">برای آپلود <span class="font-semibold text-pars-500">تصاویر</span> کلیک کنید</p>
                    <p class="text-xs text-gray-500">حداکثر حجم تصویر 10 مگابایت</p>
                </div>
                @error('picture')
                <span class="text-xs text-red-500 font-semibold">{{ $message }}</span>
                @enderror
{{--                @if(Storage::disk('ftp')->exists('Pictures/' . $this->folder . '/Large/Front/' . $product->id . '.webp')--}}
{{--                    && Storage::disk('ftp')->exists('Pictures/' . $this->folder . '/Average/Front/' . $product->id . '.webp')--}}
{{--                    && Storage::disk('ftp')->exists('Pictures/' . $this->folder . '/Small/Front/' . $product->id . '.webp'))--}}
{{--                    <img width="150" class="rounded-xl"--}}
{{--                         src="https://dl.sungraphic.ir/Pictures/{{ $this->folder}}/Small/Front/{{ $product->id }}.webp?v={{ $imageVersion }}"--}}
{{--                         alt="">--}}
{{--                @else--}}
                    <svg width="80" height="80" class="mt-4" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                        <path d="M100 20
           a80 80 0 1 1 0 160
           a80 80 0 1 1 0-160"
                              fill="none"
                              stroke="#FFA500"
                              stroke-width="12"
                              stroke-linecap="round"/>
                        <rect x="95" y="50" width="10" height="60" fill="#FFA500" rx="3"/>
                        <rect x="95" y="125" width="10" height="15" fill="#FFA500" rx="3"/>
                    </svg>
{{--                @endif--}}
                <input id="dropzone-picture" type="file" class="opacity-0 w-full h-full absolute top-0 left-0" multiple
                       wire:model="picture"/>
            </label>
            <div
                class="backdrop-blur text-center content-center text-pars-500 font-bold absolute top-0 left-0 w-full h-full rounded"
                wire:loading wire:target="picture">
                لطفا صبر کنید...
            </div>
        </div>
    </div>
    @if($uploadProgress < 101)
        <div class="px-8 w-full mt-2">
            <div class="w-full bg-pars-100 rounded-full">
                <div
                    class="bg-pars-500 text-xs font-medium text-white text-center p-0.5 leading-none rounded-full"
                    style="width: {!! $uploadProgress !!}%"> {{ $uploadProgress }}%
                </div>
            </div>
        </div>
    @endif
    <script>
        document.addEventListener('livewire-upload-start', () => {
            console.log('آپلود شروع شد');
        });

        document.addEventListener('livewire-upload-progress', (event) => {
            @this.
            set('uploadProgress', event.detail.progress);
            console.log(event.detail.progress)
        });

        document.addEventListener('livewire-upload-finish', () => {
            @this.
            set('uploadProgress', 101);
            console.log('آپلود کامل شد');
        });

        document.addEventListener('livewire-upload-error', () => {
            console.log('خطا در آپلود');
        });
    </script>

</div>
