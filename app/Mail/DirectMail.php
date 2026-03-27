<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DirectMail extends Mailable
{
    use SerializesModels;

    public $params;
    public $viewName;
    public $subjectName;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($params, $viewName, $subjectName)
    {
        $this->params = $params;
        $this->viewName = $viewName;
        $this->subjectName = $subjectName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(config('mail.from.address'), config('mail.from.name'))
                    ->subject($this->subjectName)
                    ->view($this->viewName);
    }
}
