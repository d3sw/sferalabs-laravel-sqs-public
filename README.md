# Sqs connector

A custom SQS connector for Laravel that supports custom format JSON payloads. Out of the box, Laravel expects
SQS messages to be generated in specific format - format that includes job handler class and a serialized job.

But in certain cases you may want to parse messages from third party applications, custom JSON messages and so on.

## Dependencies

* PHP >= 7.0
* Laravel >= 8.0

## Installation via Composer

To install simply run:

```
composer require d3sw/sferalabs-laravel-sqs
```

Or add it to `composer.json` manually:

### Usage

```php
// Add in your config/app.php

'providers' => [
    '...',
    'Deluxe\SqsRaw\SqsRawServiceProvider',
];
```

## Configuration

```php
// Generate standard config file
php artisan vendor:publish

// In Lumen, create it manually (see example below) and load it in bootstrap/app.php
$app->configure('sqs-raw');
```

Edit config/sqs-raw.php to suit your needs. This config matches SQS queues with handler classes.

```php
return [
    'handlers' => [
        'queue-name' => App\Jobs\HandlerJob::class,
    ],

    'default-handler' => App\Jobs\HandlerJob::class
];
```

If queue is not found in 'handlers' array, SQS payload is passed to default handler.

Add sqs-raw connection to your config/queue.php, eg:
```php
        ...
        'sqs-raw' => [
            'driver' => 'sqs-raw',
            'key'    => env('AWS_KEY', ''),
            'secret' => env('AWS_SECRET', ''),
            'prefix' => 'https://sqs.us-west-2.amazonaws.com/123456',
            'queue'  => 'queue-name',
            'region' => 'us-west-2',
        ],
        ...
```

In your .env file, choose sqs-raw as your new default queue driver:
```
QUEUE_DRIVER=sqs-raw
```

## Dispatching to SQS

If you plan to push plain messages from Laravel, you can rely on DispatcherJob:

```php
use Deluxe\SqsRaw\Jobs\DispatcherJob;

class ExampleController extends Controller
{
    public function index()
    {
        // Create a PHP object
        $object = [
            'hello' => 'World',
            'time' => time()
        ];

        // Pass it to dispatcher job
        $job = new DispatcherJob($object);

        // Dispatch the job as you normally would
        // By default, your data will be encapsulated in 'data' and 'job' field will be added
        $this->dispatch($job);

        // If you wish to submit a true plain JSON, add setPlain()
        $this->dispatch($job->setPlain());
    }
}

```

This will push the following JSON object to SQS:

```
{"job":"App\\Jobs\\HandlerJob@handle","data":{"hello":"World","time":1462511642}}
```

`job` field is not used, actually. It's just kept for compatibility’s sake.
