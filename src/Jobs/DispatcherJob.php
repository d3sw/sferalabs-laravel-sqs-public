<?php

namespace Deluxe\SqsRaw\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class DispatcherJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var mixed
     */
    protected mixed $data;

    /**
     * DispatchedJob constructor.
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getPayload()
    {
        return [
            'job' => app('config')->get('sqs-raw.default-handler'),
            'data' => $this->data
        ];
    }
}