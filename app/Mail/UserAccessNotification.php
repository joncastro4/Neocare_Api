<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class UserAccessNotification extends Mailable
{
    use Queueable, SerializesModels;

    protected $user, $signedUrl, $person;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, $person, $signedUrl)
    {
        $this->user = $user;
        $this->person = $person;
        $this->signedUrl = $signedUrl;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'User Access Notification',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'emails.user-access-notification',
            with: [
                'user' => $this->user,
                'person' => $this->person,
                'signedUrl' => $this->signedUrl
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
