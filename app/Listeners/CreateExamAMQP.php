<?php

namespace App\Listeners;

use App\Events\ExamCreated;
use App\AMQP\Publisher;

class CreateExamAMQP
{
    /**
     * @TODO
     */
    public function handle(ExamCreated $event)
    {
        $publisher = new Publisher();
        $publisher->publish($event->exam->toJson(), 'exam.' . $event->exam->client_id . '.created');
    }
}
