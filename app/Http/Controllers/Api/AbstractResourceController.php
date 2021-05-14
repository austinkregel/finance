<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AbstractResource;
use Exception;
use Illuminate\Http\Request;
use Kregel\LaravelAbstract\AbstractEloquentModel;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Kregel\LaravelAbstract\Filters\ActionFilter;
use Spatie\QueryBuilder\AllowedFilter as Filter;
use Spatie\QueryBuilder\QueryBuilder;

class AbstractResourceController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    public const RESOURCE = AbstractResource::class;

    public AbstractEloquentModel $model;

    public function __construct(AbstractEloquentModel $model)
    {
        $this->model = $model;
    }

    /**
     * @throws Exception
     */
    public function index()
    {
        $action = new ActionFilter(request()->get('action', 'paginate:14'));

        $query = QueryBuilder::for(get_class($this->model))
            ->allowedFields($this->model->getAbstractAllowedFields())
            ->allowedFilters(array_merge($this->model->getAbstractAllowedFilters(), [
                Filter::scope('q')
            ]))
            ->allowedIncludes($this->model->getAbstractAllowedRelationships())
            ->allowedSorts($this->model->getAbstractAllowedSorts());

        return $this->json($action->execute($query));
    }

    public function store(Request $request)
    {
        /** @var AbstractEloquentModel $resource */
        $resource = $this->model->newInstance();
        $resource->fill($request->validate($this->model->getValidationCreateRules()));
        $resource->save();

        return $this->json($resource->refresh());
    }

    public function show($id)
    {
        $abstract_model = $this->model::findOrFail($id);

        $query = QueryBuilder::for(get_class($this->model))
            ->allowedFields($abstract_model->getAbstractAllowedFields())
            ->allowedFilters($abstract_model->getAbstractAllowedFilters())
            ->allowedIncludes($abstract_model->getAbstractAllowedRelationships())
            ->allowedSorts($abstract_model->getAbstractAllowedSorts());

        return $this->json($query->find($abstract_model->id)) ?? $this->json([
            'message' => 'No resource found by that id.'
        ], 404);
    }

    public function update(Request $request, $id)
    {
        $abstract_model = $this->model::findOrFail($id);

        $abstract_model->update($request->validate($this->model->getValidationUpdateRules()));

        return $this->json($abstract_model->refresh());
    }

    public function destroy($request, $id)
    {
        $abstract_model = $this->model::findOrFail($id);

        $abstract_model->delete();

        return $this->json('', 204);
    }

    public function forceDestroy($request, $id)
    {
        $abstract_model = $this->model::findOrFail($id);

        if (!$abstract_model->usesSoftdeletes()) {
            abort(404, 'You cannot force delete an item of this type.');

            return;
        }

        $abstract_model->forceDelete();

        return $this->json('', 204);
    }

    public function restore($request, $id)
    {
        $abstract_model = $this->model::findOrFail($id);

        if (!$abstract_model->usesSoftdeletes()) {
            abort(404, 'You cannot restore an item of this type.');

            return;
        }

        $abstract_model->restore();

        return $this->json($abstract_model->refresh());
    }
}
