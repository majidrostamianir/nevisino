<?php

namespace App\Livewire\Components;

use Livewire\Component;

class Tooltip extends Component
{
    public string $text;
    public string $position;

    /**
     * Create a new component instance.
     */
    public function __construct(string $text = '', string $position = 'bottom')
    {
        $this->text = $text;
        $this->position = $position;
    }

    public function render()
    {
        return view('livewire.components.tooltip');
    }
}
