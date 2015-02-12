<?php
/**
 * Plugin Name: TinyPNG image compression
 * Plugin URI: http://tinypng.com
 * Description: Compressing PNG and JPG images using the TinyPNG web service.
 * Version: 0.1.0
 * Author: TinyPNG
 * Author URI: http://voormedia.com/about
 * License: GPLv2 or later
 */

require( dirname( __FILE__ ) . '/Base.php' );
require( dirname( __FILE__ ) . '/Settings.php' );
require( dirname( __FILE__ ) . '/Plugin.php' );

$tinyPngImageCompressionPlugin = new TinyPNGImageCompressionPlugin();