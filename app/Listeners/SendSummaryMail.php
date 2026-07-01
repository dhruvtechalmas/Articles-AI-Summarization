<?php

namespace App\Listeners;

use App\Events\SummaryCompleted;
use App\Mail\SummaryCompletedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class SendSummaryMail
{


    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(SummaryCompleted $event): void
    {
        // Mail::to('dhruvtechalmas@gmail.com')
        //     ->send(new SummaryCompletedMail($event->article));
    }

}
