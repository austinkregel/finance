<?php
declare(strict_types=1);

namespace App\Notifications;

use App\Mail\BudgetBreachEstablishedAmountMail;
use Illuminate\Notifications\Messages\SlackMessage;
use NotificationChannels\Discord\DiscordMessage;

class BudgetBreachedEstablishedAmountNotification extends AlertNotification
{
    public function toMail($notifiable)
    {
        return (new BudgetBreachEstablishedAmountMail($this->alertLog))->to($notifiable->email);
    }

    public function toSlack($notifiable)
    {
        return (new SlackMessage())
            ->content($this->toArray($notifiable)['title'])
            ->to($this->alertLog->alert->messaging_service_channel)
            ->warning();
    }

    public function toDiscord($notifiable)
    {
        return (new DiscordMessage())
            ->body($this->toArray($notifiable)['title']);
    }
}
