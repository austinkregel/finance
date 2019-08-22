<?php

namespace App\Providers;

use Laravel\Telescope\EntryType;
use Laravel\Telescope\Telescope;
use Illuminate\Support\Facades\Gate;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\TelescopeApplicationServiceProvider;

class TelescopeServiceProvider extends TelescopeApplicationServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
         Telescope::night();

        $this->hideSensitiveRequestDetails();

        Telescope::filter(function (IncomingEntry $entry) {
            if ($entry->type === EntryType::REQUEST) {
                if (($entry->content['response_status'] ?? 200) >= 500) {
                    return true;
                }

                if (request()->fullUrl() === config('app.url')) {
                    return false;
                }

                foreach (config('telescope.ignore_url_blacklist') as $item) {
                    $match = trim($item, '/');
                    if (empty($match)) {
                        continue;
                    }

                    $urlBlacklistItem = str_replace('\*', '*', preg_quote($item, '/'));

                    preg_match('/' . $urlBlacklistItem . '/', request()->fullUrl(), $matches);

                    if (!empty($matches)) {
                        return false;
                    }
                }

                return true;
            }

            return true;
        });
    }

    /**
     * Prevent sensitive request details from being logged by Telescope.
     *
     * @return void
     */
    protected function hideSensitiveRequestDetails()
    {
        if ($this->app->isLocal()) {
            return;
        }

        Telescope::hideRequestParameters(['_token']);

        Telescope::hideRequestHeaders([
            'cookie',
            'x-csrf-token',
            'x-xsrf-token',
        ]);
    }

    /**
     * Register the Telescope gate.
     *
     * This gate determines who can access Telescope in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewTelescope', function ($user) {
            return in_array($user->email, [
                //
            ]);
        });
    }
}
