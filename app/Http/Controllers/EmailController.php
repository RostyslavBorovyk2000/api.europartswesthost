<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    public function sendEmail(Request $request)
    {
        // Лише на пошту компанії: адресу з запиту не беремо, щоб API не можна було
        // використати для розсилки спаму на чужі адреси.
        $toEmail = 'europartswest1@gmail.com';
        $subject = mb_substr(strip_tags((string) $request->input('subject', 'Заявка з сайту')), 0, 200);
        $message = mb_substr(strip_tags((string) $request->input('message', '')), 0, 5000);

        if (trim($message) === '') {
            return response()->json(['message' => 'Empty message'], 422);
        }

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
