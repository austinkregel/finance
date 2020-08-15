<?php

namespace App\Http\Controllers\Api;

use App\Condition;
use App\Http\Controllers\Controller;
use App\Http\Requests\Budget\DestroyRequest;
use App\Http\Requests\Budget\IndexRequest;
use App\Http\Requests\Budget\StoreRequest;
use App\Http\Requests\Budget\UpdateRequest;
use App\Http\Requests\Budget\ViewRequest;
use App\Budget;
use Illuminate\Http\Request;
use Kregel\LaravelAbstract\AbstractEloquentModel;
use Kregel\LaravelAbstract\Filters\ActionFilter;
use Spatie\QueryBuilder\AllowedFilter as Filter;
use Spatie\QueryBuilder\QueryBuilder;

class BudgetController extends Controller
{
    public function index(IndexRequest $request)
    {
        $action = new ActionFilter(request()->get('action', 'paginate:14'));
        $model = new Budget;

        $query = QueryBuilder::for(Budget::class)
            ->allowedFields($model->getAbstractAllowedFields())
            ->allowedFilters(array_merge($model->getAbstractAllowedFilters(), [
                Filter::scope('q')
            ]))
            ->allowedIncludes($model->getAbstractAllowedRelationships())
            ->allowedSorts($model->getAbstractAllowedSorts())
            ->where('user_id', auth()->id());

        return $this->json($action->execute($query));
    }

    public function store(StoreRequest $request)
    {
        /** @var AbstractEloquentModel $resource */
        $resource = new Budget;
        $resource->fill($request->validated() + [
            'user_id' => auth()->id(),
        ]);
        $resource->save();
        return $this->json($resource->refresh());
    }

    public function show(ViewRequest $request, Budget $model)
    {
        $result = QueryBuilder::for(Budget::class)
            ->allowedFields($model->getAbstractAllowedFields())
            ->allowedFilters($model->getAbstractAllowedFilters())
            ->allowedIncludes($model->getAbstractAllowedRelationships())
            ->allowedSorts($model->getAbstractAllowedSorts())
            ->where('user_id', auth()->id())
            ->find($model->id);

        if (empty($result)) {
            return $this->json([
                'message' => 'No resource found by that id.'
            ], 404);
        }

        return $this->json($result);
    }

    public function update(UpdateRequest $request, Budget $budget)
    {
        $budget->update($request->all());

        return $this->json($budget->refresh());
    }

    public function destroy(DestroyRequest $request, Budget $budget)
    {
        $budget->delete();

        return $this->json('', 204);
    }

    public function tags(Request $request, Budget $budget)
    {
        $budget->tags()->sync($request->json()->all());
        return $this->json($budget->refresh());
    }

    public function totalSpends(Request $request, Budget $budget)
    {
        return abs($budget->totalSpends($budget->getStartOfCurrentPeriod(), $budget->user_id)->find($budget->id)->total_spend);
    }
}
