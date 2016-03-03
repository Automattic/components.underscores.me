<?php
/**
 * Plugin Name: Components Generator
 * Description: Generates themes based on Components by Automattic.
 */

class Components_Generator_Plugin {

	function __construct() {
		// Let's run a few init functions to set things up.
		add_action( 'init', array( $this, 'set_expiration_and_go' ) );
	}

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
	}

	/**
	 * Read files to process from base. Stores files on array for processing.
	 */
	public function read_base_dir( $dir ) {
		$files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir ) );
		$filelist = array();
		$exclude = array( '.travis.yml', 'codesniffer.ruleset.xml', 'README.md', 'CONTRIBUTING.md', '.git', '.svn', '.DS_Store', '.gitignore', '.', '..', '.git', '.svn' );
		foreach( $files as $file ) {
			if ( ! in_array( basename( $file ), $exclude )  ) {
				$filelist[] = $file;
			}
		}
		return $filelist;
	}

	/**
	 * This is an init function to grab theme components so we can control when it's called by the generator.
	 */
	public function get_theme_components_init() {
		// Grab theme components from its Github repo.
		$this->get_theme_components( get_stylesheet_directory() . '/build/' );
		$this->read_base_dir( get_stylesheet_directory() . '/build/theme-components-master/' );
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

	/**
	 * Let's set an expiration on the last download and get current time.
	 */
	function set_expiration_and_go() {
		// We only need to grab the file info of one type zip file since all files are created at once.
		$file_name = get_stylesheet_directory() . '/build/' . 'theme-components-master.zip';
		if ( file_exists( $file_name ) ) {
			$file_time_stamp = date( filemtime( $file_name ) );
			$time = time();
			$expired = 1800; /* Equal to 30 minutes. */
		}

		/**
		 * Let's fire the function as late as we can, and every 30 minutes.
		 * No need to fetch theme components all the time.
		 * If no files exist, let's run the init function anyway.
		 */
		if ( ( file_exists( $file_name ) && $expired <= ( $time - $file_time_stamp ) )  || ! file_exists( $file_name ) ) {
			add_action( 'wp_footer', array( $this, 'get_theme_components_init' ) );
		}
	}
}
new Components_Generator_Plugin;
