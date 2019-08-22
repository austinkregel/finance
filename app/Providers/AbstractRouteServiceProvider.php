<?php

namespace App\Providers;

use App\AccountKpi;
use App\Http\Controllers\Api\AfterRequestSortController;
use App\Models\AccessToken;
use App\Models\Account;
use App\Models\Category;
use App\Models\Institution;
use App\Models\Transaction;
use App\Subscription;
use App\User;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Kregel\LaravelAbstract\AbstractEloquentModel;
use Kregel\LaravelAbstract\Exceptions\ModelNotInstanceOfAbstractEloquentModel;
use Kregel\LaravelAbstract\LaravelAbstract;

class AbstractRouteServiceProvider extends ServiceProvider
{
    public function boot()
    {
        parent::boot();

        abstracted()
            ->middleware(['auth:api'])
            ->bypass(true)
            ->route([
                'accounts' => Account::class,
                'categories' => Category::class,
                'transactions' => Transaction::class,
                'subscriptions' => Subscription::class,
                'account-kpis' => AccountKpi::class,
            ]);

        Route::bind('abstract_model', abstracted()->resolveModelsUsing ?? function ($value) {
            $class = abstracted()->route($value);

            $model = new $class;

            throw_if(!$model instanceof AbstractEloquentModel, ModelNotInstanceOfAbstractEloquentModel::class);

            return $model;
        });

        Route::bind('id', abstracted()->resolveModelsUsing ?? function ($value) {
            $class = request()->route('abstract_model');

            $model = new $class;

            throw_if(!$model instanceof AbstractEloquentModel, ModelNotInstanceOfAbstractEloquentModel::class);

            return $model::find($value);
        });
    }

    public function map()
    {
        if (abstracted()->usingRoutes) {
            $this->mapRoutes();
        }
    }

    protected function mapRoutes()
    {
        Route::middleware(abstracted()->middlewareGroup)
            ->namespace('Kregel\LaravelAbstract\Http\Controllers')
            ->group(function () {
                Route::get('/api/{abstract_model}', [AfterRequestSortController::class, 'index']);
                Route::post('api/{abstract_model}', 'AbstractResourceController@store');
                Route::get('api/{abstract_model}/{id}', 'AbstractResourceController@show');
                Route::put('api/{abstract_model}/{id}', 'AbstractResourceController@update');
                Route::patch('api/{abstract_model}/{id}', 'AbstractResourceController@update');
                Route::delete('api/{abstract_model}/{id}', 'AbstractResourceController@destroy');
            });
    }
}
