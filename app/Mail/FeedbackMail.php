<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FeedbackMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $description;
    public $files;

    public function __construct($user, $description, $files = [])
    {
        $this->user = $user;
        $this->description = $description;
        $this->files = $files;
    }

    public function build()
    {
        $email = $this->subject('Feedback/Masukan Baru - BIMMO')
            ->view('emails.feedback');

        foreach ($this->files as $file) {
            $email->attach($file->getRealPath(), [
                'as' => $file->getClientOriginalName(),
                'mime' => $file->getMimeType(),
            ]);
        }

        return $email;
    }
}
