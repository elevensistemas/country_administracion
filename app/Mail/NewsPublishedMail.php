<?php

namespace App\Mail;

use App\Models\News;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewsPublishedMail extends Mailable
{
    use Queueable, SerializesModels;

    public News $news;
    public ?User $recipient;

    /**
     * Create a new message instance.
     */
    public function __construct(News $news, ?User $recipient = null)
    {
        $this->news = $news;
        $this->recipient = $recipient;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Nueva Novedad: ' . $this->news->title . ' - La Ranita')
                    ->view('emails.news_published');
    }
}