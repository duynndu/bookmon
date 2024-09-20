<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\HomeController;

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

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/login', function () {
   return view('client.pages.auth.login');
});
Route::get('/register', function () {
   return view('client.pages.auth.register');
});

Route::get('/profile', function () {
   return view('client.pages.profile.info');
});
Route::get('/profile-pass', function () {
   return view('client.pages.profile.change-pass-word');
});
Route::get('/profile-change-info', function () {
   return view('client.pages.profile.change-info');
});
Route::get('/profile-history-ticket', function () {
   return view('client.pages.profile.history-ticket');
});



Route::get('/phim', function () {
   return view('client.pages.movie');
});
Route::get('/phim-chi-tiet', function () {
   return view('client.pages.movie-detail');
});

Route::get('/chi-tiet-tin', function () {
   return view('client.pages.post-detail');
});
Route::get('/gia-ve', function () {
   return view('client.pages.ticket-price');
});
Route::get('/khuyen-mai', function () {
   return view('client.pages.promotion');
});

Route::get('/danh-gia', function () {
   return view('client.pages.review');
});

Route::get('/dich-vu', function () {
   return view('client.pages.service');
});

Route::get('/lich-chieu', function () {
   return view('client.pages.showtime');
});

Route::get('/dat-ve', function () {
   return view('client.pages.buy-ticket');
});
Route::get('/dat-ve/xac-nhan', function () {
   return view('client.pages.payment-verification');
});

Route::get('/dat-ve/thanh-toan', function () {
   return view('client.pages.payment-verification-step2');
});


