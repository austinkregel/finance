<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tag\ConditionalsRequest;
use App\Http\Requests\Tag\DestroyRequest;
use App\Tag;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Kregel\LaravelAbstract\AbstractEloquentModel;
use Kregel\LaravelAbstract\Filters\ActionFilter;
use App\Http\Requests\Tag\StoreRequest;
use App\Http\Requests\Tag\IndexRequest;
use App\Http\Requests\Tag\UpdateRequest;
use App\Http\Requests\Tag\ViewRequest;
use Spatie\QueryBuilder\AllowedFilter as Filter;
use Spatie\QueryBuilder\QueryBuilder;

class TagController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * @throws Exception
     */
    public function index(IndexRequest $request)
    {
        $action = new ActionFilter(request()->get('action', 'paginate:14'));
        $model = new Tag;

        $query = QueryBuilder::for(Tag::class)
            ->allowedFields($model->getAbstractAllowedFields())
            ->allowedFilters(array_merge($model->getAbstractAllowedFilters(), [
                Filter::scope('q')
            ]))
            ->allowedIncludes($model->getAbstractAllowedRelationships())
            ->allowedSorts($model->getAbstractAllowedSorts());

        return $this->json($action->execute($query));
    }

    public function store(StoreRequest $request)
    {
        /** @var AbstractEloquentModel $resource */
        $resource = new Tag;
        $resource->fill($request->validated() + ['user_id' => auth()->id()]);
        $resource->save();
        return $this->json($resource->refresh());
    }

    public function show(ViewRequest $request, Tag $model)
    {
        $result = QueryBuilder::for(Tag::class)
            ->allowedFields($model->getAbstractAllowedFields())
            ->allowedFilters($model->getAbstractAllowedFilters())
            ->allowedIncludes($model->getAbstractAllowedRelationships())
            ->allowedSorts($model->getAbstractAllowedSorts())
            ->find($model->id);

        if (empty($result)) {
            return $this->json([
                'message' => 'No resource found by that id.'
            ], 404);
        }

        return $this->json($result);
    }

    public function update(UpdateRequest $request, Tag $Tag)
    {
        $Tag->update($request->all());

        return $this->json($Tag->refresh());
    }

    public function destroy(DestroyRequest $request, Tag $Tag)
    {
        $Tag->delete();

        return $this->json('', 204);
    }

    public function conditionals(ConditionalsRequest $request, Tag $tag)
    {
        return $this->json(
            $tag->conditionals()->create($request->json()->all() + ['type' => 'automatic'])
        );
    }
}
