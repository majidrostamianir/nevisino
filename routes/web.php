<?php

use Illuminate\Support\Facades\Route;

Route::get('/', \App\Livewire\Home\Index::class)->name('home');


Route::group(['middleware' => ['throttle:60']], function () {
    Route::get('/category/{dashed}' , \App\Livewire\Home\CategoryPage::class)->name('category-page');
    Route::get('/product/{title}', \App\Livewire\Home\ProductPage::class)->name('product-page');
});
Route::group(['middleware' => ['throttle:60', 'guest']], function () {
    Route::get('/register', \App\Livewire\Auth\Register::class)->name('register');
    Route::get('/login', \App\Livewire\Auth\Login::class)->name('login');
    Route::get('/forget', \App\Livewire\Auth\ForgetPassword::class)->name('forget');
    Route::get('/reset', \App\Livewire\Auth\ResetPassword::class)->name('reset-password');
    Route::get('/verify-mobile', \App\Livewire\Auth\VerifyMobile::class)->name('verify-mobile');

});


Route::group(['middleware' => ['throttle:60', 'auth']], function () {
    Route::get('/dashboard', \App\Livewire\Dashboard\Index::class)->name('dashboard');


//    Route::get('/payment/callback', \App\Sun\Home\PaymentCallback::class);
});


Route::group(['middleware' => [\App\Http\Middleware\isOwner::class, 'throttle:60', 'auth']], function () {
    Route::get('/admin/url', \App\Livewire\Admin\Url\Index::class)->name('admin.url.index');
    Route::get('/admin/menu', \App\Livewire\Admin\Url\Menu::class)->name('admin.menu.index');
    Route::get('/admin/product', \App\Livewire\Admin\Product\Index::class)->name('admin.product.index');
    Route::get('/admin/product/save/{product?}', \App\Livewire\Admin\Product\Save::class)->name('admin.product.save');
});
