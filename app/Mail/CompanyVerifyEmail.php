<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Company;

class CompanyVerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $company;
    public $token;

    public function __construct(Company $company, string $token)
    {
        $this->company = $company;
        $this->token = $token;
    }

    public function build()
    {
        $verifyUrl = route('company.verify', $this->token);

        return $this->subject('Verify Your Company Registration')
                    ->view('emails.company_verify')
                    ->with([
                        'companyName' => $this->company->company_name,
                        'verifyUrl' => $verifyUrl,
                        'firstName' => $this->company->creator->firstname ?? 'User',
                    ]);
    }
}
