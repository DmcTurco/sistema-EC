<?php

use Illuminate\Support\Facades\Route;
use App\MyApp;
use App\Http\Controllers\Admin as Admin;
use App\Http\Controllers\Staff as Staff;

Route::get('/', function () {
    return view('welcome');
});


Route::prefix(MyApp::ADMINS_SUBDIR)->middleware('auth:admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.home');
    })->withoutMiddleware('auth:admin');
    
    Route::get('/home', [Admin\AdminController::class, 'index'])->name('home');
    Route::resource('products', Admin\ProductController::class);
    Route::resource('stores', Admin\StoreController::class);
    Route::resource('staff', Staff\StaffController::class);
});

Route::prefix(MyApp::STAFF_SUBDIR)->middleware('auth:staff')->name('staff.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('staff.home');
    })->withoutMiddleware('auth:staff');
    
    Route::get('/home', [Staff\HomeController::class, 'index'])->name('home');
    Route::resource('post', Staff\PostController::class);

});