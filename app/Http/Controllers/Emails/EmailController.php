<?php

namespace App\Http\Controllers\Emails;

use App\Http\Controllers\Controller;
use App\Services\EmailService;
use Illuminate\Http\Request;

class EmailController extends Controller
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function sendHtmlEmail(Request $request)
    {
        $recipientEmail = $request->input('recipient_email');
        $subject = $request->input('subject');
        $body = $request->input('body');

        $response = $this->emailService->sendHtmlEmail($recipientEmail, $subject, $body);
    }
}
