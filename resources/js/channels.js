module.exports = [
    {
        "type": "Illuminate\\Notifications\\Channels\\SlackWebhookChannel",
        "name": "Slack"
    },
    {
        "type": "NotificationChannels\\Discord\\DiscordChannel",
        "name": "Discord"
    },
    {
        "type": "NotificationChannels\\Webhook\\WebhookChannel",
        "name": "Webhook"
    },
    {
        "type": "Illuminate\\Notifications\\Channels\\BroadcastChannel",
        "name": "Broadcasts"
    },
    {
        "type": "Illuminate\\Notifications\\Channels\\MailChannel",
        "name": "Email"
    },
    {
        "type": "Illuminate\\Notifications\\Channels\\NexmoSmsChannel",
        "name": "Nexmo"
    },
    {
        "type": "Illuminate\\Notifications\\Channels\\DatabaseChannel",
        "name": "In-site notification"
    }
]