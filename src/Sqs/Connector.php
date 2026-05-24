<?php

namespace Deluxe\SqsRaw\Sqs;

use Aws\Sqs\SqsClient;
use Illuminate\Support\Arr;
use Illuminate\Queue\Connectors\SqsConnector;

class Connector extends SqsConnector
{
    /**
     * Establish a queue connection.
     *
     * @param array $config
     * @return Queue
     */
    public function connect(array $config): Queue
    {
        $config = $this->getDefaultConfiguration($config);

        if (!empty($config['key']) && !empty($config['secret'])) {
            $config['credentials'] = Arr::only($config, ['key', 'secret']);
        }

        return new Queue(
            new SqsClient($config),
            $config['queue'],
            Arr::get($config, 'prefix', ''),
            Arr::get($config, 'suffix', '')
        );
    }
}