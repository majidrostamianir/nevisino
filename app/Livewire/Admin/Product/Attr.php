<?php

namespace App\Livewire\Admin\Product;

use Illuminate\Validation\Rule;
use Livewire\Component;

class Attr extends Component
{
    public $categoryId, $title,$value;
    public \App\Models\Attr $attr;

    public function mount()
    {
        $this->attr = new \App\Models\Attr();
    }

    public function save()
    {
        $this->validate([
            'categoryId' => 'required',
            'title' => 'required|string',
            'value' => 'required|string|' . Rule::unique('attrs', 'value')->where('category_id', $this->categoryId)->where('title', $this->title),
        ]);

        $this->attr->category_id = $this->categoryId;
        $this->attr->title = $this->title;
        $this->attr->value = $this->value;
        $this->attr->save();
        $this->title = null;
        $this->value = null;
        $this->attr = new \App\Models\Attr();
    }

    public function setAttr($id)
    {
        $this->attr = \App\Models\Attr::find($id);
        $this->title = $this->attr->title;
        $this->categoryId = $this->attr->category_id;
        $this->value = $this->attr->value;
    }
    public function delAttr($id)
    {
        $this->attr = \App\Models\Attr::find($id);
        $this->attr->delete();
    }

    public function render()
    {
        $attrs = \App\Models\Attr::all()->sortBy(['category_id' , 'title' , 'value']     );
        return view('livewire.admin.product.attr',compact('attrs'))->layout('components.layouts.admin');
    }
}
