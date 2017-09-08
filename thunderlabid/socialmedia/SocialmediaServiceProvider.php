<?php

namespace Thunderlabid\Socialmedia;

use Illuminate\Support\ServiceProvider;
use Event;
use Thunderlabid\Socialmedia\Listeners\SavingValidation;
use Thunderlabid\Socialmedia\Listeners\DeleteValidation;

use Thunderlabid\Socialmedia\Events\Accounts\AccountCreating;
use Thunderlabid\Socialmedia\Events\Accounts\AccountUpdating;
use Thunderlabid\Socialmedia\Events\Accounts\AccountDeleting;

use Thunderlabid\Socialmedia\Events\Followers\FollowerCreating;
use Thunderlabid\Socialmedia\Events\Followers\FollowerUpdating;
use Thunderlabid\Socialmedia\Events\Followers\FollowerDeleting;

use Thunderlabid\Socialmedia\Events\IGEngage\IGEngageCreating;
use Thunderlabid\Socialmedia\Events\IGEngage\IGEngageUpdating;
use Thunderlabid\Socialmedia\Events\IGEngage\IGEngageDeleting;

use Thunderlabid\Socialmedia\Events\IGStatistics\IGStatisticsCreating;
use Thunderlabid\Socialmedia\Events\IGStatistics\IGStatisticsUpdating;
use Thunderlabid\Socialmedia\Events\IGStatistics\IGStatisticsDeleting;

use Thunderlabid\Socialmedia\Events\IGMedia\IGMediaCreating;
use Thunderlabid\Socialmedia\Events\IGMedia\IGMediaUpdating;
use Thunderlabid\Socialmedia\Events\IGMedia\IGMediaDeleting;

class SocialmediaServiceProvider extends ServiceProvider
{
	public function boot()
	{
		////////////////
		// Validation //
		////////////////
		Event::listen(AccountCreating::class			, SavingValidation::class);
		Event::listen(AccountUpdating::class			, SavingValidation::class);
		Event::listen(FollowerCreating::class			, SavingValidation::class);
		Event::listen(FollowerUpdating::class			, SavingValidation::class);
		Event::listen(IGEngageCreating::class			, SavingValidation::class);
		Event::listen(IGEngageUpdating::class			, SavingValidation::class);
		Event::listen(IGStatisticsCreating::class		, SavingValidation::class);
		Event::listen(IGStatisticsUpdating::class		, SavingValidation::class);
		Event::listen(IGMediaCreating::class			, SavingValidation::class);
		Event::listen(IGMediaUpdating::class			, SavingValidation::class);

		Event::listen(AccountDeleting::class			, DeleteValidation::class);
		Event::listen(FollowerDeleting::class			, DeleteValidation::class);
		Event::listen(IGEngageDeleting::class			, DeleteValidation::class);
		Event::listen(IGStatisticsDeleting::class		, DeleteValidation::class);
		Event::listen(IGMediaDeleting::class			, DeleteValidation::class);

	}

	public function register()
	{
		
	}
}