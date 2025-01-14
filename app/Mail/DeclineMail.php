<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DeclineMail extends Mailable
{
    use Queueable, SerializesModels;

    public $schedule;
    public $user;
    public $remarks;  // Changed from $message to $remarks

    public function __construct($schedule, $user, $remarks)
    {
        $this->schedule = $schedule;
        $this->user = $user;
        $this->remarks = $remarks;  // Updated here
    }

    public function build()
    {
        return $this->subject('Schedule Decline Notification')
            ->view('emails.decline-schedule')
            ->with([
                'schedule' => $this->schedule,
                'user' => $this->user,
                'remarks' => $this->remarks,  // Updated here
            ]);
    }
}
