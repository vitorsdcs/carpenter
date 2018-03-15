<?php

namespace App\AMQP;

use PhpAmqpLib\Connection\AMQPConnection;

class Receiver
{
    protected $bindingKeys = [
        'user.*.created' => '\App\AMQP\UserReceiver',
        'user.*.updated' => '\App\AMQP\UserReceiver',
        'user.*.deleted' => '\App\AMQP\UserReceiver',
    ];

    public function listen()
    {
        $connection = new AMQPConnection(
            env('RABBITMQ_HOST', '127.0.0.1'),
            env('RABBITMQ_PORT', 5672),
            env('RABBITMQ_LOGIN', 'guest'),
            env('RABBITMQ_PASSWORD', 'guest'),
            env('RABBITMQ_VHOST', '/')
        );

        $channel = $connection->channel();

        $channel->exchange_declare(
            env('RABBITMQ_EXCHANGE_NAME', null),
            env('RABBITMQ_EXCHANGE_TYPE', 'direct'),
            env('RABBITMQ_EXCHANGE_PASSIVE', false),
            env('RABBITMQ_EXCHANGE_DURABLE', true),
            env('RABBITMQ_EXCHANGE_AUTODELETE', false)
        );

        $channel->queue_declare(
            env('RABBITMQ_QUEUE', null),
            env('RABBITMQ_QUEUE_PASSIVE', false),
            env('RABBITMQ_QUEUE_DURABLE', true),
            env('RABBITMQ_QUEUE_EXCLUSIVE', false),
            env('RABBITMQ_QUEUE_AUTODELETE', false)
        );

        foreach ($this->bindingKeys as $bindingKey => $class) {
            $channel->queue_bind(
                env('RABBITMQ_QUEUE', null),
                env('RABBITMQ_EXCHANGE_NAME', null),
                $bindingKey
            );
        }

        $channel->basic_consume(
            env('RABBITMQ_QUEUE', null),
            '',
            false,
            true,
            false,
            false,
            [$this, 'handle']
        );

        while (count($channel->callbacks)) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }

    public function handle($message)
    {
        foreach ($this->bindingKeys as $bindingKey => $class) {
            if (fnmatch($bindingKey, $message->delivery_info['routing_key'])) {
                $instance = new $class();
                $instance->handle($message);
            }
        }

        return false;
    }
}
