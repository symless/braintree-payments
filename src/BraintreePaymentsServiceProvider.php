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
		$this->loadViewsFrom(__DIR__ . '/../resources/views', 'symless/braintree-payments');

		$this->publishes([
			__DIR__ . '/../resources/views' => base_path('resources/views/vendor/symless/braintree-payments'),
		]);
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