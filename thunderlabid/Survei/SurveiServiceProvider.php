<?php

namespace Thunderlabid\Survei;

use Illuminate\Support\ServiceProvider;
use Event;

class SurveiServiceProvider extends ServiceProvider
{
	public function boot()
	{
		////////////////
		// Validation //
		////////////////
		Event::listen('Thunderlabid\Survei\Events\Survei\SurveiCreating', 'Thunderlabid\Survei\Listeners\SavingSurvei');
		Event::listen('Thunderlabid\Survei\Events\Survei\SurveiUpdating', 'Thunderlabid\Survei\Listeners\SavingSurvei');

		Event::listen('Thunderlabid\Survei\Events\SurveiDetail\SurveiDetailCreating', 'Thunderlabid\Survei\Listeners\SavingSurveiDetail');
		Event::listen('Thunderlabid\Survei\Events\SurveiDetail\SurveiDetailUpdating', 'Thunderlabid\Survei\Listeners\SavingSurveiDetail');

		Event::listen('Thunderlabid\Survei\Events\SurveiFoto\SurveiFotoCreating', 'Thunderlabid\Survei\Listeners\SavingSurveiFoto');
		Event::listen('Thunderlabid\Survei\Events\SurveiFoto\SurveiFotoUpdating', 'Thunderlabid\Survei\Listeners\SavingSurveiFoto');
	}

	public function register()
	{
		
	}
}