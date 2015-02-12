<?php

class WordPressOptions {
	private $values;

	public function __construct() {
		 $this->values = array(
			'thumbnail_size_w' => 150,
			'thumbnail_size_h' => 150,
			'medium_size_w' => 300,
			'medium_size_h' => 300,
			'large_size_w' => 1024,
			'large_size_h' => 1024,
		);
	}

	public function set( $key, $value ) {
		if( preg_match( '#^(.+)\[(\w+)\]$#', $key, $match ) ) {
			if( !isset( $this->values[$match[1]] ) ) {
				$this->values[$match[1]] = array();
			}
			$this->values[$match[1]][$match[2]] = $value;
		} else {
			$this->values[$key] = $value;
		}
	}

	public function get( $key, $default=null ) {
		return isset( $this->values[$key] ) ? $this->values[$key] : $default;
	}
}

class WordPressStubs {
	private $initFunctions;
	private $adminInitFunctions;
	private $options;
	private $calls;
	private $stubs;

	public function __construct() {
		$this->addMethod( 'add_action' );
		$this->addMethod( 'register_setting' );
		$this->addMethod( 'add_settings_section' );
		$this->addMethod( 'add_settings_field' );
		$this->addMethod( 'get_option' );
		$this->addMethod( 'get_intermediate_image_sizes' );
		$this->addMethod( 'translate' );
		$this->defaults();
	}

	public function defaults() {
		$this->initFunctions = array();
		$this->adminInitFunctions = array();
		$this->options = new WordPressOptions();
	}

	public function addOption($key, $value) {
		$this->options->set( "tinypng_$key", $value );
	}

	public function clear() {
		$this->defaults();
		foreach( array_keys($this->calls) as $method ) {
			$this->calls[$method] = array();
			$this->stubs[$method] = array();
		}
	}

	public function call( $method, $args ) {
		$this->calls[$method][] = $args;
		if( 'add_action' === $method ) {
			if( 'init' === $args[0] ) {
				$this->initFunctions[] = $args[1];
			} elseif( 'admin_init' === $args[0] ) {
				$this->adminInitFunctions[] = $args[1];
			}
		}
		if( 'translate' === $method ) {
			return $args[0];
		} elseif( 'get_option' === $method ) {
			return call_user_func_array( array( $this->options, 'get' ), $args );
		} elseif( $this->stubs[$method] ) {
			return call_user_func_array( $this->stubs[$method], $args );
		} elseif( 'get_intermediate_image_sizes' == $method ) {
			return array( 'thumbnail', 'medium', 'large' );
		}
	}

	public function addMethod( $method ) {
		$this->calls[$method] = array();
		$this->stubs[$method] = array();
		eval( "function $method() { return \$GLOBALS['wp']->call( '$method', func_get_args() ); }" );
	}

	public function getCalls( $method ) {
		return $this->calls[$method];
	}

	public function init() {
		foreach( $this->initFunctions as $func ) {
			call_user_func($func);
		}
	}

	public function adminInit() {
		foreach( $this->adminInitFunctions as $func ) {
			call_user_func($func);
		}
	}

	public function stub( $method, $func ) {
		$this->stubs[$method] = $func;
	}
}


$GLOBALS['wp'] = new WordPressStubs();
