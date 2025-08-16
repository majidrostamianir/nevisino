<div class="h-[86vh]">
    <div>
        <div class="h-[30vh] w-full bg-pars-100 rounded-2xl shadow-md p-4 sm:ml-2 overflow-y-scroll">
            <livewire:admin.url.category />
        </div>
    </div>
    <div class="sm:flex justify-between mt-4">
        <div class="sm:ml-2 w-full bg-pars-100 rounded-2xl shadow-md p-4 sm:mr-2 h-[55vh]">
            <livewire:admin.url.url  :categories="$categories"/>
        </div>
        <div class="sm:mr-2 w-full bg-pars-100 rounded-2xl shadow-md p-4 sm:ml-2 h-[55vh]">
            <livewire:admin.url.url-product />
        </div>
    </div>
</div>
