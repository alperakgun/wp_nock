<?php
/**
 * A WP_Http server mocking library for PHP/WordPress
 *
 * @package wp_nock
 * @since 0.0.1
 */

namespace Alperakgun\Testing;
require( 'class-wp-nock-exception.php' );

/**
 * WP_Nock class stubs the WP_Http class requests and redirects
 *
 * @since 0.0.1
 */
class WP_Nock {

	/**
	 * List of all responses added during testing
	 *
	 * @var Array
	 */
	protected $mock_responses;

	/**
	 * Adds filters before testing
	 */
	public function set_up() {

		add_filter( 'wp_redirect', array( $this, 'redirect_spy' ) );
		add_filter( 'pre_http_request', array( $this, 'http_stub' ), 10, 3 );

		$this->mock_responses = array();

	}

	/**
	 * Cleanups filters after testing
	 */
	public function tear_down() {

		remove_filter( 'wp_redirect', array( $this, 'redirect_spy' ) );
		remove_filter( 'pre_http_request', array( $this, 'http_stub' ) );

	}

	/**
	 * Compares string or regex against string
	 *
	 * @param String $string The source string.
	 * @param String $regex_or_string What has to be searched in the source.
	 * @return Boolean $match  Matches or not.
	 */
	private static function matches( $string, $regex_or_string ) {

		if ( preg_match( '/^\/[\s\S]+\/$/', $regex_or_string ) ) { // valid regex?
			return preg_match( $regex_or_string, $string );
		} else { // ordinary string?
			return strpos( $string, $regex_or_string ) !== false;
		}

	}

	/**
	 * A callback for the wp_redirect which throws an Exception
	 *
	 * @param String $location the target url.
	 * @param String $status HTTP status code.
	 * @param String $x_redirect_by What caused the redirect.
	 * @throws WP_Nock_Exception  An exception with a payload.
	 * @return String $location  The not modified location.
	 */
	public function redirect_spy( $location, $status = 302, $x_redirect_by = 'WordPress' ) {

		foreach ( $this->mock_responses as $i => $mock ) {
			if ( 'REDIRECT' == $mock['type'] && self::matches( $location, $mock['url'] ) ) {
				unset( $this->mock_responses[ $i ] );
				$payload = array( 'location' => $location );
				throw new WP_Nock_Exception( 'WP Nock Redirect Spy Exception', $payload );
			}
		}

		return $location;

	}

	/**
	 * Get the mock response array set during testing
	 *
	 * @return Array $payload
	 */
	public function get_mock_responses() {

		return $this->mock_responses;

	}

	/**
	 * Get the number of items in the mock response array
	 *
	 * @return Integer $count Number of items in the mock response list
	 */
	public function get_mock_responses_count() {

		return count( $this->mock_responses );

	}

	/**
	 * Add a stub request to intercept
	 *
	 * @param String $url the target url to intercept String or Regex.
	 * @param String $type HTTP Verb like GET, POST or REDIRECT.
	 * @param Array  $reply The reply array in response to the request.
	 * @return Boolean $result True for success.
	 */
	public function request( $url, $type, $reply = array() ) {

		$this->mock_responses[] = array(
			'type'  => $type,
			'url'   => $url,
			'reply' => $reply,
		);

		return true;
	}

	/**
	 * Add a GET stub request to intercept
	 *
	 * @param String $url the target url to intercept String or Regex.
	 * @param Array  $reply The reply array in response to the request.
	 * @return Boolean $result True for success.
	 */
	public function get( $url, $reply = array( 'response' => array( 'code' => 200 ) ) ) {

		return $this->request( $url, 'GET', $reply );

	}

	/**
	 * Add a POST stub request to intercept
	 *
	 * @param String $url the target url to intercept String or Regex.
	 * @param Array  $reply The reply array in response to the request.
	 * @return Boolean $result True for success.
	 */
	public function post( $url, $reply = array( 'response' => array( 'code' => 200 ) ) ) {

		return $this->request( $url, 'POST', $reply );

	}

	/**
	 * Add a HEAD stub request to intercept
	 *
	 * @param String $url the target url to intercept String or Regex.
	 * @param Array  $reply The reply array in response to the request.
	 * @return Boolean $result True for success.
	 */
	public function head( $url, $reply = array( 'response' => array( 'code' => 200 ) ) ) {

		return $this->request( $url, 'HEAD', $reply );

	}

	/**
	 * Add a redirect URL stub request to intercept and raise an exception
	 *
	 * @param String $url the target url to intercept String or Regex.
	 * @return Boolean $result True for success.
	 */
	public function redirect( $url ) {

		return $this->request( $url, 'REDIRECT' );

	}

	/**
	 * A callback for the pre_http_request  which throws an Exception
	 *
	 * @param Boolean $preempt Preempt value.
	 * @param Object  $request HTTP Request object.
	 * @param String  $url the target url.
	 * @return Array $response  A response or true for blocking all other requests.
	 */
	public function http_stub( $preempt, $request, $url ) {

		foreach ( $this->mock_responses as $i => $mock ) {
			if ( $request['method'] == $mock['type'] && self::matches( $url, $mock['url'] ) ) {

				unset( $this->mock_responses[ $i ] );

				return $mock['reply'];
			}
		}
		return true;

	}

}

