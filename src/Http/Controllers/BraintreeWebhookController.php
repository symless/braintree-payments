<?php

namespace Symless\BraintreePayments\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Braintree\WebhookNotification;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

class BraintreeWebhookController extends Controller
{
	/**
	 * Handle a Braintree webhook call.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function handleWebhook(Request $request)
	{
		try {
			$webhook = $this->parseBraintreeNotification($request);
		} catch (Exception $ex) {
			return;
		}

		$method = 'handle' . studly_case(str_replace('.', '_', $webhook->kind));

		if (method_exists($this, $method)) {
			return $this->{$method}($webhook);
		}

		return $this->missingMethod();
	}

	/**
	 * Parse the given Braintree webhook notification request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Braintree\WebhookNotification
	 */
	protected function parseBraintreeNotification($request)
	{
		return WebhookNotification::parse($request->bt_signature, $request->bt_payload);
	}

	/**
	 * Handle calls to missing methods on the controller.
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	protected function missingMethod()
	{
		return new Response();
	}
}