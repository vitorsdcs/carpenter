<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Console\Commands\RabbitMQListenCommand;

class CommandServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('command.rabbitmq.listen', function() {
            return new RabbitMQListenCommand;
        });

        $this->commands(
            'command.rabbitmq.listen'
        );
    }
}
