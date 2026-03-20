<?php

use App\Http\Controllers\BasalamTestController;
use Illuminate\Support\Facades\Route;




Route::group(['middleware' => ['throttle:60' , \App\Http\Middleware\visitTracker::class]], function () {
    Route::get('/', \App\Livewire\Home\Index::class)->name('home');
    Route::get('/shop', \App\Livewire\Home\Shop::class)->name('shop');
    Route::get('/category/{dashed}', \App\Livewire\Home\CategoryPage::class)->name('category-page');
    Route::get('/product/{title}', \App\Livewire\Home\ProductPage::class)->name('product-page');
    Route::get('/cart', \App\Livewire\Payment\Cart::class)->name('cart');
    Route::get('/trust', \App\Livewire\Home\Trust::class)->name('trust');

});

Route::group(['middleware' => ['throttle:60', 'guest',\App\Http\Middleware\visitTracker::class]], function () {
    Route::get('/register', \App\Livewire\Auth\Register::class)->name('register');
    Route::redirect('/login', '/register' , 301)->name('login');
    Route::get('/verify-mobile', \App\Livewire\Auth\VerifyMobile::class)->name('verify-mobile');

});

Route::group(['middleware' => ['throttle:60', 'auth' , \App\Http\Middleware\visitTracker::class]], function () {
    Route::get('/dashboard/{page?}', App\Livewire\Dashboard\Index::class)
        ->whereAlpha('page')
        ->name('dashboard');
    Route::get('/dashboard/order', App\Livewire\Dashboard\Order::class)->name('dashboard.order');
    Route::get('/checkout', \App\Livewire\Payment\Checkout::class)->name('checkout');
    Route::get('/payment/callback', \App\Livewire\Payment\Callback::class)->name('payment.callback');
});


Route::group(['middleware' => [\App\Http\Middleware\isOwner::class, 'throttle:60', 'auth' ]], function () {
    Route::get('/admin/category', \App\Livewire\Admin\Category\Index::class)->name('admin.category.index');
    Route::get('/admin/url', \App\Livewire\Admin\Url\Index::class)->name('admin.url.index');
    Route::get('/admin/url-product/{url}', \App\Livewire\Admin\Url\UrlProduct::class)->name('admin.url.product');
    Route::get('/admin/product', \App\Livewire\Admin\Product\Index::class)->name('admin.product.index');
    Route::get('/admin/product/save/{product?}', \App\Livewire\Admin\Product\Save::class)->name('admin.product.save');
    Route::get('/admin/user', \App\Livewire\Admin\User\Index::class)->name('admin.user.index');
    Route::get('/admin/user/order/{user}', \App\Livewire\Admin\User\Order::class)->name('admin.user.order');
    Route::get('/admin/order', \App\Livewire\Admin\Order\Index::class)->name('admin.order.index');
    Route::get('/admin/setting', \App\Livewire\Admin\Setting\Index::class)->name('admin.setting.index');
    Route::get('/admin/static', \App\Livewire\Admin\Static\Index::class)->name('admin.static.index');
    Route::get('/admin/visit', \App\Livewire\Admin\Visit\Index::class)->name('admin.visit.index');
    Route::get('/admin/attr',\App\Livewire\Admin\Product\Attr::class)->name('admin.product.attr');
});

/*
     *
     * Use For Emalls
     * List Of Products
     *
     */
Route::match(['get', 'post'], '/list', [\App\Http\Controllers\EmallsProductsController::class, 'list']);
