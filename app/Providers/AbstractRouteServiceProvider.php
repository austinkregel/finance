<?php

namespace App\Providers;

use App\Activity;
use App\Budget;
use App\FailedJob;
use App\Models\AccessToken;
use App\Models\Account;
use App\Models\Alert;
use App\Models\Category;
use App\Models\Transaction;
use App\Tag;
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
                'access_tokens' => AccessToken::class,
                'groups' => Tag::class,
                'alerts' => Alert::class,
                'failed_jobs' => FailedJob::class,
                'budgets' => Budget::class,
                'activities' => Activity::class,
            ]);

        Route::bind('abstract_model', abstracted()->resolveModelsUsing ?? function ($value) {
            $class = abstracted()->route($value);

            $model = new $class;

            throw_if(! $model instanceof AbstractEloquentModel, ModelNotInstanceOfAbstractEloquentModel::class);

            return $model;
        });

        Route::bind('abstract_model_id', abstracted()->resolveModelsUsing ?? function ($value) {
            $class = request()->route('abstract_model');

            $model = new $class;

            throw_if(! $model instanceof AbstractEloquentModel, ModelNotInstanceOfAbstractEloquentModel::class);

            return $model::find($value);
        });
    }
}
