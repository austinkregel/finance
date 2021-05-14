<?php
declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;

class AbstractResource extends JsonResource
{
    /**
     * @var null
     */
    public static $wrap = null;

    /**
     * @param \Illuminate\Http\Request|mixed $request
     */
    public function toArray($request): array
    {
        if (is_null($this->resource)) {
            return [];
        }

        if (is_array($this->resource)) {
            return $this->resource;
        }

        if ($this->resource instanceof Arrayable) {
            return $this->resource->toArray();
        }

        return (array) $this->resource;
    }
}
