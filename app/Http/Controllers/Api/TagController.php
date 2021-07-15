<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Condition;
use App\Http\Requests\Tag\ConditionalsRequest;
use App\Http\Requests\Tag\ConditionalUpdateRequest;
use App\Jobs\SyncTagsWithTransactionsInDatabase;
use App\Tag;

class TagController extends AbstractResourceController
{
    public function __construct(Tag $model)
    {
        parent::__construct($model);
    }

    public function conditionals(ConditionalsRequest $request, Tag $tag)
    {
        try {
            $tag->transactions()->sync([]);

            return $this->json(
                $tag->conditionals()->create($request->json()->all())
            );
        } finally {
            $this->dispatch(new SyncTagsWithTransactionsInDatabase($request->user()));
        }
    }

    public function updateConditional(ConditionalUpdateRequest $request, Tag $tag, Condition $condition)
    {
        try {
            $tag->transactions()->sync([]);
            $condition->update($request->validated());

            return $condition;
        } finally {
            $this->dispatch(new SyncTagsWithTransactionsInDatabase($request->user()));
        }
    }

    public function deleteConditional(ConditionalUpdateRequest $request, Tag $tag, Condition $condition)
    {
        try {
            $tag->transactions()->sync([]);
            $condition->delete();

            return response('', 204);
        } finally {
            $this->dispatch(new SyncTagsWithTransactionsInDatabase($request->user()));
        }
    }
}
