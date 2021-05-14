<?php
declare(strict_types=1);

use App\Http\Controllers\Api\ActionController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\DynamicViewController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Plaid\CreateLinkTokenController;
use App\Http\Controllers\Plaid\TokenController;
use App\Http\Controllers\Plaid\UpdateLinkTokenController;
use App\Http\Controllers\WebhookController;
use App\Models\Transaction;
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

Route::macro('abstractRoute', function ($variableName, $controller, $resolveBinding): void {
    Route::bind($variableName . '_id', $resolveBinding);

    Route::get(sprintf('abstract-api/%ss', $variableName), [$controller, 'index']);
    Route::post(sprintf('abstract-api/%ss', $variableName), [$controller, 'store']);
    Route::get(sprintf('abstract-api/%ss/{%s_id}', $variableName, $variableName), [$controller, 'show']);
    Route::put(sprintf('abstract-api/%ss/{%s_id}', $variableName, $variableName), [$controller, 'update']);
    Route::patch(sprintf('abstract-api/%ss/%sl_id}', $variableName, $variableName), [$controller, 'update']);
    Route::delete(sprintf('abstract-api/%ss/{%s_id}', $variableName, $variableName), [$controller, 'destroy']);
});

Route::get('/', static fn () => view('welcome'))->middleware('auth');

Route::group(['namespace' => 'App\\Http\\Controllers'], static function (): void {
    Auth::routes(['register' => true]);
});

Route::post('webhook/plaid', WebhookController::class)->name('webhook');

Route::group(['middleware' => 'auth'], static function (): void {
    Route::get('/home', HomeController::class . '@index')->name('home');
    Route::get('/{view}', DynamicViewController::class . '@index')->middleware('auth');

    Route::group(['prefix' => 'abstract-api'], function (): void {
        Route::apiResource('categories', App\Http\Controllers\Api\CategoryController::class);
        Route::apiResource('transactions', App\Http\Controllers\Api\TransactionController::class);
        Route::apiResource('access_tokens', App\Http\Controllers\Api\AccessTokenController::class);
        Route::apiResource('groups', App\Http\Controllers\Api\TagController::class);
        Route::apiResource('alerts', App\Http\Controllers\Api\AlertController::class);
        Route::apiResource('failed_jobs', App\Http\Controllers\Api\FailedJobController::class);
        Route::apiResource('budgets', App\Http\Controllers\Api\BudgetController::class);
        Route::apiResource('activities', App\Http\Controllers\Api\ActivitiesController::class);
    });

    Route::group(['prefix' => 'api'], function (): void {
        Route::get('user', function () {
            $user = auth()->user();
            $user->load(['accessTokens', 'unreadNotifications']);

            return $user;
        });
        Route::put('user', function () {
            $user = auth()->user();
            $user->update([
                'alert_channels' => request()->get('alert_channels', []),
            ]);

            return $user;
        });
        Route::put('read-notification/{notification}', function (Illuminate\Http\Request $request, App\Models\DatabaseNotification $notification) {
            $notification->markAsRead();

            return response('', 204);
        });

        Route::apiResource('budgets', App\Http\Controllers\Api\BudgetController::class);
        Route::apiResource('accounts', App\Http\Controllers\Api\AccountController::class);
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

        Route::abstractRoute('transaction', TransactionController::class, fn ($value) => Transaction::findOrFail($value));
        Route::abstractRoute('budget', App\Http\Controllers\Api\BudgetController::class, fn ($value) => App\Budget::findOrFail($value));
        Route::abstractRoute('budgets', App\Http\Controllers\Api\BudgetController::class, fn ($value) => App\Budget::findOrFail($value));

        Route::get('budgets/{budget}/total_spends', [App\Http\Controllers\Api\BudgetController::class, 'totalSpends']);
        Route::put('budgets/{budget}/tags', [App\Http\Controllers\Api\BudgetController::class, 'tags']);

        Route::get('data/{type}:{model}', \App\Http\Controllers\Api\UglyChartController::class);
    });
});
