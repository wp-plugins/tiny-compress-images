<?php

class TinyPNGImageCompressionSettings extends TinyPNGImageCompressionBase {

	const PREFIX = 'tinypng_';

	protected static function getPrefixedName( $name ) {
		return self::PREFIX . $name;
	}

	private $sizes;

	public function adminInit() {
		$section = self::getPrefixedName( 'settings' );
		add_settings_section( $section, self::translate('PNG and JPEG compression'), $this->getMethod( 'renderSection' ), 'media' );

		$field = self::getPrefixedName( 'api_key' );
		register_setting( 'media', $field );
		add_settings_field( $field, self::translate('TinyPNG API key'), $this->getMethod( 'renderApiKey' ), 'media', $section, array( 'label_for' => $field ) );

		$field = self::getPrefixedName( 'sizes' );
		register_setting( 'media', $field );
		add_settings_field( $field, self::translate('File compression'), $this->getMethod( 'renderSizes' ), 'media', $section );
	}

	public function getSizes() {
		if (is_array($this->sizes)) {
			return $this->sizes;
		}

		$this->sizes = array();
		$setting = get_option( self::getPrefixedName('sizes') );

		global $_wp_additional_image_sizes;
		foreach ( get_intermediate_image_sizes() as $size ) {
			$width = get_option( $size . '_size_w', isset( $_wp_additional_image_sizes[$size] ) ? $_wp_additional_image_sizes[$size]['width'] : null );
			$height = get_option( $size . '_size_h', isset( $_wp_additional_image_sizes[$size] ) ? $_wp_additional_image_sizes[$size]['height'] : null );
			if ($width && $height) {
				$this->sizes[$size] = array(
					'width' => $width, 'height' => $height,
					'tinify' => isset($setting[$size]) && $setting[$size] == 'on',
				);
			}
		}
		return $this->sizes;
	}

	public function renderSection() {
	}

	public function renderApiKey() {
		$field = self::getPrefixedName( 'api_key' );
		$value = get_option( $field, '' );
?>
<input type="text" id="<?php echo $field; ?>" name="<?php echo $field; ?>" value="<?php echo htmlspecialchars($value); ?>" size="40" />
<br/><?php echo self::translateEscape('Visit') . ' '; ?>
<a href="https://tinypng.com/developers"><?php echo self::translateEscape('TinyPNG Developer section'); ?></a>
<?php echo self::translateEscape('to gain an API key') . '.';
	}

	public function renderSizes() {
		echo '<p>' . self::translateEscape( 'You can choose to compress different image sizes created by WordPress here' ) . '.<br/>';
		echo self::translateEscape( 'Remember each additional image size will affect your TinyPNG monthly usage' ) . "!</p>\n";
		foreach( $this->getSizes() as $size => $option ) {
			$id = self::getPrefixedName( "sizes_$size" );
			$field = self::getPrefixedName( "sizes[$size]" );
?>
<p><input type="checkbox" id="<?php echo $id; ?>" name="<?php echo $field ?>"<?php if ( $option['tinify'] == 'on' ) { echo ' checked="checked"'; } ?>/>
<label for="<?php echo $id; ?>"><?php echo $size . " - ${option['width']}x${option['height']}"; ?></label></p>
<?php
		}
	}

}