<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    /**
     * Display the contact page.
     */
    public function index()
    {
        return view('contact');
    }

    /**
     * Store a new contact message.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->only(['name', 'email', 'phone', 'subject', 'message']);

        // Handle file upload
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('contacts', $filename, 'public');
            $data['attachment'] = $path;
        }

        // Save to database
        $contact = Contact::create($data);

        // Send email notification
        try {
            Mail::send('emails.contact', ['contact' => $contact], function ($message) use ($contact) {
                $message->to(config('mail.from.address'))
                    ->subject('New Contact Form Submission - ' . ($contact->subject ?? 'No Subject'));
                $message->replyTo($contact->email, $contact->name);
            });
        } catch (\Exception $e) {
            \Log::error('Failed to send contact email: ' . $e->getMessage());
        }

        return back()->with('success', 'Thank you for contacting us! We will get back to you soon.');
    }
}
