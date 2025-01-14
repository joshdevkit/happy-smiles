<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FollowUpMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $user;
    public $followup;
    public function __construct($user, $followup)
    {
        $this->user = $user;
        $this->followup = $followup;
    }

    public function build()
    {
        return $this->subject('Follow Up Notice From Happy Smile')
            ->view('emails.followup')
            ->with([
                'followup' => $this->followup,
                'user' => $this->user
            ]);
    }
}
