<?php

require( dirname(__FILE__) . '/../helpers/WordPressStubs.php' );
require( dirname(__FILE__) . '/../../src/Base.php' );
require( dirname(__FILE__) . '/../../src/Settings.php' );
require( dirname(__FILE__) . '/../../src/Plugin.php' );

class TestCase extends PHPUnit_Framework_TestCase {

	function setUp() {
		$this->wp = $GLOBALS['wp'];
	}

	function tearDown() {
		$this->wp->clear();
	}
}