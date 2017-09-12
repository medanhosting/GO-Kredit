<?php

namespace Thunderlabid\Pengajuan;

use Illuminate\Support\ServiceProvider;
use Event;

class PengajuanServiceProvider extends ServiceProvider
{
	public function boot()
	{
		////////////////
		// Validation //
		////////////////
		Event::listen('Thunderlabid\Pengajuan\Events\Status\StatusCreating', 'Thunderlabid\Pengajuan\Listeners\SavingStatus');
		Event::listen('Thunderlabid\Pengajuan\Events\Status\StatusUpdating', 'Thunderlabid\Pengajuan\Listeners\SavingStatus');

		Event::listen('Thunderlabid\Pengajuan\Events\Pengajuan\PengajuanCreating', 'Thunderlabid\Pengajuan\Listeners\AssignIDPengajuan');
		Event::listen('Thunderlabid\Pengajuan\Events\Pengajuan\PengajuanCreating', 'Thunderlabid\Pengajuan\Listeners\SavingPengajuan');
		Event::listen('Thunderlabid\Pengajuan\Events\Pengajuan\PengajuanCreated', 'Thunderlabid\Pengajuan\Listeners\GenerateStatusPermohonan');
		Event::listen('Thunderlabid\Pengajuan\Events\Pengajuan\PengajuanUpdating', 'Thunderlabid\Pengajuan\Listeners\SavingPengajuan');

		Event::listen('Thunderlabid\Pengajuan\Events\Jaminan\JaminanCreating', 'Thunderlabid\Pengajuan\Listeners\SavingJaminan');
		Event::listen('Thunderlabid\Pengajuan\Events\Jaminan\JaminanUpdating', 'Thunderlabid\Pengajuan\Listeners\SavingJaminan');

		Event::listen('Thunderlabid\Pengajuan\Events\Jaminan\JaminanCreating', 'Thunderlabid\Pengajuan\Listeners\BatasanJaminan');
		Event::listen('Thunderlabid\Pengajuan\Events\Jaminan\JaminanUpdating', 'Thunderlabid\Pengajuan\Listeners\BatasanJaminan');

		Event::listen('Thunderlabid\Pengajuan\Events\Jaminan\JaminanCreating', 'Thunderlabid\Pengajuan\Listeners\DuplikasiJaminan');
		Event::listen('Thunderlabid\Pengajuan\Events\Jaminan\JaminanUpdating', 'Thunderlabid\Pengajuan\Listeners\DuplikasiJaminan');
	}

	public function register()
	{
		
	}
}