<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ZapytaniaMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->from('crm@mkl.pl', 'CRM - MKLBAU')
            ->to('pszemo.koziniak@gmail.com')
//            ->cc('natalia.paldyna@mkl.pl')
//            ->bcc('krzysztof.kielczykowski@mkl.pl')
            ->subject('Nowe zapytanie - '.$this->data->id_zapyt)
            ->view('zapytaniaPdf')
            ->with(['data' => $this->data]);
    }

}
