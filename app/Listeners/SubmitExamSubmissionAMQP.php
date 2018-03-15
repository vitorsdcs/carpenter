<?php

namespace App\Listeners;

use App\Events\ExamSubmitted;
use App\AMQP\Publisher;

class SubmitExamSubmissionAMQP
{
    /**
     * @TODO
     */
    public function handle(ExamSubmitted $event)
    {
        $publisher = new Publisher();
        $publisher->publish($event->submission->toJson(), 'exam.' . $event->submission->exam->client_id . '.submitted');
    }
}
