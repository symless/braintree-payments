<?php

namespace Symless\BraintreePayments\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Laravel\Cashier\Subscription;
use Braintree\WebhookNotification;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

class BraintreeWebhookController extends Controller
{
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

	protected function parseBraintreeNotification($request)
	{
		return WebhookNotification::parse($request->bt_signature, $request->bt_payload);
	}

	protected function handleSubscriptionCancelled($webhook)
	{
		return $this->cancelSubscription($webhook->subscription->id);
	}

	protected function handleSubscriptionExpired($webhook)
	{
		return $this->cancelSubscription($webhook->subscription->id);
	}

	protected function cancelSubscription($subscriptionId)
	{
		$subscription = $this->getSubscriptionById($subscriptionId);

		if ($subscription && (!$subscription->cancelled() || $subscription->onGracePeriod())) {
			$subscription->markAsCancelled();
		}

		return new Response('Webhook Handled', 200);
	}

	protected function getSubscriptionById($subscriptionId)
	{
		return Subscription::where('braintree_id', $subscriptionId)->first();
	}

	protected function missingMethod($params = [])
	{
		return new Response();
	}
}