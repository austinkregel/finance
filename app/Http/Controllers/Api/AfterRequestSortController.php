<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Kregel\LaravelAbstract\AbstractEloquentModel;
use Kregel\LaravelAbstract\Http\Controllers\AbstractResourceController;
use Kregel\LaravelAbstract\Http\Requests\IndexRequest;

class AfterRequestSortController extends AbstractResourceController
{
    public function index(IndexRequest $request, AbstractEloquentModel $model)
    {
        $paginator = parent::index($request, $model);

        $sort = $request->get('sortable');

        if (empty($sort)) {
            return $paginator;
        }

        if ($paginator instanceof LengthAwarePaginator) {
            if (! $request->get('sortable')) {
                return $paginator->appends($request->query());
            }

            $items = Collection::make($paginator->items());

            return (new \Illuminate\Pagination\LengthAwarePaginator(
                $items->sortBy($sort)->values(),
                $paginator->total(),
                $paginator->perPage(),
                $paginator->currentPage()
            ))->appends($request->query());
        } elseif ($paginator instanceof Collection) {
            if (! $request->get('sortable')) {
                return $paginator;
            }

            return $paginator->sort(function ($a, $b) {
                $dateCarbona = (int) Carbon::parse($a->next_due_date ?? 0)->format('d');
                $dateCarbonb = (int) Carbon::parse($b->next_due_date ?? 0)->format('d');

                return $dateCarbona <=> $dateCarbonb;
            })->values();
        }

        asort($paginator);

        return $paginator;
    }
}
