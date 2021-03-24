<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\DestroyRequest;
use App\Http\Requests\Transaction\IndexRequest;
use App\Http\Requests\Transaction\StoreRequest;
use App\Http\Requests\Transaction\TagRequest;
use App\Http\Requests\Transaction\UntagRequest;
use App\Http\Requests\Transaction\UpdateRequest;
use App\Http\Requests\Transaction\ViewRequest;
use App\Models\Transaction;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Kregel\LaravelAbstract\AbstractEloquentModel;
use Kregel\LaravelAbstract\Filters\ActionFilter;
use Spatie\QueryBuilder\AllowedFilter as Filter;
use Spatie\QueryBuilder\QueryBuilder;

class TransactionController extends AbstractResourceController
{
    public function __construct(Transaction $model)
    {
        parent::__construct($model);
    }
}
