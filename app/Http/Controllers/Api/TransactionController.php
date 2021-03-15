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

class TransactionController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * @throws Exception
     */
    public function index(IndexRequest $request)
    {
        $action = new ActionFilter(request()->get('action', 'paginate:14'));
        $model = new Transaction;

        $query = QueryBuilder::for(Transaction::class)
            ->allowedFields($model->getAbstractAllowedFields())
            ->allowedFilters(array_merge($model->getAbstractAllowedFilters(), [
                Filter::scope('q'),
            ]))
            ->allowedIncludes($model->getAbstractAllowedRelationships())
            ->allowedSorts($model->getAbstractAllowedSorts())
            ->whereHas('account', function ($query) use ($request): void {
                $query->whereIn('access_token_id', $request->user()->accessTokens->map->id);
            });

        return $this->json($action->execute($query));
    }

    public function store(StoreRequest $request)
    {
        /** @var AbstractEloquentModel $resource */
        $resource = new Transaction;
        $resource->fill($request->validated());
        $resource->save();

        return $this->json($resource->refresh());
    }

    public function show(ViewRequest $request, Transaction $model)
    {
        $result = QueryBuilder::for(Transaction::class)
            ->allowedFields($model->getAbstractAllowedFields())
            ->allowedFilters($model->getAbstractAllowedFilters())
            ->allowedIncludes($model->getAbstractAllowedRelationships())
            ->allowedSorts($model->getAbstractAllowedSorts())
            ->find($model->id);

        if (empty($result)) {
            return $this->json([
                'message' => 'No resource found by that id.',
            ], 404);
        }

        return $this->json($result);
    }

    public function update(UpdateRequest $request, Transaction $transaction)
    {
        $transaction->update($request->all());

        return $this->json($transaction->refresh());
    }

    public function destroy(DestroyRequest $request, Transaction $transaction)
    {
        $transaction->delete();

        return $this->json('', 204);
    }

    public function tag(TagRequest $request, Transaction $transaction)
    {
        $transaction->attachTag($request->get('tag'));

        return $this->json('', 204);
    }

    public function untag(UntagRequest $request, Transaction $transaction)
    {
        $transaction->delete();

        return $this->json('', 204);
    }
}
