<?php
/**
 * Plugin Name: Components Generator
 * Description: Generates themes based on Components by Automattic.
 */

class Components_Generator_Plugin {

	var $build_dir = 'build/';
	var $repo_url = 'https://codeload.github.com/Automattic/theme-components/zip/master';
	var $repo_file_name = 'theme-components-master.zip';
	var $components_dir;

	function __construct() {
		// Initialize class properties
		$this->build_dir = sprintf( '%s/%s', get_stylesheet_directory(), $this->build_dir );
		$this->repo_url = esc_url_raw( $this->repo_url );
		$this->components_dir = $this->build_dir . str_replace( '.zip', '', $this->repo_file_name );

		// Patch repo url and filename to work with `branchless-merge` branch
		// TODO: remove this code after the branch is merged.
		$this->repo_url = preg_replace( '%/master$%', '/branchless-merge', $this->repo_url );
		$this->repo_file_name = preg_replace( '%-master.zip$%', '-branchless-merge.zip', $this->repo_file_name );
		$this->components_dir = preg_replace( '%-master$%', '-branchless-merge', $this->components_dir );

		// Let's run a few init functions to set things up.
		add_action( 'init', array( $this, 'set_expiration_and_go' ) );
	}

	/**
	 * This is an init function to grab theme components so we can control when it's called by the generator.
	 */
	public function get_theme_components_init() {
		// Grab theme components from its Github repo.
		$this->get_theme_components( $this->build_dir );
	}

	/**
	 * Places data in JSON files in an array for later use.
	 */
	public function parse_config( $file ) {
		$json = file_get_contents( $file );
		return json_decode( $json, TRUE );
	}

	/**
	 * Builds a given type from theme components.
	 */
	public function build_type( $type ) {
		// The target directory where we will be working on.
		$target_dir = $this->build_dir . $type;
		
		// Get type config
		$config_path = sprintf( '%s/configs/type-%s.json', $this->components_dir, $type );
		$config = $this->parse_config( $config_path );

		// Create target directory if it doesn't exist
		if ( ! file_exists( $target_dir ) && ! is_dir( $target_dir ) ) {
			mkdir( $target_dir,  0755 );
		}

		// Copy the build files so we can work with them.
		$this->copy_build_files( $this->components_dir, $target_dir );

		// Handle config
		$this->handle_config( $config, $target_dir );
	}

	/**
	 * This gets our zip from the Github repo.
	 */
	public function get_theme_components( $destination ) {
		if ( ! file_exists( $this->build_dir ) && ! is_dir( $this->build_dir ) ) {
			mkdir( $this->build_dir,  0755 );
		}
		// Get our download.
		$this->download_file( $this->repo_url, $this->repo_file_name );
		// Copy the file to its new directory.
		copy( ABSPATH . $this->repo_file_name, sprintf( '%s/%s', $destination, $this->repo_file_name ) );
		// Unzip the file.
		$this->unzip_file( sprintf( '%s/%s', $destination, $this->repo_file_name ) );
		// Delete the unneeded files. Original download in root.
		$this->delete_file( ABSPATH . $this->repo_file_name );
	}

	/**
	 * Read files to process from base. Stores files on array for processing.
	 */
	public function read_base_dir( $dir ) {
		$files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir ) );
		$filelist = array();
		$exclude = array( '.travis.yml', 'codesniffer.ruleset.xml', 'README.md', 'CONTRIBUTING.md', '.git', '.svn', '.DS_Store', '.gitignore', '.', '..' );
		foreach( $files as $file ) {
			if ( ! in_array( basename( $file ), $exclude )  ) {
				$filelist[] = $file;
			}
		}
		return $filelist;
	}

	/**
	 * Handles the configuration and coordinates everything.
	 */
	public function handle_config( $config, $target_dir ) {
		foreach ( $config as $section => $args ) {
			switch ( $section ) {
				case 'replacement_files';
					$this->add_replacement_files( $args, $target_dir );
					break;
				case 'sass_replace';
					$this->add_sass_includes( $args, $target_dir );
					break;
				case 'components';
					$this->add_component_files( $args, $target_dir );
					break;
				case 'templates';
					$this->add_templates( $args, $target_dir );
					break;
				case 'js';
					$this->add_javascript( $args, $target_dir );
					break;
			}
		}
	}

	/**
	 * Adds component files needed for the build.
	 */
	public function add_component_files( $files, $target_dir ) {

	}

	/**
	 * Replaces files in the build from those specified by type.
	 */
	public function add_replacement_files( $files, $target_dir ) {

	}

	/**
	 * Adds sass includes to the build and takes care of file overrides.
	 */
	public function add_sass_includes( $files, $target_dir ) {

	}

	/**
	 * Adds templates to the build.
	 */
	public function add_templates( $files, $target_dir ) {

	}

	/**
	 * Removes component insertion comments from source.
	 */
	public function add_javascript( $files, $target_dir ) {

	}

	/**
	 * Replaces component insertion comments with the actual component code.
	 */
	public function insert_components( $components, $source ) {

	}

	/**
	 * Removes component insertion comments from source.
	 */
	public function cleanup_template_source( $source ) {

	}

	// Utility functions: These help the generator do its work.

	/**
	 * Copy files to temporary build directory.
	 */
	public function copy_build_files( $source_dir, $target_dir ) {
		if ( ! is_dir( $source_dir ) ) {
			return;
		}
		$dir = opendir( $source_dir );
		@mkdir( $target_dir );
		while( false !== ( $file = readdir( $dir ) ) ) {
			if ( ( $file != '.' ) && ( $file != '..' ) ) {
				if ( is_dir( $source_dir . '/' . $file ) ) {
					$this->copy_build_files( $source_dir . '/' . $file, $target_dir . '/' . $file );
				} else {
					copy( $source_dir . '/' . $file, $target_dir . '/' . $file );
				}
			}
		}
		closedir( $dir );
	}

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
		$file_name = $this->build_dir . $this->repo_file_name;
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

if ( ! is_admin() ) {
	new Components_Generator_Plugin;
}