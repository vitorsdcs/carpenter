<?php

namespace App\AMQP;

use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Publisher
{
    public function publish($message, $routingKey)
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

        $message = new AMQPMessage($message);

        $channel->basic_publish(
            $message,
            env('RABBITMQ_EXCHANGE_NAME', null),
            $routingKey
        );

        $channel->close();
        $connection->close();
    }
}
