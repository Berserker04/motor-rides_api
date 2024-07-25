<?php

namespace App\Http\Controllers\Api\V1\shared;

use App\Http\Controllers\Controller;
use App\Mail\InfoClientMail;
use App\Mail\NotifyMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SendEmailsController extends Controller
{
    public static function sendEmail($emailTo, $request)
    {
        $emailData = [
            "fullName" => $request->fullName,
            "email" => $request->email,
            "subject" => $request->subject ? $request->subject : "Informacion sobre Aguas de Nuquí",
            "cellPhone" => $request->cellPhone,
            "message" => $request->message,
        ];

        $mail = new InfoClientMail($emailData);
        $mail->subject($emailData['subject']);

        Mail::to($emailTo)->send($mail);
    }
}
