<?php

use App\Events\TransactionCreated;
use App\Models\Institution;
use App\Models\Transaction;
use Illuminate\Foundation\Inspiring;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function (): void {
    $this->comment(Inspiring::quote());
})->describe('Display an inspiring quote');

Artisan::command('institution {institution_id}', function (): void {
    /** @var \App\Services\Banking\PlaidService $service */
    $service = app(\App\Services\Banking\PlaidService::class);

    if (! Institution::where('institution_id', $this->argument('institution_id'))->exists()) {
        $item = json_decode($service->getInstitutionsById($this->argument('institution_id'))->toJson());

        if ($item->logo) {
            $logo = 'institutions/'.$item->institution_id.'.png';
            file_put_contents(public_path($logo), base64_decode($item->logo));
        }

        Institution::create([
            'name' => $item->name,
            'institution_id' => $item->institution_id,
            'logo' => $logo ?? null,
            'products' => $item->products,
            'site_url' => $item->url ?? null,
            'primary_color' => $item->primary_color ?? null,
        ]);
    }
});

Artisan::command('test', function (): void {
    $transaction = Transaction::find(129);
    $event = new TransactionCreated($transaction);

    app(\App\Listeners\ApplyGroupToTransactionAutomaticallyListener::class)->handle($event);
});
