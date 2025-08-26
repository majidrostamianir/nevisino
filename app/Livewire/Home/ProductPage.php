<?php

namespace App\Livewire\Home;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class ProductPage extends Component
{
    public string $title;
    public Product $product;
    public string $message = '';
    public int $count = 1;
    public string $src;
    public Collection $images;
    public string $selectedVariant;

    public function mount(): void
    {
        $this->product = Product::query()->where('dashed_title', '=', $this->title)->firstOrFail();
        $path = 'products/' . $this->product->id . '/large';

        $this->images = collect(Storage::disk('public')->files($path))
            ->map(fn($file) => pathinfo($file, PATHINFO_FILENAME)) // گرفتن فقط اسم بدون پسوند
            ->sortBy(fn($name) => intval($name)) // مرتب‌سازی عددی
            ->values(); // ریسِت کلیدها

        $this->src = asset('storage/products/' . $this->product->id . '/large/' . $this->images[0] . '.webp');

    }

    public function updatedSelectedVariant($id)
    {
        if ($id != "")
            $this->setImage($id);
    }

    public function setImage($id)
    {
        $this->src = asset('storage/products/' . $this->product->id . '/large/' . $id . '.webp');
    }

    public function increase()
    {
        $this->count++;
    }

    public function decrease()
    {
        if ($this->count > 1) {
            $this->count--;
        }
    }

    public function addToCart()
    {

    }

//    public function makePayment()
//    {
//        if (!\Auth::check()) {
//            session()->put('product_url', route('product-page', ['title' => $this->product->dashed_title]));
//            return $this->redirect('/register', navigate: true);
//        }
//        $invoice = (new Invoice)->amount(Category::query()->find($this->product->category_id)->price);
//        $payment = Payment::purchase($invoice, function ($driver, $transactionId) {
//            Transaction::query()->create([
//                'product_id' => $this->product->id,
//                'user_id' => auth()->id(),
//                'authority' => (string)$transactionId,
//                'price' => Category::query()->find($this->product->category_id)->price,
//            ]);
//        });
//        return redirect()->away($payment->pay()->getAction());
//
//    }


    public function render(): \Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
    {
        $product = $this->product;
        $similar = Product::whereHas('urls', function ($query) use ($product) {
            $query->whereIn('urls.id', $product->urls->pluck('id'));
        })
            ->where('id', '!=', $product->id) // خود محصول فعلی رو نیاره
            ->distinct()
            ->get();
        return view('livewire.home.product-page', compact('product', 'similar'))->layout('components.layouts.product');
    }
}
