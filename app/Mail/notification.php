<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class notification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    protected $nom="";
    protected $prenom="";
    protected $date_prevue_vacc="";

    public function __construct($nom,$prenom,$date_prevue_vacc)
    {
        $this->nom=$nom;
        $this->prenom=$prenom;
        $this->date_prevue_vacc=$date_prevue_vacc;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {       $nom=$this->nom;
        return $this->markdown('email/notification')->with(compact('nom'));
    }
}
