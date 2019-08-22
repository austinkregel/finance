<?php

namespace Tests\Integration\Observers;

use App\Jobs\SyncPlaidAccountsJob;
use App\Models\AccessToken;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessTokenObserverTest extends TestCase
{
    use DatabaseMigrations, RefreshDatabase;

    public function testJobDispatched()
    {
        \Bus::fake();

        $resource = AccessToken::create([
            'user_id' => 1,
            'token' => 'Fake token',
        ]);

        \Bus::assertDispatched(SyncPlaidAccountsJob::class);
    }
}
