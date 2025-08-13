<?php

namespace App\Livewire\Home;

use App\Models\Product;
use Livewire\Component;

class ProductPage extends Component
{
    public string $title;
    public Product $product;
    public string $message = '';

    public function mount(): void
    {
        $this->product = Product::query()->where('dashed_title', '=', $this->title)->firstOrFail();

//        if (session()->exists('callback-download') &&  Transaction::query()->where('product_id', $this->product->id)
//                ->where('user_id', \Auth::id())
//                ->where('status' , '=' , "1")->exists()) {
//            $this->message = 'پرداخت با موفقیت انجام شد. کد پیگیری:' . session()->pull('callback-download');
//        }
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
