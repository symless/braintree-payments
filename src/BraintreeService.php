<?php

namespace Symless\BraintreePayments;

use Braintree\Plan as BraintreePlan;
use Braintree\Exception as BraintreeException;

class BraintreeService
{
	public static function findPlan($id)
	{
		$plans = BraintreePlan::all();

		foreach ($plans as $plan) {
			if ($id === $plan->id) {
				return $plan;
			}
		}

		throw new BraintreeException("Unable to find Braintree plan ID: [{$id}]");
	}
}