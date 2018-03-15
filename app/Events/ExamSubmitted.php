<?php

namespace App\Events;

use App\ExamSubmission;
use Illuminate\Queue\SerializesModels;

class ExamSubmitted extends Event
{
    public $submission;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(ExamSubmission $submission)
    {
        $this->submission = $submission;
    }
}
