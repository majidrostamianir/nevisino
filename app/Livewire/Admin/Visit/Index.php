<?php

namespace App\Livewire\Admin\Visit;

use App\Models\Visit;
use Livewire\Component;

class Index extends Component
{
    public $visits;
    public $searchQuery;

    public function mount()
    {
        $this->visits = Visit::query()
            ->take(500)
            ->latest()
            ->get();
    }
    public function updatedSearchQuery()
    {
        $this->visits = Visit::search($this->searchQuery );
    }
    public function render()
    {

        return view('livewire.admin.visit.index')->layout('components.layouts.admin');
    }
}
