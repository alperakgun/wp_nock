wp_nock
=========
WP_Http server mocking  library for PHP/WordPress inspired by the nock JavaScript library.

Usage
----------

You can install wp_nock as a development composer package dependencies.

```
$ composer require --dev alperakgun/wp_nock
```

Require it in your tests/bootstrap.php

```
require dirname( dirname( __FILE__ ) ) . '/vendor/alperakgun/wp_nock/src/class-wp-nock.php';
```

Using PHPUnit, you can test any code which uses 
* WP_Http classes such as wp_remote_get, wp_remote_request,wp_remote_post.
* Or wp_redirect, wp_safe_redirect etc.

```
use Alperakgun\Testing\WP_Nock;
use Alperakgun\Testing\WP_Nock_Exception;

class Test_My_Cool_WordPress_Plugin extends WP_UnitTestCase {

	const TEST_URL = 'https://www.wordpress.com/test';
  
  # Testing wp_remote_get, or wp_remote_post is similar
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

```

Testing the wp_redirect requires a callback

```
	public function test_wp_nock_wp_redirect_with_callback() {
		$nock = new WP_Nock();
		$nock->set_up();

		$nock->redirect(
			self::TEST_URL,
			function( $payload ) {
				$this->assertSame( $payload['location'], self::TEST_URL );
				return false;
			}
		);

		wp_redirect( self::TEST_URL );
		$nock->tear_down();
	}
```

Another way of testing the wp_redirect requires an exception

```
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
  
}
```

Contributing
----------
We welcome contributions. Please don’t hesitate to send a
pull request, send a suggestion, file a bug, or just ask a
question. We promise we’ll be nice. 

For PHP, we use PHP Code Sniffer with the WordPress coding standards.

```
# Run this once at the beginning to set up some test instrumentation.
composer run lint

# Run this whenever you start testing to start Docker containers
composer run lint:fix

```

## Testing

We like tests :) Make sure you run them before starting out!

PHP unit tests can be run with:

```
# Run this once at the very beginning to set up some test instrumentation.
composer run test:setup

# Run this whenever you start testing to start Docker containers
composer run test:run-docker

# Run PHP unit tests
composer run test
```


## We’re Here To Help

We encourage you to ask for help at any point. We want your first
experience with wp_nock to be a good one, so don’t be shy. If you’re
wondering why something is the way it is, or how a decision was made,
you can tag issues with **<span class="label type-question">[Type]
Question</span>** or prefix them with “Question:”.

## License

wp_nock is licensed under [GNU General Public License v2 (or later)](../LICENSE.md).

All materials contributed should be compatible with the GPLv2. This means that if you own the material, you agree to license it under the GPLv2 license. If you are contributing code that is not your own, such as adding a component from another Open Source project, or adding an `npm` package, you need to make sure you follow these steps:

1. Check that the code has a license. If you can't find one, you can try to contact the original author and get permission to use, or ask them to release under a compatible Open Source license.
2. Check the license is compatible with [GPLv2](http://www.gnu.org/licenses/license-list.en.html#GPLCompatibleLicenses), note that the Apache 2.0 license is *not* compatible.
