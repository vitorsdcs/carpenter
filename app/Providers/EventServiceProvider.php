<?php

namespace App\Providers;

use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\ExamCreated' => [
            'App\Listeners\CreateExamAMQP',
        ],
        'App\Events\ExamUpdated' => [
            'App\Listeners\UpdateExamAMQP',
        ],
        'App\Events\ExamStarted' => [
            'App\Listeners\StartExamSubmissionAMQP',
        ],
        'App\Events\ExamSubmitted' => [
            'App\Listeners\SubmitExamSubmissionAMQP',
        ],
        'App\Events\ExamSubmissionDeleted' => [
            'App\Listeners\DeleteExamSubmissionAMQP',
        ],
    ];
}
