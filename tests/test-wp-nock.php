<?php

use Alperakgun\Testing\WP_Nock;
use Alperakgun\Testing\WP_Nock_Exception;

class Test_WP_Nock extends WP_UnitTestCase {

	const TEST_URL = 'https://www.wordpress.com/test';

	public function test_wp_nock_available_matches() {
		$nock = new WP_Nock();
		$nock->set_up();

		$this->assertInstanceOf( WP_Nock::class, $nock );

		$this->assertTrue( WP_Nock::matches( 'https://www.wordpress.com/test', 'word' ) !== false );
		$this->assertTrue( WP_Nock::matches( 'https://www.wordpress.com/test', '/wordpres./' ) !== false );

		$nock->tear_down();
	}

	public function test_wp_nock_mock_responses() {
		$nock = new WP_Nock();
		$nock->set_up();

		$this->assertSame( 0, $nock->get_mock_responses_count() );
		$nock->redirect( self::TEST_URL );
		$this->assertSame( 1, $nock->get_mock_responses_count() );

		$mock_responses = $nock->get_mock_responses();
		$this->assertSame( $mock_responses[0]['type'], 'REDIRECT' );
		$this->assertSame( $mock_responses[0]['url'], self::TEST_URL );

		$nock->tear_down();
	}

	public function test_wp_nock_wp_redirect() {
		$nock = new WP_Nock();
		$nock->set_up();

		$nock->redirect( self::TEST_URL );
		try {

			wp_redirect( self::TEST_URL );

		} catch ( WP_Nock_Exception $ex ) {

			$payload = $ex->get_payload();
			$this->assertSame( $payload['location'], self::TEST_URL );
		}

		$nock->tear_down();
	}

	public function test_wp_nock_wp_remote_get() {
		$nock = new WP_Nock();
		$nock->set_up();

		$nock->get(
			self::TEST_URL,
			array(
				'response' => array(
					'code' => 200,
				),
				'body'     => '{"foo":"bar"}',
			)
		);

		$result = wp_remote_get( self::TEST_URL );
		$this->assertSame( $result['response']['code'], 200 );
		$this->assertSame( $result['body'], '{"foo":"bar"}' );

		$nock->tear_down();
	}

	public function test_wp_nock_wp_remote_post() {
		$nock = new WP_Nock();
		$nock->set_up();

		$nock->post(
			self::TEST_URL,
			array(
				'response' => array(
					'code' => 200,
				),
				'body'     => '{"foo":"bar"}',
			)
		);

		$result = wp_remote_post(
			self::TEST_URL,
			array(
				'body' => '{"foo":1}',
			)
		);

		$this->assertSame( $result['response']['code'], 200 );
		$this->assertSame( $result['body'], '{"foo":"bar"}' );

		$nock->tear_down();
	}

}
