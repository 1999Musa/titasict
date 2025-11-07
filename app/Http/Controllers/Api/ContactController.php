<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function sendMail(Request $request)
    {
        // Validation
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255', 
            'phone'   => 'required|string|max:20',
            'message' => 'required|string|max:2000', // mandatory
        ]);

        try {
            $data = [
                'name'    => $request->name,
                'email'   => $request->email,
                'phone'   => $request->phone,
                'user_message' => $request->message, // renamed to avoid conflict
            ];

            // Send mail
            Mail::send('emails.contact', $data, function ($mail) use ($data) {
                $mail->to('info@arbellafashion.com')
                     ->subject('New Contact Message from Website')
                     ->from('info@arbellafashion.com', $data['name']);
            });

            return response()->json(['success' => true, 'message' => 'Email sent successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
