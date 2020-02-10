<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Mail;

class Simple extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->from($this->from_email,$this->from_name);
        $this->subject($this->simple_subject);
        
        
        $this->bcc($this->arr_bcc);
//        $this->cc($d);
//        if(move_uploaded_file($d['tmp_name'],$base_path.'/'.$d['name'])){
//            $this->attach($base_path.'/'.$d['name'], ['as' => $d['name']]);
//        }
        return $this;
    }
    public function simple_send()
    {
        $this->with($this->arr_variable);
        $this->view($this->template);
        Mail::to($this->arr_to)->send($this);
    }
}
