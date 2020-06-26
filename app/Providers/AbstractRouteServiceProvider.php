<?php

namespace App\Providers;

use App\AccountKpi;
use App\FailedJob;
use App\Models\Alert;
use App\Tag;
use App\Http\Controllers\Api\AbstractResourceController;
use App\Http\Controllers\Api\AfterRequestSortController;
use App\Models\AccessToken;
use App\Models\Account;
use App\Models\Action;
use App\Models\Category;
use App\Models\Institution;
use App\Models\Transaction;
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
                'account-kpis' => AccountKpi::class,
                'access_tokens' => AccessToken::class,
                'groups' => Tag::class,
                'alerts' => Alert::class,
                'failed_jobs' => FailedJob::class,
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
            ->namespace('App\Http\Controllers\Api')
            ->group(function () {
                Route::get('abstract-api/{abstract_model}/fields', 'AbstractResourceController@fields');
            });
        Route::middleware(abstracted()->middlewareGroup)
            ->group(function () {
                Route::get('abstract-api/{abstract_model}', [AbstractResourceController::class, 'index']);
                Route::post('abstract-api/{abstract_model}', [AbstractResourceController::class, 'store']);
                Route::get('abstract-api/{abstract_model}/{id}', [AbstractResourceController::class, 'show']);
                Route::put('abstract-api/{abstract_model}/{id}', [AbstractResourceController::class, 'update']);
                Route::patch('abstract-api/{abstract_model}/{id}', [AbstractResourceController::class, 'update']);
                Route::delete('abstract-api/{abstract_model}/{id}', [AbstractResourceController::class, 'destroy']);
            });
    }
}
