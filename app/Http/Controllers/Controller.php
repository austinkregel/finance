<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\AbstractResource;
use App\Http\Resources\AbstractResourceCollection;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public const RESOURCE = AbstractResource::class;

    protected function buildResource($value): JsonResource
    {
        $resourceResponse = static::RESOURCE;

        // Due to how resources work we have to handle paginators differently than collections
        if ($value instanceof LengthAwarePaginator) {
            return AbstractResourceCollection::collectionWithResource($value, $resourceResponse);
        } elseif ($value instanceof Collection) {
            return AbstractResourceCollection::collectionWithResource($value, $resourceResponse);
        }

        return new $resourceResponse($value);
    }

    public function json($content, $status = 200)
    {
        $resource = $this->buildResource($content);

        return $resource->toResponse(request())->setStatusCode($status);
    }
}
