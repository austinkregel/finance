<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Account\DestroyRequest;
use App\Models\Account;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Kregel\LaravelAbstract\AbstractEloquentModel;
use Kregel\LaravelAbstract\Filters\ActionFilter;
use App\Http\Requests\Account\StoreRequest;
use App\Http\Requests\Account\IndexRequest;
use App\Http\Requests\Account\UpdateRequest;
use App\Http\Requests\Account\ViewRequest;
use Spatie\QueryBuilder\AllowedFilter as Filter;
use Spatie\QueryBuilder\QueryBuilder;

class AccountController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * @throws Exception
     */
    public function index(IndexRequest $request)
    {
        $action = new ActionFilter(request()->get('action', 'paginate:14'));
        $model = new Account;

        $query = QueryBuilder::for(Account::class)
            ->allowedFields($model->getAbstractAllowedFields())
            ->allowedFilters(array_merge($model->getAbstractAllowedFilters(), [
                Filter::scope('q')
            ]))
            ->allowedIncludes($model->getAbstractAllowedRelationships())
            ->allowedSorts($model->getAbstractAllowedSorts())
            ->whereIn('access_token_id', $request->user()->accessTokens->map->id);

        return $this->json($action->execute($query));
    }

    public function store(StoreRequest $request)
    {
        /** @var AbstractEloquentModel $resource */
        $resource = new Account;
        $resource->fill($request->validated());
        $resource->save();

        return $this->json($resource->refresh());
    }

    public function show(ViewRequest $request, Account $model)
    {
        $result = QueryBuilder::for(Account::class)
            ->allowedFields($model->getAbstractAllowedFields())
            ->allowedFilters($model->getAbstractAllowedFilters())
            ->allowedIncludes($model->getAbstractAllowedRelationships())
            ->allowedSorts($model->getAbstractAllowedSorts())
            ->whereIn('access_token_id', $request->user()->accessTokens->map->id)
            ->find($model->id);

        if (empty($result)) {
            return $this->json([
                'message' => 'No resource found by that id.'
            ], 404);
        }

        return $this->json($result);
    }

    public function update(UpdateRequest $request, Account $Account)
    {
        $Account->update($request->all());

        return $this->json($Account->refresh());
    }

    public function destroy(DestroyRequest $request, Account $Account)
    {
        $Account->delete();

        return $this->json('', 204);
    }
}
