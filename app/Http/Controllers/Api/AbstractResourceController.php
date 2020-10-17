<?php

namespace App\Http\Controllers\Api;

use App\FailedJob;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Tag;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Kregel\LaravelAbstract\AbstractEloquentModel;
use Kregel\LaravelAbstract\Filters\ActionFilter;
use Kregel\LaravelAbstract\Http\Requests\CreateRequest;
use Kregel\LaravelAbstract\Http\Requests\DeleteRequest;
use Kregel\LaravelAbstract\Http\Requests\ForceDeleteRequest;
use Kregel\LaravelAbstract\Http\Requests\IndexRequest;
use Kregel\LaravelAbstract\Http\Requests\RestoreRequest;
use Kregel\LaravelAbstract\Http\Requests\UpdateRequest;
use Kregel\LaravelAbstract\Http\Requests\ViewRequest;
use Spatie\QueryBuilder\AllowedFilter as Filter;
use Spatie\QueryBuilder\QueryBuilder;

class AbstractResourceController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * @throws Exception
     */
    public function index(IndexRequest $request, AbstractEloquentModel $model)
    {
        $action = new ActionFilter(request()->get('action', 'paginate:14'));

        $query = QueryBuilder::for(get_class($model))
            ->allowedFields($model->getAbstractAllowedFields())
            ->allowedFilters(array_merge($model->getAbstractAllowedFilters(), [
                Filter::scope('q')
            ]))
            ->allowedIncludes($model->getAbstractAllowedRelationships())
            ->allowedSorts($model->getAbstractAllowedSorts());

        if (!in_array(get_class($model), [FailedJob::class, Transaction::class])) {
            $query->where('user_id', auth()->id());
        }

        if (get_class($model) === Tag::class) {
            $query->withSum('transactions.amount');
        }

        return $this->json($action->execute($query));
    }

    public function store(CreateRequest $request, AbstractEloquentModel $model)
    {
        /** @var AbstractEloquentModel $resource */
        $resource = new $model;
        $resource->fill($request->validated());
        $resource->save();

        return $this->json($resource->refresh());
    }

    public function show(ViewRequest $request, AbstractEloquentModel $model, AbstractEloquentModel $abstractEloquentModel)
    {
        $result = QueryBuilder::for(get_class($model))
            ->allowedFields($model->getAbstractAllowedFields())
            ->allowedFilters($model->getAbstractAllowedFilters())
            ->allowedIncludes($model->getAbstractAllowedRelationships())
            ->allowedSorts($model->getAbstractAllowedSorts())
            ->find($abstractEloquentModel->id);

        if (empty($result)) {
            return $this->json([
                'message' => 'No resource found by that id.'
            ], 404);
        }

        return $this->json($result);
    }

    /**
     * @param  UpdateRequest  $request
     * @param  AbstractEloquentModel  $model
     * @param  AbstractEloquentModel|Model  $abstractEloquentModel
     * @return \Illuminate\Http\JsonResponse|object
     */
    public function update(UpdateRequest $request, AbstractEloquentModel $model, AbstractEloquentModel $abstractEloquentModel)
    {
        $abstractEloquentModel->update($request->validated());

        return $this->json($abstractEloquentModel->refresh());
    }

    public function destroy(DeleteRequest $request, AbstractEloquentModel $model, AbstractEloquentModel $abstractEloquentModel)
    {
        $abstractEloquentModel->delete();

        return $this->json('', 204);
    }

    public function forceDestroy(ForceDeleteRequest $request, AbstractEloquentModel $model, AbstractEloquentModel $abstractEloquentModel)
    {
        if (!$model->usesSoftdeletes()) {
            abort(404, 'You cannot force delete an item of this type.');

            return;
        }

        $abstractEloquentModel->forceDelete();

        return $this->json('', 204);
    }

    public function restore(RestoreRequest $request, AbstractEloquentModel $model, AbstractEloquentModel $abstractEloquentModel)
    {
        if (!$model->usesSoftdeletes()) {
            abort(404, 'You cannot restore an item of this type.');

            return;
        }

        $abstractEloquentModel->restore();

        return $this->json($abstractEloquentModel->refresh());
    }
}
