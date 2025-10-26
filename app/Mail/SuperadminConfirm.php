<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SuperadminConfirm extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $editor;
    public $url;

    public function __construct($user, $editor, $url)
    {
        $this->user = $user;
        $this->editor = $editor;
        $this->url = $url;
    }

    public function build()
    {
        return $this->subject('Confirm Account Edit Request')
                    ->view('emails.confirmation');
    }
}
