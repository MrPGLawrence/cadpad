<?php
namespace MangoPay\WPPlugin;
/**
 * Same as storage strategy implementation for tests
 */
class MockStorage implements \MangoPay\Libraries\IStorageStrategy {

	private static $_oAuthToken = null;

	/**
	 * Gets the current authorization token.
	 * @return \MangoPay\Libraries\OAuthToken Currently stored token instance or null.
	 */
	public function Get() {
		return self::$_oAuthToken;
	}
	/**
	 * Stores authorization token passed as an argument.
	 * @param \MangoPay\Libraries\OAuthToken $token Token instance to be stored.
	 */
	public function Store($token) {
		self::$_oAuthToken = $token;
	}
}