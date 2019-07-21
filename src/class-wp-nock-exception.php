<?php
/**
 * A custom exception class for wp_nock
 *
 * @package wp_nock
 * @since 0.0.1
 */

namespace Alperakgun\Testing;
/**
 * A custom nock exception class used for wp_direct filtering
 *
 * @since 0.0.1
 */
class WP_Nock_Exception extends \Exception {

	/**
	 * The payload data
	 *
	 * @var Array
	 */
	protected $payload;

	/**
	 * Constructor for WP_Nock_Exception
	 *
	 * Example: new WP_Nock_Exception( 'My message', array() );
	 *
	 * @param String    $message The exception message.
	 * @param Array     $data The exception payload.
	 * @param Integer   $code The exception code.
	 * @param Exception $previous  The previous exception.
	 */
	public function __construct( $message, $data, $code = 0, $previous = null ) {
		$this->payload = $data;
		parent::__construct( $message, $code, $previous );
	}

	/**
	 * Get the payload array set when exception thrown
	 *
	 * @return Array $payload
	 */
	public function get_payload() {
		return $this->payload;
	}
}

