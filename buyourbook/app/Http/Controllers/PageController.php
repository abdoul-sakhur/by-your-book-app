<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class PageController extends Controller
{
    public function howItWorks(): View
    {
        return view('pages.how-it-works');
    }

    public function contact(): View
    {
        return view('pages.contact');
    }

    public function contactSend(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        // Envoi d'un mail à l'admin via notification simple
        Mail::raw(
            "Nom : {$validated['name']}\nEmail : {$validated['email']}\nSujet : {$validated['subject']}\n\nMessage :\n{$validated['message']}",
            function ($mail) use ($validated) {
                $mail->to(config('mail.from.address', 'admin@buyyourbook.ci'))
                     ->subject('Contact BuyYourBook : ' . $validated['subject'])
                     ->replyTo($validated['email'], $validated['name']);
            }
        );

        return redirect()->route('pages.contact')->with('success', 'Votre message a été envoyé avec succès. Nous vous répondrons rapidement.');
    }

    public function terms(): View
    {
        return view('pages.terms');
    }

    public function privacy(): View
    {
        return view('pages.privacy');
    }

    public function legal(): View
    {
        return view('pages.legal');
    }
}
