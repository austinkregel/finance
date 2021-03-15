<?php

use App\Http\Controllers\Api\ActionController;
use App\Http\Controllers\DynamicViewController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Plaid\CreateLinkTokenController;
use App\Http\Controllers\Plaid\TokenController;
use App\Http\Controllers\Plaid\UpdateLinkTokenController;
use App\Http\Controllers\WebhookController;

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

Route::get('/', static fn () => view('welcome'))->middleware('auth');

Route::group(['namespace' => 'App\\Http\\Controllers'], static function (): void {
    Auth::routes(['register' => true]);
});

Route::post('webhook/plaid', WebhookController::class);

Route::group(['middleware' => 'auth'], static function (): void {
    Route::get('/home', HomeController::class.'@index')->name('home');
    Route::get('/{view}', DynamicViewController::class.'@index')->middleware('auth');

    Route::group(['prefix' => 'api', 'middleware' => ['auth']], function (): void {
        Route::get('user', function () {
            $user = auth()->user();
            $user->load(['accessTokens', 'unreadNotifications' ]);

            return $user;
        });
        Route::put('user', function () {
            $user = auth()->user();
            $user->update([
                'alert_channels' => request()->get('alert_channels', [])
            ]);

            return $user;
        });
        Route::put('read-notification/{notification}', function (\Illuminate\Http\Request $request, \App\Models\DatabaseNotification $notification) {
            $notification->markAsRead();

            return response('', 204);
        });

        Route::post('actions/{action}', ActionController::class);
        Route::post('plaid/create-link-token', CreateLinkTokenController::class);
        Route::post('plaid/update-access-token', UpdateLinkTokenController::class);
        Route::post('plaid/exchange-token', TokenController::class);
        Route::post('cache-clear', App\Http\Controllers\Api\CacheController::class);

        Route::apiResource('alerts', App\Http\Controllers\Api\AlertController::class);
        Route::post('alerts/{alert}/conditionals', [App\Http\Controllers\Api\AlertController::class, 'conditionals']);
        Route::put('alerts/{alert}/conditionals/{condition}', [App\Http\Controllers\Api\AlertController::class, 'updateConditional']);
        Route::patch('alerts/{alert}/conditionals/{condition}', [App\Http\Controllers\Api\AlertController::class, 'updateConditional']);
        Route::delete('alerts/{alert}/conditionals/{condition}', [\App\Http\Controllers\Api\AlertController::class, 'deleteConditional']);

        Route::apiResource('tags', App\Http\Controllers\Api\TagController::class);

        Route::post('tags/{tag}/conditionals', [App\Http\Controllers\Api\TagController::class, 'conditionals']);
        Route::put('tags/{tag}/conditionals/{condition}', [App\Http\Controllers\Api\TagController::class, 'updateConditional']);
        Route::patch('tags/{tag}/conditionals/{condition}', [App\Http\Controllers\Api\TagController::class, 'updateConditional']);
        Route::delete('tags/{tag}/conditionals/{condition}', [\App\Http\Controllers\Api\TagController::class, 'deleteConditional']);

        Route::apiResource('transactions', App\Http\Controllers\Api\TransactionController::class);
        Route::apiResource('accounts', App\Http\Controllers\Api\AccountController::class);
        Route::apiResource('budgets', App\Http\Controllers\Api\BudgetController::class);

        Route::get('budgets/{budget}/total_spends', [App\Http\Controllers\Api\BudgetController::class, 'totalSpends']);
        Route::put('budgets/{budget}/tags', [App\Http\Controllers\Api\BudgetController::class, 'tags']);

        Route::get('data/{type}:{model}', \App\Http\Controllers\Api\UglyChartController::class);
    });
});
