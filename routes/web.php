<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::middleware(['auth:sanctum', 'verified', 'subscribed'])->get('/games', function () {
    return view('games');
})->name('games');

Route::middleware(['auth:sanctum', 'verified'])->get('/subscribe', function () {
    return view('subscribe', [
        'intent' => auth()->user()->createSetupIntent()
    ]);
})->name('subscribe');

Route::middleware(['auth:sanctum', 'verified'])->post('/subscribe', function (Request $request) {
    $request->user()->newSubscription(
        'games', $request->plan
    )->create($request->stripeToken);
    auth()->user()->invoiceFor('games', 20);
    return redirect()->route('dashboard');
})->name('subscribe.post');

Route::middleware(['auth:sanctum', 'verified'])->get('/invoices', function(Request $request){
    return view('invoices', [
        'invoices'=>auth()->user()->invoices(),
    ]);
})->name('invoices');

Route::middleware(['auth:sanctum', 'verified'])->get('/user/invoice/{invoice}', function (Request $request, $invoiceId) {
    return $request->user()->downloadInvoice($invoiceId, [
        'vendor' => 'Test Company',
        'product' => 'Your Product',
        'street' => 'Main Str. 1',
        'location' => '2000 Antwerp, Belgium',
        'phone' => '+32 499 00 00 00',
        'email' => 'info@example.com',
        'url' => 'https://example.com',
        'vendorVat' => 'BE123456789',
    ], 'my-invoice');
})->name('invoice.download');
