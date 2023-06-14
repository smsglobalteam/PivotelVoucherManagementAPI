<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicationSubmitted extends Mailable
{
    use Queueable, SerializesModels;
    /**
     * Create a new message instance.
     */

     public $emailGrab;
     public $nameGrab;


    public function __construct($getEmail, $getName)
    {
        //
        $this->emailGrab = $getEmail;
        $this->nameGrab = $getName;
    }

    public function build()
    {
        return $this->subject('Application Submitted')
        ->markdown('emails.application-submitted-mail', ['email'=>$this->emailGrab, 'name'=>$this->nameGrab]);
    }
}
