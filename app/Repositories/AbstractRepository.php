<?php
/**
 * AbstractRepository
 */
declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\AbstractRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * Class AbstractRepository
 * @package App\Repositories
 */
abstract class AbstractRepository  implements AbstractRepositoryInterface
{
    /**
     * @return string|Model
     */
    abstract public function model(): string;

    /**
     * Find one item.
     * @param array $where
     * @param array $with
     * @return Model
     */
    public function findOne(array $where = [], array $with = []): ?Model
    {
        $query = $this->model()::with($with);

        return $this->figureOutTheWheres($query, $where)->first();
    }

    /**
     * Find one item.
     * @param array $where
     * @param array $with
     * @return Model
     */
    public function findOneWithAll(array $where = []): Model
    {
        $query = $this->model()::with($this->model->getAllRelationships());

        return $this->figureOutTheWheres($query, $where)->firstOrFail();
    }

    /**
     * Find all items.
     * @param array $where
     * @param array $with
     * @param int $limit
     * @param int $page
     * @return LengthAwarePaginator
     */
    public function findAll(array $where = [], array $with = [], int $limit = 15, int $page = 1): LengthAwarePaginator
    {
        /** @var Builder $query */
        $query = $this->model()::with($with);

        return $this->figureOutTheWheres($query, $where)->paginate($limit, ['*'], 'page', $page);
    }

    /**
     * Find all items.
     * @param array $where
     * @param array $with
     * @param int $limit
     * @param int $page
     * @return LengthAwarePaginator
     */
    public function findAllOrderByDesc(string $orderBy, array $where = [], array $with = [], int $limit = 15, int $page = 1): LengthAwarePaginator
    {
        /** @var Builder $query */
        $query = $this->model()::with($with);

        return $this->figureOutTheWheres($query, $where)
            ->orderBy($orderBy, 'desc')
            ->paginate($limit, ['*'], 'page', $page);
    }

    /**
     * Find all items.
     * @param array $where
     * @param array $with
     * @return Collection
     */
    public function findAllWithoutPagination(array $where = [], array $with = []): Collection
    {
        $query = $this->model()::with($with);
        return $this->figureOutTheWheres($query, $where)->get();
    }

    /**
     * Checks to see if anything exists given some conditionals.
     * @param array $where
     * @return boolean
     */
    public function existsBy(array $where = []): bool
    {
        /** @var Builder $query */
        $query = $this->figureOutTheWheres($this->model()::query(), $where);

        return $query->exists();
    }

    /**
     * @param array $_
     * @param Model $relation
     * @return Model
     */
    public function create(array $_)
    {
        $args = func_get_args();

        if (count($args) === 1) {
            return $this->model()::create($args[0]);
        }

        [$attributes, $relation] = $args;

        /** @var Model $createdModel */
        $createdModel = $this->model->newInstance($attributes);

        $relationships = [];

        foreach ((new \ReflectionClass($createdModel))->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->class != get_class($createdModel) || !empty($method->getParameters()) || $method->getName() == __FUNCTION__) continue;
            try {
                $return = $method->invoke($createdModel);
                if ($return instanceof Relation) $relationships[] = $method->getName();
            } catch (\ErrorException $e) {}
        }

        foreach ($relationships as $relationshipName) {
            /** @var Relation $relationship */
            $relationship = $createdModel->$relationshipName();
            // We only want the relationship that matches the given relation.
            if ($relationship->getModel() instanceof $relation) {
                if ($relationship instanceof BelongsTo) {
                    $relationKey = $relationship->getForeignKey();
                    $relationIdKey = $relationship->getOwnerKey();
                    $createdModel->$relationKey = $relation->$relationIdKey;
                } elseif ($relationship instanceof BelongsToMany) {
                    if (!$createdModel->wasRecentlyCreated){
                        $createdModel = static::create($createdModel->toArray());
                    } else {
                        $createdModel->save();
                    }

                    $createdModel->$relationshipName()->attach($relation->{$createdModel->getKeyName()});
                } else {
                    throw new \DomainException(sprintf('Sorry, dont know how to handle %s type relations yet...', class_basename(get_class($relationship))));
                }
            }
        }

        if (!$createdModel->wasRecentlyCreated){
            return static::create($createdModel->toArray());
        }

        return $createdModel;
    }

    /**
     * @param Builder $query
     * @param array $where
     * @return Builder
     */
    protected function figureOutTheWheres(Builder $query, array $where = [])
    {
        foreach ($where as [$field]) {
            if ($field === 'deleted_at') {
                $query->withTrashed();
                break;
            }
        }

        $whereIns = array_filter($where, function (array $whereDeclaration) {
            return $whereDeclaration[1] == 'in';
        });
        foreach ($whereIns as $whereIn) {
            $query->whereIn($whereIn[0], $whereIn[2]);
        }

        $whereNotIns = array_filter($where, function (array $whereDeclaration) {
            return $whereDeclaration[1] == 'not in';
        });
        foreach ($whereNotIns as $whereNotIn) {
            $query->whereNotIn($whereNotIn[0], $whereNotIn[2]);
        }

        $wheres = array_filter($where, function (array $whereDeclaration) {
            return $whereDeclaration[1] != 'in' && $whereDeclaration[1] != 'not in';
        });
        $query->where($wheres);

        return $query;
    }
}
