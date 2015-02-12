<?php

abstract class TinyPNGImageCompressionBase {
	const TEXT_DOMAIN = 'tinypng-image-compression';

	protected static function translate( $phrase ) {
		return translate( $phrase, self::TEXT_DOMAIN );
	}

	protected static function translateEscape( $phrase ) {
		return htmlspecialchars( self::translate($phrase) );
	}

	public function __construct() {
		add_action( 'init', $this->getMethod( 'init' ) );
		add_action( 'admin_init', $this->getMethod( 'adminInit' ) );
	}

	protected function getMethod( $name ) {
		return array( $this, $name );
	}

	public function init() {
	}

	public function adminInit() {
	}
}