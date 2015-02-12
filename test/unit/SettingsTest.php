<?php

require_once( dirname(__FILE__) . "/TestCase.php" );

class SettingsTest extends TestCase {

	public function setUp() {
		parent::setUp();
		$this->subject = new TinyPNGImageCompressionSettings();
		$this->wp->adminInit();
	}

	public function testShouldRegisterKeys() {
		$this->assertEquals( array(
				array( 'media', 'tinypng_api_key' ),
				array( 'media', 'tinypng_sizes' ),
		), $this->wp->getCalls( 'register_setting' ) );
	}

	public function testShouldAddSettingsSection() {
		$this->assertEquals( array(
				array( 'tinypng_settings', 'PNG and JPEG compression', array( $this->subject, 'renderSection' ), 'media' ),
		), $this->wp->getCalls( 'add_settings_section' ) );
	}

	public function testShouldAddSettingsField() {
		$this->assertEquals( array(
				array( 'tinypng_api_key', 'TinyPNG API key', array( $this->subject, 'renderApiKey' ), 'media', 'tinypng_settings', array( 'label_for' => 'tinypng_api_key' ) ),
				array( 'tinypng_sizes', 'File compression', array( $this->subject, 'renderSizes' ), 'media', 'tinypng_settings' ),
		), $this->wp->getCalls( 'add_settings_field' ) );
	}

	public function testShouldRenderApiKey() {
		$this->expectOutputString( <<<HEREDOC
<input type="text" id="tinypng_api_key" name="tinypng_api_key" value="" size="40" />
<br/>Visit <a href="https://tinypng.com/developers">TinyPNG Developer section</a>
to gain an API key.
HEREDOC
		);
		$this->subject->renderApiKey();
	}

	public function testShouldRenderApiKeyWithValue() {
		$this->wp->addOption('api_key', 'TEST_KEY');
		$this->expectOutputString( <<<HEREDOC
<input type="text" id="tinypng_api_key" name="tinypng_api_key" value="TEST_KEY" size="40" />
<br/>Visit <a href="https://tinypng.com/developers">TinyPNG Developer section</a>
to gain an API key.
HEREDOC
		);
		$this->subject->renderApiKey();
	}

	public function testShouldRetrieveSizes() {
		$this->wp->stub( 'get_option', array( 'SettingsTest', 'getOption' ) );
		$this->wp->stub( 'get_intermediate_image_sizes', create_function('', 'return array( "thumbnail", "medium", "large", "post-thumbnail", "wrong" );') );
		global $_wp_additional_image_sizes;
		$_wp_additional_image_sizes = array( 'post-thumbnail' => array( 'width' => 825, 'height' => 510 ) );

		$this->assertEquals( array(
				'thumbnail' => array( 'width' => 150, 'height' => 150, 'tinify' => false ),
				'medium' => array( 'width' => 300, 'height' => 300, 'tinify' => false ),
				'large' => array( 'width' => 1024, 'height' => 1024, 'tinify' => false ),
				'post-thumbnail' => array( 'width' => 825, 'height' => 510, 'tinify' => false )
		), $this->subject->getSizes() );
	}

	public function testShouldRenderSizes() {
		$this->expectOutputString( <<<HEREDOC
<p>You can choose to compress different image sizes created by WordPress here.<br/>Remember each additional image size will affect your TinyPNG monthly usage!</p>
<p><input type="checkbox" id="tinypng_sizes_thumbnail" name="tinypng_sizes[thumbnail]"/>
<label for="tinypng_sizes_thumbnail">thumbnail - 150x150</label></p>
<p><input type="checkbox" id="tinypng_sizes_medium" name="tinypng_sizes[medium]"/>
<label for="tinypng_sizes_medium">medium - 300x300</label></p>
<p><input type="checkbox" id="tinypng_sizes_large" name="tinypng_sizes[large]"/>
<label for="tinypng_sizes_large">large - 1024x1024</label></p>

HEREDOC
		);
		$this->subject->renderSizes();
	}

	public function testShouldRenderSizesWithValues() {
		$this->wp->addOption('sizes[medium]', 'on');
		$this->expectOutputString( <<<HEREDOC
<p>You can choose to compress different image sizes created by WordPress here.<br/>Remember each additional image size will affect your TinyPNG monthly usage!</p>
<p><input type="checkbox" id="tinypng_sizes_thumbnail" name="tinypng_sizes[thumbnail]"/>
<label for="tinypng_sizes_thumbnail">thumbnail - 150x150</label></p>
<p><input type="checkbox" id="tinypng_sizes_medium" name="tinypng_sizes[medium]" checked="checked"/>
<label for="tinypng_sizes_medium">medium - 300x300</label></p>
<p><input type="checkbox" id="tinypng_sizes_large" name="tinypng_sizes[large]"/>
<label for="tinypng_sizes_large">large - 1024x1024</label></p>

HEREDOC
		);
		$this->subject->renderSizes();
	}
}