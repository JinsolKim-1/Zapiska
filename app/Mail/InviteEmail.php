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
        $company = $this->invitation->company;
        return $this->subject('You have been invited to join Zapiska!')
                    ->view('emails.invitation')
                    ->with([
                        'invitee_email' => $this->invitation->invitee_email,
                        'invite_link' => $this->inviteLink,
                        'company_name' => $company ? $company->company_name : 'a company',
                        'company_email' => $company ? $company->company_email : 'support@zapiska.com',
                    ]);
    }

}
