<?php

namespace App\Console\Commands;

use App\AccountKpi;
use App\Condition;
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
        $this->writeToDisk('js/channels.js', [
            SlackWebhookChannel::class => [
                'name' => 'Slack',
                'fields' => ['webhook_url', 'channel']
            ],
            DiscordChannel::class => [
                'name' => 'Discord',
                'fields' => ['webhook_url', 'channel']
            ],
            WebhookChannel::class => [
                'name' => 'Webhook',
                'fields' => ['webhook_url', 'payload']
            ],
            MailChannel::class => [
                'name' => 'Email',
                'fields' => ['email']
            ],
            NexmoSmsChannel::class => [
                'name' => 'Nexmo',
                'fields' => ['sms_number']
            ],
            DatabaseChannel::class => [
                'name' => 'In-site notification',
                'fields' => []
            ],
        ]);

        $this->writeToDisk('js/alert-events.js', [
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
        ]);

        $this->writeToDisk('js/condition-parameters.js', [
            [
                'value' => 'name',
                'name' => 'transaction.name'
            ],
            [
                'value' => 'amount',
                'name' => 'transaction.amount'
            ],
            [
                'value' => 'account.name',
                'name' => 'transaction.account.name'
            ],
            [
                'value' => 'date',
                'name' => 'transaction.date'
            ],
            [
                'value' => 'pending',
                'name' => 'transaction.pending'
            ],
            [
                'value' => 'category.name',
                'name' => 'transaction.category.name'
            ],
            [
                'value' => 'tag.name.en',
                'name' => 'transaction.tag.name.en'
            ],
        ]);

        $this->writeToDisk('js/alert-events.js', [
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
        ]);

        $this->writeToDisk('js/condition-comparator.js', array_map(function ($comparator) {
            return [
                'value' => $comparator,
                'name' => $comparator,
            ];
        }, Condition::ALL_COMPARATORS));

    }

    protected function writeToDisk(string $file, array $data)
    {
        file_put_contents(resource_path($file), sprintf('module.exports = %s', json_encode($data, JSON_PRETTY_PRINT)));
    }
}
