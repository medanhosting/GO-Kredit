<?php

namespace Thunderlabid\Auths;

use Illuminate\Support\ServiceProvider;
use Event;

class AuthServiceProvider extends ServiceProvider
{
	public function boot()
	{
		////////////////
		// Validation //
		////////////////
		Event::listen('Thunderlabid\Auths\Events\Users\UserCreating', 'Thunderlabid\Auths\Listeners\SavingUser');
		Event::listen('Thunderlabid\Auths\Events\Users\UserUpdating', 'Thunderlabid\Auths\Listeners\SavingUser');
		Event::listen('Thunderlabid\Auths\Events\Users\UserDeleting', 'Thunderlabid\Auths\Listeners\DeletingUser');
		
		//////////////////////
		// Encrypt Password //
		//////////////////////
		Event::listen('Thunderlabid\Auths\Events\Users\UserCreating', 'Thunderlabid\Auths\Listeners\EncryptPassword');
		Event::listen('Thunderlabid\Auths\Events\Users\UserUpdating', 'Thunderlabid\Auths\Listeners\EncryptPassword');
	}

	public function register()
	{
		
	}
}