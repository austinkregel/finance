<?php

namespace App\Providers;

use App\AccountKpi;
use App\Budget;
use App\FailedJob;
use App\Models\Alert;
use App\Tag;
use App\Http\Controllers\Api\AbstractResourceController;
use App\Models\AccessToken;
use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Kregel\LaravelAbstract\AbstractEloquentModel;
use Kregel\LaravelAbstract\Exceptions\ModelNotInstanceOfAbstractEloquentModel;

class AbstractRouteServiceProvider extends ServiceProvider
{
    public function boot(): void
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
                'budgets' => Budget::class,
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

    public function map(): void
    {
        if (abstracted()->usingRoutes) {
            $this->mapRoutes();
        }
    }

    protected function mapRoutes(): void
    {
        Route::middleware(abstracted()->middlewareGroup)
            ->group(function (): void {
                Route::get('abstract-api/{abstract_model}', [AbstractResourceController::class, 'index']);
                Route::post('abstract-api/{abstract_model}', [AbstractResourceController::class, 'store']);
                Route::get('abstract-api/{abstract_model}/{id}', [AbstractResourceController::class, 'show']);
                Route::put('abstract-api/{abstract_model}/{id}', [AbstractResourceController::class, 'update']);
                Route::patch('abstract-api/{abstract_model}/{id}', [AbstractResourceController::class, 'update']);
                Route::delete('abstract-api/{abstract_model}/{id}', [AbstractResourceController::class, 'destroy']);
            });
    }
}
