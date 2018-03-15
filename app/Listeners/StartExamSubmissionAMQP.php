<?php

namespace App\Listeners;

use App\Events\ExamStarted;
use App\AMQP\Publisher;

class StartExamSubmissionAMQP
{
    /**
     * @TODO
     */
    public function handle(ExamStarted $event)
    {
        $publisher = new Publisher();
        $publisher->publish($event->submission->toJson(), 'exam.' . $event->submission->exam->client_id . '.started');
    }
}
