<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    public function sendEmail(Request $request)
    {
        $toEmail = $request->input('to_email');
        $subject = $request->input('subject');
        $message = $request->input('message');

        try {
            Mail::raw($message, function ($mail) use ($toEmail, $subject) {
                $mail->to($toEmail)->subject($subject);
            });

            return response()->json(['message' => 'Email sent successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to send email', 'error' => $e->getMessage()], 500);
        }
    }
}
