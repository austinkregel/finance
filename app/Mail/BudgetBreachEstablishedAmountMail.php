<?php

namespace App\Mail;

use App\Models\AlertLog;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BudgetBreachEstablishedAmountMail extends Mailable
{
    use Queueable, SerializesModels;

    public AlertLog $alertLog;

    public function __construct(AlertLog $alertLog)
    {
        $this->alertLog = $alertLog;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->alertLog->load([
            'budget',
        ]);

        return $this->markdown('mail.budget-breach', [
            'alertLog' => $this->alertLog,
            'budget' => $this->alertLog->budget,
        ]);
    }
}
