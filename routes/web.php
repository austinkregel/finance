<?php
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
    if (auth()->check()) {
        return redirect('/home');
    }
    return view('welcome');
});

Route::group(['namespace' => 'App\\Http\\Controllers'], static function () {
    Auth::routes();
});

Route::group(['middleware' => 'auth'], static function () {
    Route::get('/home', HomeController::class.'@index')->name('home');
    Route::get('/{view}', DynamicViewController::class.'@index')->middleware('auth');

    Route::group(['prefix' => 'api', 'middleware' => ['auth']], function () {
        Route::get('subscription-event', SubscriptionAsEventController::class . '@events');
        Route::get('subscription', SubscriptionAsEventController::class . '@index');
        Route::get('subscriptions2', SubscriptionsController::class)->middleware('filter');
        Route::post('plaid/exchange_token', TokenController::class);
    });
});
