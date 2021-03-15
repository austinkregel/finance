<?php

namespace App\Jobs;

use App\FailedJob;
use Illuminate\Contracts\Queue\Factory;
use Illuminate\Contracts\Queue\Factory as Queue;

class RerunDeadJob
{
    /**
     * The job ID.
     *
     * @var string
     */
    public $id;

    /**
     * Create a new job instance.
     *
     * @param  string  $id;
     * @return void
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * Re-execute the job.
     *
     * @param  Factory  $queue
     * @return void
     */
    public function handle(Queue $queue): void
    {
        if (is_null($job = FailedJob::find($this->id))) {
            return;
        }

        $newJobDetails = array_merge(json_decode($job->payload, true), [
            'attempts' => 0,
            'retry_of' => $this->id,
        ]);

        $queue->connection($job->connection)
            ->pushRaw(json_encode($newJobDetails), $job->queue);
        // delete failed job
        $job->delete();
    }
}
