<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReScheduleMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $schedule;
    public $name;
    public function __construct($schedule, $name)
    {
        $this->schedule = $schedule;
        $this->name = $name;
    }
    public function build()
    {
        return $this->subject('Reschedule Notice From Happy Smile')
            ->view('emails.re-schedule')
            ->with([
                'schedule' => $this->schedule,
                'user' => $this->name
            ]);
    }
}