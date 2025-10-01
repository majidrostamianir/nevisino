<?php

namespace App\Livewire\Admin\Visit;

use App\Models\Visit;
use Livewire\Component;

class Index extends Component
{
    public $visits;
    public $queryIp , $queryUser;

    public function mount()
    {
        $this->visits = Visit::query()->orderBy('created_at', 'desc')
            ->take(100)
            ->get();
    }
    public function updatedQueryIp()
    {
        $this->visits = Visit::ipSearch($this->queryIp , 100);
    }
    public function updatedQueryUser()
    {
        $this->visits = Visit::userSearch($this->queryUser , 100);
    }
    public function render()
    {

        return view('livewire.admin.visit.index')->layout('components.layouts.admin');
    }
}
