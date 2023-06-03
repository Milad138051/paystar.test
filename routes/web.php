<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SalesProceesController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::prefix('sales-process')->group(function(){
	Route::get('cart',[SalesProceesController::class,'index'])->name('sales-process.cart');
	Route::post('submit',[SalesProceesController::class,'submitPayment'])->name('sales-process.submit');
	Route::post('call-back/payment',[SalesProceesController::class,'callback'])->name('sales-process.callback');
})->middleware('auth');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
