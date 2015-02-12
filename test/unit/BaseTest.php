<?php

require_once( dirname(__FILE__) . "/TestCase.php" );

class Base extends TinyPNGImageCompressionBase {
}

class BaseTest extends TestCase {

	public function setUp() {
		parent::setUp();
			$this->subject = new Base();
	}

	public function testShouldAddInitHooks() {
		$this->assertEquals( array(
				array( 'init', array( $this->subject, 'init' ) ),
				array( 'admin_init', array( $this->subject, 'adminInit' ) )
			),
			$this->wp->getCalls('add_action')
		);
	}
}