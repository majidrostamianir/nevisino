<?php

use App\Http\Controllers\BasalamTestController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => [\App\Http\Middleware\visitTracker::class]], function () {
    Route::get('/', \App\Livewire\Home\Index::class)->name('home');
    Route::get('/shop', \App\Livewire\Home\Shop::class)->name('shop');
});


Route::group(['middleware' => ['throttle:60', \App\Http\Middleware\getReferrer::class , \App\Http\Middleware\visitTracker::class]], function () {
    Route::get('/category/{dashed}', \App\Livewire\Home\CategoryPage::class)->name('category-page');
    Route::get('/product/{title}', \App\Livewire\Home\ProductPage::class)->name('product-page');
    Route::get('/cart', \App\Livewire\Payment\Cart::class)->name('cart');
});
Route::group(['middleware' => ['throttle:60', 'guest', \App\Http\Middleware\getReferrer::class,\App\Http\Middleware\visitTracker::class]], function () {
    Route::get('/register', \App\Livewire\Auth\Register::class)->name('register');
    Route::get('/login', \App\Livewire\Auth\Login::class)->name('login');
    Route::get('/forget', \App\Livewire\Auth\ForgetPassword::class)->name('forget');
//    Route::get('/reset', \App\Livewire\Auth\ResetPassword::class)->name('reset-password');
    Route::get('/verify-mobile', \App\Livewire\Auth\VerifyMobile::class)->name('verify-mobile');

});


Route::group(['middleware' => ['throttle:60', 'auth' , \App\Http\Middleware\visitTracker::class]], function () {
    Route::get('/dashboard/{page?}', App\Livewire\Dashboard\Index::class)
        ->whereAlpha('page')
        ->name('dashboard');
    Route::get('/checkout', \App\Livewire\Payment\Checkout::class)->name('checkout');
    Route::get('/payment/callback', \App\Livewire\Payment\Callback::class)->name('payment.callback');
});


Route::group(['middleware' => [\App\Http\Middleware\isOwner::class, 'throttle:60', 'auth' ]], function () {
    Route::get('/admin/url', \App\Livewire\Admin\Url\Index::class)->name('admin.url.index');
    Route::get('/admin/product', \App\Livewire\Admin\Product\Index::class)->name('admin.product.index');
    Route::get('/admin/product/save/{product?}', \App\Livewire\Admin\Product\Save::class)->name('admin.product.save');
    Route::get('/admin/user', \App\Livewire\Admin\User\Index::class)->name('admin.user.index');
    Route::get('/admin/user/order/{user}', \App\Livewire\Admin\User\Order::class)->name('admin.user.order');
    Route::get('/admin/order', \App\Livewire\Admin\Order\Index::class)->name('admin.order.index');
    Route::get('/admin/setting', \App\Livewire\Admin\Setting\Index::class)->name('admin.setting.index');
    Route::get('/admin/static', \App\Livewire\Admin\Static\Index::class)->name('admin.static.index');
    Route::get('/admin/visit', \App\Livewire\Admin\Visit\Index::class)->name('admin.visit.index');
    /*
     *
     * Use For Basalam
     * Add Products in Basalam
     *
     */
    Route::get('/basalam/test', [BasalamTestController::class, 'send']);

});

/*
     *
     * Use For Emalls
     * List Of Products
     *
     */
Route::match(['get', 'post'], '/list', [\App\Http\Controllers\EmallsProductsController::class, 'list']);
