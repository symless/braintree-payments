<?php

namespace Symless\BraintreePayments\Tests;

use Carbon\Carbon;
use Braintree_Configuration;
use Illuminate\Http\Request;
use PHPUnit_Framework_TestCase;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Laravel\Cashier\Http\Controllers\WebhookController;

class BraintreePaymentsTest extends PHPUnit_Framework_TestCase
{
	public static function setUpBeforeClass()
	{
		if (file_exists(__DIR__ . '/../.env')) {
			$dotenv = new \Dotenv\Dotenv(__DIR__ . '/../');
			$dotenv->load();
		}
	}
}