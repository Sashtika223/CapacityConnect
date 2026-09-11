<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Mailable sent to a trainer when their account is approved by an admin.
 *
 * Implements ShouldQueue because QUEUE_CONNECTION=database is already configured
 * and the jobs table exists. This means approval completes instantly for the Admin
 * while email delivery is handled by the queue worker.
 *
 * To process queued emails, run:
 *   php artisan queue:work
 *
 * Or for a single batch:
 *   php artisan queue:work --once
 *
 * To run a specific queue:
 *   php artisan queue:work --queue=default
 */
class TrainerApproved extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Number of times the job may be attempted if it fails.
     */
    public int $tries = 3;

    /**
     * Number of seconds to wait before retrying a failed attempt.
     */
    public int $backoff = 60;

    public function __construct(public User $trainer) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Capacity Connect — Trainer Registration Approved',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.trainer-approved',
        );
    }
}
