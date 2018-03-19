<?php

namespace Thunderlabid\SMSGway;

use Illuminate\Support\ServiceProvider;
use Event, Config;

class SMSGwayServiceProvider extends ServiceProvider
{
	public function boot()
	{
		////////////////
		// Validation //
		////////////////
		Event::listen('Thunderlabid\SMSGway\Events\SMSQueue\SMSQueueCreating', 'Thunderlabid\SMSGway\Listeners\Saving');
		Event::listen('Thunderlabid\SMSGway\Events\SMSQueue\SMSQueueUpdating', 'Thunderlabid\SMSGway\Listeners\Saving');
		Event::listen('Thunderlabid\SMSGway\Events\SMSQueue\SMSQueueDeleting', 'Thunderlabid\SMSGway\Listeners\Deleting');

		Event::listen('Thunderlabid\SMSGway\Events\SMSQueue\SMSQueueCreated', 'Thunderlabid\SMSGway\Listeners\KirimSMS');
		Event::listen('Thunderlabid\SMSGway\Events\SMSQueue\SMSQueueUpdated', 'Thunderlabid\SMSGway\Listeners\KirimSMS');
		// Event::listen('Thunderlabid\SMSGway\Events\SMSQueue\SMSQueueUpdating', 'Thunderlabid\SMSGway\Listeners\KirimSMS');
	}

	public function register(){
		Config::set('messagebird.access_key', env('SMS_MBIRD_AK', 'none'));
		Config::set('messagebird.originator', 'GOKREDIT');
	}
}