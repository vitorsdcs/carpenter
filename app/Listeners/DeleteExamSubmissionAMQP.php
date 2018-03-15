<?php

namespace App\Listeners;

use App\Events\ExamSubmissionDeleted;
use App\AMQP\Publisher;

class DeleteExamSubmissionAMQP
{
    /**
     * @TODO
     */
    public function handle(ExamSubmissionDeleted $event)
    {
        $publisher = new Publisher();
        $publisher->publish($event->submission->toJson(), 'examsubmission.' . $event->submission->exam->client_id . '.deleted');
    }
}
