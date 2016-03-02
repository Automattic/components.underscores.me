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
	 * This unzips our zip from the Github repo.
	 */
	public function unzip_file( $zip_file ) {
		$path = pathinfo( realpath( $zip_file ), PATHINFO_DIRNAME );
		$zip = new ZipArchive;
		$res = $zip->open( $zip_file );
		if ( true === $res  ) {
			// Extract it to the path we determined above.
			$zip->extractTo( $path );
			$zip->close();
		} else {
			die( 'Oh no! I couldn\'t open the zip: ' . $zip_file . '.' );
		}
	}

	/**
	 * This deletes a file.
	 */
	public function delete_file( $URI ) {
		unlink( $URI );
	}
}
new Components_Generator_Plugin;
