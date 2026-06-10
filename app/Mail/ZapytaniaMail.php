<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ZapytaniaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $emails;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data, $emails)
    {
        $this->data = $data;
        $this->emails = $emails;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // Adres FROM musi być taki sam jak MAIL_USERNAME w .env (crmsystem@mkl.pl)
        return $this
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->to($this->emails)
            ->subject('Nowe zapytanie - '.$this->data->id_zapyt)
            ->view('zapytaniaPdf')
            ->with(['data' => $this->data]);
    }

}
