<?php

namespace Symless\BraintreePayments\Tests;

use Carbon\Carbon;
use Braintree_Configuration;
use Illuminate\Http\Request;
use PHPUnit_Framework_TestCase;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Symless\BraintreePayments\Http\Controllers\BraintreeWebhookController;

class BraintreePaymentsTest extends PHPUnit_Framework_TestCase
{
	public static function setUpBeforeClass()
	{
		if (file_exists(__DIR__ . '/../.env')) {
			$dotenv = new \Dotenv\Dotenv(__DIR__ . '/../');
			$dotenv->load();
		}
	}

	public function setUp()
	{
		Braintree_Configuration::environment('sandbox');
		Braintree_Configuration::merchantId(getenv('BRAINTREE_MERCHANT_ID'));
		Braintree_Configuration::publicKey(getenv('BRAINTREE_PUBLIC_KEY'));
		Braintree_Configuration::privateKey(getenv('BRAINTREE_PRIVATE_KEY'));

		Eloquent::unguard();

		$db = new DB();

		$db->addConnection([
			'driver'    =>  'sqlite',
			'database'  => ':memory:',
		]);

		$db->bootEloquent();
		$db->setAsGlobal();

		$this->schema()->create('users', function($table) {
			$table->increments('id');
			$table->string('email');
			$table->string('forename');
			$table->string('surname');
			$table->string('braintree_id')->nullable();
			$table->string('paypal_email')->nullable();
			$table->string('card_brand')->nullable();
			$table->string('card_last_four')->nullable();
			$table->timestamps();
		});
	}

	public function tearDown()
	{
		$this->schema()->drop('users');
	}

	public function testUserCanBeCreated()
	{
		/** @var User $owner */
		$owner = User::create([
			'email'     => 'travisci@symless.com',
			'forename'  => 'Travis',
			'surname'   => 'Testing'
		]);

		$braintreeCustomer = $owner->createAsBraintreeCustomer($this->getTestToken());

		$this->assertEquals($owner->surname, $braintreeCustomer->lastName);
		$this->assertEquals($owner->forename, $braintreeCustomer->firstName);
		$this->assertEquals($owner->email, $braintreeCustomer->email);
	}

	protected function getTestToken()
	{
		return 'fake-valid-nonce';
	}

	protected function schema()
	{
		return $this->connection()->getSchemaBuilder();
	}

	protected function connection()
	{
		return Eloquent::getConnectionResolver()->connection();
	}
}

class User extends Eloquent
{
	use \Symless\BraintreePayments\Billable;
}

class BraintreePaymentsTestControllerStub extends BraintreeWebhookController
{
	protected function parseBraintreeNotification( $request )
	{
		return json_decode($request->getContent());
	}
}