<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Mail\NewsletterAdminMail;
use App\Mail\NewsletterUserMail;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $email = strtolower(trim($request->email));

        $alreadyExists = NewsletterSubscriber::where('email', $email)->exists();

        if ($alreadyExists) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'info',
                    'message' => 'You are already subscribed to our newsletter!',
                ]);
            }

            return back()->with('newsletter_info', 'You are already subscribed to our newsletter!');
        }

        $subscriber = NewsletterSubscriber::create(['email' => $email]);

        // Email to subscriber
        try {
            Mail::to($email)->send(new NewsletterUserMail($subscriber));
        } catch (\Throwable $e) {
            Log::error('Newsletter user email failed: ' . $e->getMessage(), [
                'email' => $email,
            ]);
        }

        // Email to admin
        $adminEmail = config('mail.admin_order_email') ?: config('mail.from.address');
        if ($adminEmail) {
            try {
                Mail::to($adminEmail)->send(new NewsletterAdminMail($subscriber));
            } catch (\Throwable $e) {
                Log::error('Newsletter admin email failed: ' . $e->getMessage(), [
                    'email' => $email,
                ]);
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Thank you for subscribing! Check your email for a confirmation.',
            ]);
        }

        return back()->with('newsletter_success', 'Thank you for subscribing! Check your email for a confirmation.');
    }
}
