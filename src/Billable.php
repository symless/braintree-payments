<?php

namespace Symless\BraintreePayments;

use Braintree\Customer as BraintreeCustomer;
use Braintree\Exception as BraintreeException;
use Braintree\PayPalAccount;
use Braintree\Transaction as BraintreeTransaction;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Trait Billable
 *
 * @package Symless\BraintreePayments
 */
trait Billable
{
	/**
	 * @var int
	 */
	protected $taxPercentage = 0;

	/**
	 * Create a Braintree customer for the given model.
	 *
	 * @param  string  $token
	 * @param  array  $options
	 * @return \Braintree\Customer
	 * @throws \Exception
	 */
	public function createAsBraintreeCustomer($token, array $options = [])
	{
		$response = BraintreeCustomer::create(
			array_replace_recursive([
				'firstName'          => $this->forename,
				'lastName'           => $this->surname,
				'email'              => $this->email,
				'paymentMethodNonce' => $token,
				'creditCard'         => [
					'options'   => [
						'verifyCard'    => true,
					],
				],
			], $options)
		);

		if (!$response->success) {
			throw new BraintreeException('Braintree was unable to create a customer: ' . $response->message);
		}

		$this->braintree_id = $response->customer->id;

		$paymentMethod = $this->paymentMethod();

		$paypalAccount = $paymentMethod instanceof PayPalAccount;

		$this->forceFill([
			'braintree_id'   => $response->customer->id,
			'paypal_email'   => $paypalAccount ? $paymentMethod->email : null,
			'card_brand'     => !$paypalAccount ? $paymentMethod->cardType : null,
			'card_last_four' => !$paypalAccount ? $paymentMethod->last4 : null,
		])->save();

		return $response->customer;
	}

	/**
	 * Get the default payment method for the customer.
	 *
	 * @return array
	 */
	public function paymentMethod()
	{
		$customer = $this->asBraintreeCustomer();

		foreach ($customer->paymentMethods as $paymentMethod) {
			if ($paymentMethod->isDefault()) {
				return $paymentMethod;
			}
		}
	}

	/**
	 * Get the Braintree customer for the model.
	 *
	 * @return \Braintree\Customer
	 */
	public function asBraintreeCustomer()
	{
		return BraintreeCustomer::find($this->braintree_id);
	}

	/**
	 * Determine if the entity has a Braintree customer ID.
	 *
	 * @return bool
	 */
	public function hasBraintreeId()
	{
		return !is_null($this->braintree_id);
	}

	/**
	 * Make a one off charge to the customer for the given amount.
	 *
	 * @param       $amount
	 * @param array $options
	 * @return \Braintree\Result\Error|\Braintree\Result\Successful
	 * @throws BraintreeException
	 */
	public function charge($amount, array $options = [])
	{
		$customer = $this->asBraintreeCustomer();

		$response = BraintreeTransaction::sale(array_merge([
			'amount'             => (string) round($amount * (1 + ($this->taxPercentage / 100)) , 2),
			'paymentMethodToken' => $this->paymentMethod()->token,
			'options'            => [
				'submitForSettlement'   => true
			],
			'recurring'          => false,
		], $options));

		if (!$response->success) {
			throw new BraintreeException('Braintree was unable to process a charge: ' . $response->message);
		}

		return $response;
	}

	public function setTaxPercentage($taxPercentage)
	{
		$this->taxPercentage = $taxPercentage;
		return $this;
	}

	public function getTaxPercentage()
	{
		return $this->taxPercentage;
	}

}