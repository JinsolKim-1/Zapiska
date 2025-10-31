<?php

namespace App\Mail;

use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InviteEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $invitation;
    public $inviteLink;

    public function __construct(Invitation $invitation)
    {
        $this->invitation = $invitation;
        $this->inviteLink = url('/users/invite/' . $invitation->invite_token);
    }

    public function build()
    {
        return $this->subject('You have been invited to join Zapiska!')
                    ->view('emails.invitation')
                    ->with([
                        'inviter_email' => $this->invitation->inviter_email,
                        'invite_link' => $this->inviteLink,
                    ]);
    }

}
