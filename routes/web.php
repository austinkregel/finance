<?php

use App\Http\Controllers\Api\ActionController;
use App\Http\Controllers\Api\SubscriptionAsEventController;
use App\Http\Controllers\Api\SubscriptionsController;
use App\Http\Controllers\DynamicViewController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Plaid\TokenController;

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

Route::get('/', static function () {
    return view('welcome');
})->middleware('auth');

Route::group(['namespace' => 'App\\Http\\Controllers'], static function () {
    Auth::routes(['register' => true]);
});

Route::group(['middleware' => 'auth'], static function () {
    Route::get('/home', HomeController::class.'@index')->name('home');
    Route::get('/{view}', DynamicViewController::class.'@index')->middleware('auth');

    Route::group(['prefix' => 'api', 'middleware' => ['auth']], function () {
        Route::get('user', function () {
            $user = auth()->user();
            $user->load(['accounts']);
            return $user;
        });
        Route::post('actions/{action}', ActionController::class);
        Route::post('plaid/exchange_token', TokenController::class);

        Route::apiResource('tags', App\Http\Controllers\Api\TagController::class);
        Route::post('tags/{tag}/conditionals', [App\Http\Controllers\Api\TagController::class, 'conditionals']);
        Route::apiResource('transactions', App\Http\Controllers\Api\TransactionController::class);
        Route::apiResource('accounts', App\Http\Controllers\Api\AccountController::class);
    });
});

