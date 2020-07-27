<?php

namespace App\Http\Controllers\Api;

use App\Condition;
use App\Http\Controllers\Controller;
use App\Http\Requests\Alert\ConditionalsRequest;
use App\Http\Requests\Alert\ConditionalUpdateRequest;
use App\Http\Requests\Alert\DestroyRequest;
use App\Http\Requests\Alert\IndexRequest;
use App\Http\Requests\Alert\StoreRequest;
use App\Http\Requests\Alert\UpdateRequest;
use App\Http\Requests\Alert\ViewRequest;
use App\Models\Alert;
use Illuminate\Http\Request;
use Kregel\LaravelAbstract\AbstractEloquentModel;
use Kregel\LaravelAbstract\Filters\ActionFilter;
use Spatie\QueryBuilder\AllowedFilter as Filter;
use Spatie\QueryBuilder\QueryBuilder;

class AlertController extends Controller
{
    public function index(IndexRequest $request)
    {
        $action = new ActionFilter(request()->get('action', 'paginate:14'));
        $model = new Alert;

        $query = QueryBuilder::for(Alert::class)
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
        $resource = new Alert;
        $resource->fill($request->validated() + [
            'user_id' => auth()->id(),
        ]);
        $resource->save();
        return $this->json($resource->refresh());
    }

    public function show(ViewRequest $request, Alert $model)
    {
        $result = QueryBuilder::for(Alert::class)
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

    public function update(UpdateRequest $request, Alert $alert)
    {
        $alert->update($request->all());

        return $this->json($alert->refresh());
    }

    public function destroy(DestroyRequest $request, Alert $alert)
    {
        $alert->delete();

        return $this->json('', 204);
    }

    public function conditionals(ConditionalsRequest $request, Alert $alert)
    {
        return $this->json(
            $alert->conditionals()->create($request->json()->all())
        );
    }

    public function updateConditional(ConditionalUpdateRequest $request, Alert $alert, Condition $condition)
    {
        $condition->update($request->validated());

        return $condition;
    }
    public function deleteConditional(ConditionalUpdateRequest $request, Alert $alert, Condition $condition)
    {
        $condition->delete();

        return response('', 204);
    }
}
