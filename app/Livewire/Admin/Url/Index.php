<?php

namespace App\Livewire\Admin\Url;

use App\Models\Url;
use Livewire\Component;

class Index extends Component
{

    public function toggleIndexing($id)
    {
        $url = Url::query()->find($id);
        $url->update([
            'indexing' => !$url->indexing,
        ]);
    }

    public function toggleFollowing($id)
    {
        $url = Url::query()->find($id);
        $url->update([
            'following' => !$url->following,
        ]);
    }

    public function render()
    {
        $urls = \App\Models\Url::query()->orderBy('in_menu', 'desc')->get();
        return view('livewire.admin.url.index', compact('urls'))->layout('components.layouts.admin');
    }
}
