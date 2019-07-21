<?php

use Alperakgun\Testing\WP_Nock;

class Test_WP_Nock extends WP_UnitTestCase {

	public function setUp() {

	}

	public function tearDown() {

	}

	public function test_wp_nock_available() {
		$nock = new WP_Nock();

		$this->assertInstanceOf( WP_Nock::class, $nock );

	}

}
