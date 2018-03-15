<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\AMQP\Receiver;

class RabbitMQListenCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'rabbitmq:listen';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run RabbitMQ consumer.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $receiver = new Receiver();
        $receiver->listen();
    }

}