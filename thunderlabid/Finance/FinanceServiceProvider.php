<?php

namespace Thunderlabid\Finance;

use Illuminate\Support\ServiceProvider;
use Event;

class FinanceServiceProvider extends ServiceProvider
{
	public function boot()
	{
		////////////////
		// Validation //
		////////////////
		Event::listen('Thunderlabid\Finance\Events\Account\AccountCreating', 'Thunderlabid\Finance\Listeners\Saving');
		Event::listen('Thunderlabid\Finance\Events\Account\AccountUpdating', 'Thunderlabid\Finance\Listeners\Saving');
		Event::listen('Thunderlabid\Finance\Events\Account\AccountDeleting', 'Thunderlabid\Finance\Listeners\Deleting');

		Event::listen('Thunderlabid\Finance\Events\COA\COACreating', 'Thunderlabid\Finance\Listeners\Saving');
		Event::listen('Thunderlabid\Finance\Events\COA\COAUpdating', 'Thunderlabid\Finance\Listeners\Saving');
		Event::listen('Thunderlabid\Finance\Events\COA\COADeleting', 'Thunderlabid\Finance\Listeners\Deleting');

		Event::listen('Thunderlabid\Finance\Events\TransactionDetail\TransactionDetailCreating', 'Thunderlabid\Finance\Listeners\Saving');
		Event::listen('Thunderlabid\Finance\Events\TransactionDetail\TransactionDetailUpdating', 'Thunderlabid\Finance\Listeners\Saving');
		Event::listen('Thunderlabid\Finance\Events\TransactionDetail\TransactionDetailDeleting', 'Thunderlabid\Finance\Listeners\Deleting');
	}

	public function register()
	{
		
	}
}