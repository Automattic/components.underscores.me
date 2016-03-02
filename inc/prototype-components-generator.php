<?php
/**
 * Plugin Name: Components Generator
 * Description: Generates themes based on Components by Automattic.
 */

class Components_Generator_Plugin {

	/**
	 * Places data in JSON files in an array for later use.
	 */
	function parse_config( $file ) {
		$json = file_get_contents( $file );
		$data = json_decode( $json, TRUE );
	}
}
new Components_Generator_Plugin;
