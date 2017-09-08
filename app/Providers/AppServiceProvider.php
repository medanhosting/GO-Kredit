<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Form;

class AppServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		//
		error_reporting(E_ERROR);
	    Schema::defaultStringLength(191);

		//
		Form::component('bsText'       	, 'bootstrap.form.text'				, ['label' => null, 'name', 'value' => null, 'attributes' => [], 'show_error' => true, 'addon' => null]);
		Form::component('bsSelect'      , 'bootstrap.form.select'			, ['label' => null, 'name', 'options' => [], 'value' => null, 'attributes' => [], 'show_error' => true]);
		Form::component('bsSearch'      , 'bootstrap.form.search'			, ['name', 'value' => null, 'attributes' => [], 'show_error' => true]);
		Form::component('bsPassword'	, 'bootstrap.form.password'			, ['label' => null, 'name', 'attributes' => [], 'show_error' => true]);
		Form::component('bsSubmit'		, 'bootstrap.form.submit'			, ['label' => null, 'attributes' => []]);
		Form::component('bsIcon'		, 'bootstrap.icon'					, ['icon' => null, 'class' => null]);

	}

	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}
}
