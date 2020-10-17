<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class AbstractResourceCollection extends ResourceCollection
{
    /**
     * @var AbstractResource
     */
    public $resourceResponse;

    /**
     * @var null
     */
    public static $wrap = null;

    /**
     * @param mixed $resource
     */
    public function __construct($resource, string $abstractResource)
    {
        parent::__construct($resource);
        $this->resourceResponse = $abstractResource;
    }

    /**
     * @param mixed $resource
     */
    public static function collectionWithResource($resource, string $abstractResource): self
    {
        return new static($resource, $abstractResource);
    }
}
