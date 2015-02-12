<?php

class TinyPNGImageCompressionPlugin extends TinyPNGImageCompressionBase {

	const URL = 'https://api.tinypng.com/shrink';

	private $settings;

	public function __construct() {
		parent::__construct();
		$this->settings = new TinyPNGImageCompressionSettings();
	}

	public function adminInit() {
		load_plugin_textdomain(self::TEXT_DOMAIN, false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}
}