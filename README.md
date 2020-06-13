# finance
A self hosted app to help you get a better understanding of your finances.
 - [screenshots](#screenshots)
 - [Installing/Setting up](#installing-setting-up)
 - [Cron Jobs](#cron-jobs)
 
 
# Installing/Setting up
At the moment, this project is configured to be used from docker containers. You may use non-docker versions of the software.

### Configuration
First and foremost you must have a [Plaid account](https://plaid.com). Once you have your account you'll need to copy your [`development` tokens](https://dashboard.plaid.com/overview/development).

You'll then need to go to your `.env` file in the root of this project (or copy the `.env.example` file if the`.env` file doesn't exist) and add the following values.
```
PLAID_ENV=development
PLAID_PUBLIC_KEY=
PLAID_SECRET=
PLAID_CLIENT_ID=
```
And then fill in your plaid values.

### Requirements
  - MySql
  - PHP >7.2
  - Redis
Mysql is used as our database for the app. It's where we store all our information. PHP is how the project runs. And Redis is there too...

### Using Docker
```bash
docker-compose up --build -d
```
Which will build the containers, and start them daemonized (in the background)

(If your nginx container won't start, be sure you don't have any other programs using port 80)

 - Install the composer dependencies `docker exec -it php composer install`
 - Install the npm/yarn dependencies `npm install` or `yarn` (Yes you need this on your host computer for now.)
 - Build the assets `npm run production`
 - Navigate to `127.0.0.1` and register an `account. Then navigate to your `settings` page and link your bank account
 - Refresh the page to ensure your accounts show up. If no accounts show up the run the `sync:tokens` command `docker exec -it php artisan sync:tokens` to pull down your accounts.
 Once the accounts are pulled you need to sync your transaction history (actual history will vary from bank to bank, USAA only goes back 3 months and others dont limit history).
 - Run the command `sync:plaid 5` to have the app contact your bank and pull the last 5 months of historical transactions. `docker exec -it php artisan sync:plaid 5`
 - Go to the website `127.0.0.1` and classify your transactions. (You can classify transactions by clicking the i icon).

# Cron Jobs

At the moment there are 2 commands that need to be ran on cron jobs.
 - The `sync:plaid 1` command to pull the transactions for the last month.
 - The `sync:tokens` command to sync any extra accounts you might have added via your bank.

If you can configure the Laravel task scheduler `php artisan schedule:run` then those commands will be ran when they're suppose to.

Or you can configure a manual cron job to run those commands.

# Screenshots
![Dashboard Screenshot](https://raw.githubusercontent.com/austinkregel/finance/master/screenshot.jpg)  
![Calendar Screenshot](https://raw.githubusercontent.com/austinkregel/finance/master/screenshot-calendar.jpg)
