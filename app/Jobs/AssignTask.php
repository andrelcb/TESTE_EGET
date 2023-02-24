<?php

namespace App\Jobs;

use App\Mail\AssignTask as MailAssignTask;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class AssignTask implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $task;
    protected $user;
    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct($task, $user)
    {
        $this->task = $task;
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->user->email)->send(new MailAssignTask($this->task, $this->user));
    }
}
