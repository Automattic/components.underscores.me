<?php
/**
 * Plugin Name: Components Generator
 * Description: Generates themes based on Components by Automattic.
 */

class Components_Generator_Plugin {

	/**
	 * Places data in JSON files in an array for later use.
	 */
	public function parse_config( $file ) {
		$json = file_get_contents( $file );
		return json_decode( $json, TRUE );
	}

	// Utility functions: These help the generator do its work.
	/**
	 * This downloads a file at a URL.
	 */
	public function download_file( $URI, $file_name ) {
		$fp = fopen( $file_name, 'w' );
		$ch = curl_init( $URI );
		curl_setopt( $ch, CURLOPT_FILE, $fp );
		$data = curl_exec( $ch );
		curl_close( $ch );
		fclose( $fp );
	}

	/**
	 * This deletes a file.
	 */
	public function delete_file( $URI ) {
		unlink( $URI );
	}
}
new Components_Generator_Plugin;
