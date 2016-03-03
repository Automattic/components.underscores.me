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

	/**
	 * This gets our zip from the Github repo.
	 */
	public function get_theme_components( $destination ) {
		// Our Github repo zip URL.
		$repo_url = esc_url_raw( 'https://codeload.github.com/Automattic/theme-components/zip/master' );
		// Repo file name.
		$repo_file_name = 'theme-components-master.zip';
		// Our build directory.
		$build_dir = get_stylesheet_directory() . '/build/';
		if ( ! file_exists( $build_dir ) && ! is_dir( $build_dir ) ) {
			mkdir( $build_dir,  0755 );
		}
		// Get our download.
		$this->download_file( $repo_url, $repo_file_name );
		// Copy the file to its new directory.
		copy( ABSPATH . $repo_file_name, $destination . $repo_file_name );
		// Unzip the file.
		$this->unzip_file( $destination . $repo_file_name );
		// Delete the unneeded files.
		$this->delete_file( ABSPATH . $repo_file_name ); // Original download in root.
		$this->delete_file( $destination . $repo_file_name ); // Zip file, after it's moved and unzipped.
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
