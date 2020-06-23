<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Kregel\ExceptionProbe\Stacktrace;
use Kregel\LaravelAbstract\AbstractEloquentModel;
use Kregel\LaravelAbstract\AbstractModelTrait;

/**
 * Class FailedJob.
 *
 * @package namespace App\Models;
 * @mixin \Eloquent
 */
class FailedJob extends Model implements AbstractEloquentModel
{
    use AbstractModelTrait;

    public $appends = [
        'codestack',
        'message',
        'args',
    ];

    public function __construct(array $attributes = [])
    {
        $this->table = config('queue.failed.table');
        parent::__construct($attributes);
    }

    public function getArgsAttribute()
    {
        $payload = json_decode($this->payload);

        if (empty($payload)) {
            return new \stdClass();
        }

        if (empty($payload->data)) {
            return new \stdClass();
        }

        if (empty($payload->data->command)) {
            return new \stdClass();
        }

        return unserialize(json_decode($this->payload)->data->command);
    }

    public function getCodestackAttribute()
    {
        return array_map(function ($codestack) {
            $codestack->file = str_replace(base_path(), '.', $codestack->file);
            return $codestack;
        }, (new Stacktrace)->parse($this->exception));
    }

    public function getMessageAttribute()
    {
        $stack = (new Stacktrace);

        $stack->parse($this->exception);

        return $stack->message;
    }

    public function getValidationCreateRules(): array
    {
        // TODO: Implement getValidationCreateRules() method.
    }

    public function getValidationUpdateRules(): array
    {
        // TODO: Implement getValidationUpdateRules() method.
    }

    public function getAbstractAllowedFilters(): array
    {
        return [
            'connection',
            'queue',
            'payload',
            'exception',
            'failed_at',
        ];
    }

    public function getAbstractAllowedRelationships(): array
    {
        return [
            'connection',
            'queue',
            'payload',
            'exception',
            'failed_at',
        ];
    }

    public function getAbstractAllowedSorts(): array
    {
        return ['failed_at'];
    }

    public function getAbstractAllowedFields(): array
    {
        return [
            'connection',
            'queue',
            'payload',
            'exception',
            'failed_at',
        ];
    }

    public function getAbstractSearchableFields(): array
    {
        return [
            'connection',
            'queue',
            'payload',
            'exception',
            'failed_at',
        ];
    }
}
