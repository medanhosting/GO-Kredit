<?php

namespace Thunderlabid\Manajemen;

use Illuminate\Support\ServiceProvider;
use Event;

class ManajemenServiceProvider extends ServiceProvider
{
	public function boot()
	{
		////////////////
		// Validation //
		////////////////
		Event::listen('Thunderlabid\Manajemen\Events\Orang\OrangCreating', 'Thunderlabid\Manajemen\Listeners\SavingOrang');
		Event::listen('Thunderlabid\Manajemen\Events\Orang\OrangUpdating', 'Thunderlabid\Manajemen\Listeners\SavingOrang');
		Event::listen('Thunderlabid\Manajemen\Events\Orang\OrangDeleting', 'Thunderlabid\Manajemen\Listeners\DeletingOrang');
		
		Event::listen('Thunderlabid\Manajemen\Events\PenempatanKaryawan\PenempatanKaryawanSaving', 'Thunderlabid\Manajemen\Listeners\SavingPenempatanKaryawan');
		Event::listen('Thunderlabid\Manajemen\Events\PengaturanScopes\PengaturanScopesSaving', 'Thunderlabid\Manajemen\Listeners\SavingPengaturanScopes');
		Event::listen('Thunderlabid\Manajemen\Events\Kantor\KantorSaving', 'Thunderlabid\Manajemen\Listeners\SavingKantor');
		Event::listen('Thunderlabid\Manajemen\Events\MobileApi\MobileApiSaving', 'Thunderlabid\Manajemen\Listeners\SavingMobileApi');
		
		//////////////////////
		// Encrypt Password //
		//////////////////////
		Event::listen('Thunderlabid\Manajemen\Events\Orang\OrangCreating', 'Thunderlabid\Manajemen\Listeners\EncryptPassword');
		Event::listen('Thunderlabid\Manajemen\Events\Orang\OrangUpdating', 'Thunderlabid\Manajemen\Listeners\EncryptPassword');
	
		//////////////////////
		//  Encrypt Secret  //
		//////////////////////	
		Event::listen('Thunderlabid\Manajemen\Events\MobileApi\MobileApiSaving', 'Thunderlabid\Manajemen\Listeners\EncryptSecret');
		
		//////////////////////
		//    Assign  NIP   //
		//////////////////////
		Event::listen('Thunderlabid\Manajemen\Events\PenempatanKaryawan\PenempatanKaryawanSaving', 'Thunderlabid\Manajemen\Listeners\AutoAssignNIP');
	}

	public function register()
	{
		
	}
}