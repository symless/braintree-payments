<?php

namespace Symless\BraintreePayments;

use Illuminate\Support\ServiceProvider;

/**
 * Class BraintreePaymentsServiceProvider
 *
 * @package Symless\BraintreePayments
 */
class BraintreePaymentsServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{	
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		// @noop
	}
}
