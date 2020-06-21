<?php

namespace App\Console\Commands;

use App\AccountKpi;
use App\Events\TransactionCreated;
use App\Events\TransactionGroupedEvent;
use App\Events\TransactionUpdated;
use App\Models\Account;
use Illuminate\Console\Command;
use Illuminate\Notifications\Channels\BroadcastChannel;
use Illuminate\Notifications\Channels\DatabaseChannel;
use Illuminate\Notifications\Channels\MailChannel;
use Illuminate\Notifications\Channels\NexmoSmsChannel;
use Illuminate\Notifications\Channels\SlackWebhookChannel;
use Kregel\LaravelAbstract\Repositories\GenericRepository;
use NotificationChannels\Discord\DiscordChannel;
use NotificationChannels\Webhook\WebhookChannel;

class GenerateChannelsAndAlertsFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:channels-and-alerts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build channels and alerts.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $channels =  [
            [
                'type' => SlackWebhookChannel::class,
                'name' => 'Slack',
            ],
            [
                'type' => DiscordChannel::class,
                'name' => 'Discord',
            ],
            [
                'type' => WebhookChannel::class,
                'name' => 'Webhook',
            ],
            [
                'type' => BroadcastChannel::class,
                'name' => 'Broadcasts',
            ],
            [
                'type' => MailChannel::class,
                'name' => 'Email',
            ],
            [
                'type' => NexmoSmsChannel::class,
                'name' => 'Nexmo',
            ],
            [
                'type' => DatabaseChannel::class,
                'name' => 'In-site notification',
            ],
        ];

        file_put_contents(resource_path('js/channels.js'), sprintf('module.exports = %s', json_encode($channels)));


        $channels =  [
            [
                'type' => TransactionUpdated::class,
                'name' => 'When a transaction is updated (moving from pending to not pending, updating amounts, etc...)'
            ],
            [
                'type' => TransactionCreated::class,
                'name' => 'When a transaction is initially created (only fired once per transaction)',
            ],
            [
                'type' => TransactionGroupedEvent::class,
                'name' => 'When a transaction is added to a group (this gives you access to the `tag` variable in your title, body and payload.)'
            ],
        ];

        file_put_contents(resource_path('js/alert-events.js'), sprintf('module.exports = %s', json_encode($channels)));
    }
}
