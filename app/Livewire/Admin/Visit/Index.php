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
            ->take(500)
            ->get();
    }
    public function updatedQueryIp()
    {
        $this->visits = Visit::ipSearch($this->queryIp );
    }
    public function updatedQueryUser()
    {
        $this->visits = Visit::userSearch($this->queryUser );
    }
    public function render()
    {

        return view('livewire.admin.visit.index')->layout('components.layouts.admin');
    }
}
