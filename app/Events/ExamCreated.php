<?php

namespace App\Events;

use App\Exam;
use Illuminate\Queue\SerializesModels;

class ExamCreated extends Event
{
    public $exam;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Exam $exam)
    {
        $this->exam = $exam;
    }
}
