# finance
![Tests](https://github.com/austinkregel/finance/workflows/Tests/badge.svg)

A self hosted app to help you get a better understanding of your finances.
 - [screenshots](#screenshots)
 - [Installing/Setting up](#installing-setting-up)
 - [Cron Jobs](#cron-jobs)
 
 
# Installing/Setting up
 For installation instructions, please refer to [the project wiki](https://github.com/austinkregel/finance/wiki/Before-you-begin)
 
# Features
 - Group transaction by a set of conditions.
 - Send alerts to Discord, Slack, Webhooks, email, Nexmo, and In-site notifications!
 - Sync older transactions
 - Graph your groups and compare numbers vs a previous time period (a trend)
 - Add together all your transactions in a time period (a metric)
 - Can automatically sync your transactions

# Cron Jobs
If you can configure the Laravel task scheduler `php artisan schedule:run` then commands will be ran when they're suppose to.
```cron
* * * * * "docker exec -it finance_php_1 php artisan queue:work"
```
Or you can configure a manual cron job to run those commands.

# Screenshots
![Transactions](transactions-page.PNG)  
![Accounts](accounts.PNG)  
![Alerts](alerts.PNG)  
![Grouping transactions](groupings.PNG)  
![Metrics](metrics.PNG)
