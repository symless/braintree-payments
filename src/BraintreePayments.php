<?php

namespace Symless\BraintreePayments;

class BraintreePayments
{
	protected static $currency = 'usd';

	protected static $currencySymbol = '$';

	protected static $formatCurrencyUsing;

	public static function useCurrency($currency, $symbol = null)
	{
		static::$currency = $currency;
		static::useCurrencySymbol($symbol ? : static::guessCurrencySymbol($currency));
	}

	public static function usesCurrency()
	{
		return static::$currency;
	}

	public static function useCurrencySymbol($symbol)
	{
		static::$currencySymbol = $symbol;
	}

	public static function usesCurrencySymbol()
	{
		return static::$currencySymbol;
	}

	protected static function guessCurrencySymbol($currency)
	{
		switch (strtolower($currency)) {
			case 'usd':
			case 'aud':
			case 'cad':
				return '$';
			case 'eur':
				return '€';
			case 'gbp':
				return '£';
			default:
				throw new \Exception('Unable to guess the symbol for this currency.');
		}
	}

	public static function formatCurrencyUsing(callable $callback)
	{
		static::$formatCurrencyUsing = $callback;
	}

	public static function formatAmount($amount)
	{
		if (static::$formatCurrencyUsing) {
			return call_user_func(static::$formatCurrencyUsing, $amount);
		}

		$amount = number_format($amount, 2);

		if (Str::startsWith($amount, '-')) {
			return '-' . static::usesCurrencySymbol() . ltrim($amount, '-');
		}

		return static::usesCurrencySymbol() . $amount;
	}
}