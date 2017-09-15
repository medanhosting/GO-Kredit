<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
	/**
	 * The event listener mappings for the application.
	 *
	 * @var array
	 */
	protected $listen = [
	];

	protected $subscribe = [
	];

	/**
	 * Register any events for your application.
	 *
	 * @return void
	 */
	public function boot()
	{
		parent::boot();

		//SURVEI DAN PENGAJUAN BRIDGE UPDATE STATUS
		Event::listen('Thunderlabid\Pengajuan\Events\Pengajuan\PengajuanCreated', 'App\Listeners\SimpanStatusDoingPermohonan');

		Event::listen('Thunderlabid\Survei\Events\SurveiDetail\SurveiDetailCreated', 'App\Listeners\SimpanStatusDoingSurvei');
		Event::listen('Thunderlabid\Survei\Events\SurveiDetail\SurveiDetailUpdated', 'App\Listeners\SimpanStatusDoingSurvei');

		Event::listen('Thunderlabid\Survei\Events\SurveiFoto\SurveiFotoCreated', 'App\Listeners\SimpanStatusDoingSurvei');
		Event::listen('Thunderlabid\Survei\Events\SurveiFoto\SurveiFotoUpdated', 'App\Listeners\SimpanStatusDoingSurvei');

		Event::listen('Thunderlabid\Pengajuan\Events\Analisa\AnalisaCreated', 'App\Listeners\SimpanStatusDoingAnalisa');
		Event::listen('Thunderlabid\Pengajuan\Events\Analisa\AnalisaUpdated', 'App\Listeners\SimpanStatusDoingAnalisa');
	}
}
