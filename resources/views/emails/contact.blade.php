<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Contact Form Submission</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #ff6b18; color: white; padding: 20px; text-align: center; }
        .content { background: #f9f9f9; padding: 20px; }
        .field { margin-bottom: 15px; }
        .field strong { display: inline-block; width: 120px; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>New Contact Form Submission</h2>
        </div>
        <div class="content">
            <div class="field">
                <strong>Name:</strong> {{ $contact->name }}
            </div>
            <div class="field">
                <strong>Email:</strong> {{ $contact->email }}
            </div>
            @if($contact->phone)
            <div class="field">
                <strong>Phone:</strong> {{ $contact->phone }}
            </div>
            @endif
            @if($contact->subject)
            <div class="field">
                <strong>Subject:</strong> {{ $contact->subject }}
            </div>
            @endif
            <div class="field">
                <strong>Message:</strong>
                <p style="margin-top: 10px; padding: 10px; background: white; border-left: 3px solid #ff6b18;">
                    {{ $contact->message }}
                </p>
            </div>
            @if($contact->attachment)
            <div class="field">
                <strong>Attachment:</strong> File uploaded
            </div>
            @endif
            <div class="field">
                <strong>Received:</strong> {{ $contact->created_at->format('M d, Y h:i A') }}
            </div>
        </div>
        <div class="footer">
            <p>This email was sent from your website contact form.</p>
        </div>
    </div>
</body>
</html>
