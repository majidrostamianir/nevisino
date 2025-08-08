<div class="h-[86vh]">
    <div >
        <div class="h-[25vh]  w-full bg-pars-100 rounded-2xl shadow-md p-4 sm:ml-2">
{{--            <livewire:admin.url.category :categories="$categories" />--}}
        </div>
    </div>
    <div class="sm:flex justify-between mt-2">
        <div class="sm:ml-2 w-full bg-pars-100 rounded-2xl shadow-md p-4 sm:mr-2 h-[60vh]  ">
            <livewire:admin.url.url :urls="$urls"  :categories="$categories" />
        </div>
        <div class="sm:mr-2 w-full bg-pars-100 rounded-2xl shadow-md p-4 sm:ml-2 h-[60vh] ">
            <livewire:admin.url.url-product :url="$url"  />
        </div>

    </div>
</div>
