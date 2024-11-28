<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Person;
use App\Models\User;


class NurseActivatedNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    protected $user;
    protected $person;
    protected $signedUrl;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, Person $person, $signedUrl)
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
            subject: 'Nurse Activated Notification',
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
            view: 'emails.nurse_activated',
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
