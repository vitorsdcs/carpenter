<?php

namespace App\Listeners;

use App\Events\ExamUpdated;
use App\AMQP\Publisher;

class UpdateExamAMQP
{
    /**
     * @TODO
     */
    public function handle(ExamUpdated $event)
    {
        $publisher = new Publisher();
        $publisher->publish($event->exam->toJson(), 'exam.' . $event->exam->client_id . '.updated');
    }
}
