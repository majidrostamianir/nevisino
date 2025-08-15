<div class="h-[86vh]">
    <div class="sm:flex justify-between">
        <div class="sm:ml-2 w-full bg-pars-100 rounded-2xl shadow-md p-4 sm:mr-2 h-[86vh]">
            <livewire:admin.url.url :urls="$urls"  :menus="$menus" />
        </div>
        <div class="sm:mr-2 w-full bg-pars-100 rounded-2xl shadow-md p-4 sm:ml-2 h-[86vh]">
            <livewire:admin.url.url-product :url="$url"  />
        </div>
    </div>
</div>
