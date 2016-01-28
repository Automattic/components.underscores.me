<?php
/**
 * Plugin Name: Components Generator
 * Description: Generates themes based on the theme pattern library by Automattic.
 * Much of this code is from the original Underscores generator:
 * https://github.com/Automattic/underscores.me/blob/master/plugins/underscoresme-generator/underscoresme-generator.php
 */

/**
 * This file adds functionality to make the different theme pattern types.
 */
class Components_Generator_Plugin {

	protected $theme;
	private $selected_theme, $prototype_dir, $type_branch;
	static protected $theme_types = array(), $file_data = array();

	function __construct() {
		/*
		 * This contains all the data for our file structure.
		 *
		 */
		// Server info
		self::$file_data = array(
			'server' => array (
				'root'	=> $_SERVER[ 'DOCUMENT_ROOT' ] . '/',
				'download_dir' => $_SERVER[ 'DOCUMENT_ROOT' ] . '/downloads/',
				'prototype_dir' => $_SERVER[ 'DOCUMENT_ROOT' ] . '/prototype/',
			),

			// Github repository info
			'remote' => array (
				'repo'	=> 'theme-pattern-library',
				'download_url' => 'https://codeload.github.com/Automattic/theme-pattern-library/zip/',
			),
		);

		/*
		 * This contains all the data for our different types.
		 * If a new type is added to Components, add the data here.
		 * The generator will then work with the new type.
		 */
		 self::$theme_types = array(
 			'base' => array (
 				'title'	=> esc_html__( 'Base', 'components' ),
 				'id' => esc_attr( 'type-base' ),
 				'zip_file' => self::$file_data['remote']['repo'] . '-master.zip',
 				'branch' => 'master',
 				'branch_slash' => false,
 				'prototype_dir' => self::$file_data['remote']['repo'] . '-master',
 			),

 			'modern' => array (
 				'title'	=> esc_html__( 'Modern Blog', 'components' ),
 				'id' => esc_attr( 'type-blog-modern' ),
 				'zip_file' => self::$file_data['remote']['repo'] . '-types-blog-modern.zip',
 				'branch' => 'types/blog-modern',
 				'branch_slash' => true,
 				'prototype_dir' => self::$file_data['remote']['repo'] . '-types-blog-modern',
 			),

 			'classic' => array (
 				'title'	=> esc_html__( 'Classic Blog', 'components' ),
 				'id' => esc_attr( 'type-classic' ),
 				'zip_file' => self::$file_data['remote']['repo'] . '-types-blog-traditional.zip',
 				'branch' => 'types/blog-traditional',
 				'branch_slash' => true,
 				'prototype_dir' => self::$file_data['remote']['repo'] . '-types-blog-traditional',
 			),

 			'magazine' => array (
 				'title'	=> esc_html__( 'Magazine', 'components' ),
 				'id' => esc_attr__( 'type-magazine', 'components' ),
 				'zip_file' => self::$file_data['remote']['repo'] . '-types-magazine.zip',
 				'branch' => 'types/magazine',
 				'branch_slash' => true,
 				'prototype_dir' => self::$file_data['remote']['repo'] . '-types-magazine',
 			),

 			'portfolio' => array (
 				'title'	=> esc_html__( 'Portfolio', 'components' ),
 				'id' => esc_attr( 'type-portfolio' ),
 				'zip_file' => self::$file_data['remote']['repo'] . '-types-portfolio.zip',
 				'branch' => 'types/portfolio',
 				'branch_slash' => true,
 				'prototype_dir' => self::$file_data['remote']['repo'] . '-types-portfolio',
 			),

 			'business' => array (
 				'title'	=> esc_html__( 'Business', 'components' ),
 				'id' => esc_attr( 'type-business' ),
 				'zip_file' => self::$file_data['remote']['repo'] . '-types-business.zip',
 				'branch' => 'types/business',
 				'branch_slash' => true,
 				'prototype_dir' => self::$file_data['remote']['repo'] . '-types-business',
 			),
 		);

		// All the black magic is happening in these actions.
		add_action( 'init', array( $this, 'components_generator_set_expiration_and_go' ) );
		add_action( 'init', array( $this, 'components_generator_zippity_zip' ) );
		add_filter( 'components_generator_file_contents', array( $this, 'components_generator_do_replacements' ), 10, 2 );
		// Use do_action( 'components_generator_print_form' ); in your theme to render the form.
		add_action( 'components_generator_print_form', array( $this, 'components_generator_print_form' ) );
	}

	/*
	 * Sets theme selected when form is submitted.
	 */
	public function set_theme( $the_theme ) {
		$this->selected_theme = $the_theme;
	}

	/*
	 * Sets prototype directory when form is submitted, so proper theme files are generated.
	 */
	public function set_prototype_dir( $dir ) {
		$this->prototype_dir = self::$file_data['server']['prototype_dir'] . $dir;
	}

	/*
	 * Sets branch name when form is submitted, so proper theme is downloaded.
	 */
	public function set_type_branch( $the_branch ) {
		$this->type_branch = $the_branch;
	}

	/**
	 * This downloads a file at a URL.
	 */
	function components_generator_download_file( $URI, $file_name ) {
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
	function components_generator_delete_file( $URI ) {
		unlink( $URI );
	}

	/**
	 * This unzips our zip from the Github repo.
	 */
	function components_generator_unzip( $zip_file ) {
		$path = pathinfo( realpath( $zip_file ), PATHINFO_DIRNAME );
		$zip = new ZipArchive;
		$res = $zip->open( $zip_file );
		if ( $res === TRUE ) {
			// Extract it to the path we determined above.
			$zip->extractTo( $path );
			$zip->close();
		} else {
			exit( 'Oh no! I couldn\'t open the zip: ' . $zip_file . '.' );
		}
	}

	/**
	 * This gets our zip from the Github repo.
	 */
	function components_generator_get_download( $branch, $destination, $branch_slash ) {
		// Our file name.
		$repo_file_name = self::$file_data['remote']['repo'] . '-' . $branch . '.zip';
		// Grab the file.
		// Github changes forward slashes to dashes to file names, so we account for that.
		if ( $branch_slash == true ) {
			$this->components_generator_download_file( esc_url_raw( self::$file_data['remote']['download_url'] . $branch ), str_replace( '/', '-', $repo_file_name ) );
		} else {
			$this->components_generator_download_file( esc_url_raw( self::$file_data['remote']['download_url'] . $branch ), $repo_file_name );
		}
		if ( ! file_exists( 'downloads' ) && ! is_dir( 'downloads' ) ) {
			mkdir( self::$file_data['server']['root'] . '/downloads/',  0755 );
		}
		if ( ! file_exists( 'prototype' ) && ! is_dir( 'prototype' ) ) {
			mkdir( self::$file_data['server']['root'] . '/prototype/',  0755 );
		}
		// Copy the file to its new directory.
		if ( $branch_slash == true ) {
			copy( self::$file_data['server']['root'] . str_replace( '/', '-', $repo_file_name ), $destination . str_replace( '/', '-', $repo_file_name ) );
		} else {
			copy( self::$file_data['server']['root'] . $repo_file_name, $destination . $repo_file_name );
		}
	}

	/**
	 * Renders the generator form
	 */
	function components_generator_print_form() { ?>
		<section id="generator">

			<div class="gear-set-one">
				<?php echo file_get_contents( esc_url( get_template_directory_uri() ) . '/assets/img/gear-set.svg' ); ?>
			</div><!-- .gear-set-one -->

			<div class="gear-set-two">
				<?php echo file_get_contents( esc_url( get_template_directory_uri() ) . '/assets/img/gear-set.svg' ); ?>
			</div><!-- .gear-set-two -->

			<div class="wrap">
				<h2>Build your own Components theme</h2>
				<p>Pick a type, fill out the information about your new theme, and download it.</p>
				<div id="generator-form" class="generator-form">
					<form method="POST">
						<input type="hidden" name="components_generate" value="1" />

						<div class="theme-input clear">
							<div class="generator-form-primary">
								<fieldset>
									<legend class="components-label">Theme type<span class="required">*<span class="screen-reader-text">Required</span></span></legend>
									<div class="components-radio-block">
										<?php
											$i = 0;
											foreach ( self::$theme_types as $theme_type ) :
												// Check our first radio as a default.
												if ( $i == 0 ) {
													$checked = 'checked="checked"';
												} else {
													$checked = null;
												}
												$i++;
										?>
											<div class="components-radio-group">
												<input id="<?php echo $theme_type['id']; ?>" class="components-input" type="radio" name="theme-type" value="<?php echo $theme_type['title']; ?>" <?php echo $checked; ?> required aria-required="true">
												<label class="components-label" for="<?php echo $theme_type['id']; ?>"><?php echo $theme_type['title']; ?></label>
											</div>
										<?php endforeach; ?>
									</div>
								</fieldset>
							</div><!-- .generator-form-primary -->

							<div class="generator-form-secondary">
								<div class="components-form-field">
									<label class="components-label" for="components-name">Theme Name<span class="required">*<span class="screen-reader-text">Required</span></span></label>
									<input type="text" id="components-name" class="components-input" name="components_name" placeholder="Awesome Theme" required="" aria-required="true">
								</div>

								<div class="components-form-field">
									<label class="components-label" for="components-slug">Theme Slug</label>
									<input type="text" id="components-slug" class="components-input" name="components_slug" placeholder="awesome-theme">
								</div>

								<div class="components-form-field">
									<label class="components-label" for="components-author">Author Name</label>
									<input type="text" id="components-author" class="components-input" name="components_author" placeholder="Your Name">
								</div>

								<div class="components-form-field">
									<label class="components-label" for="components-author-uri">Author URI</label>
									<input type="url" id="components-author-uri" class="components-input" name="components_author_uri" placeholder="http://themeshaper.com/">
								</div>

								<div class="components-form-field">
									<label class="components-label" for="components-description">Theme description</label>
									<input type="text" id="components-description" class="components-input" name="components_description" placeholder="A brief description of your awesome theme">
								</div>

								<div class="generator-form-submit">
									<input type="submit" name="components_generate_submit" value="Download Theme">
									<button type="button" class="components-form-cancel">Cancel</button>
								</div><!-- .generator-form-submit -->
							</div><!-- .generator-form-secondary -->
						</div><!-- .generator-form-inputs -->
					</form>
				</div><!-- .generator-form -->
			</div><!-- .wrap -->
		</section><!-- #generator -->
	<?php }

	/**
	 * Let's fire the needed functions to set things up.
	 * Loops through each type, and downloads it, moves the files, unzips it and deletes anything not needed.
	 */
	function components_generator_init() {
		foreach ( self::$theme_types as $theme_type ) :
			// Download the file
			$this->components_generator_get_download( $theme_type['branch'], self::$file_data['server']['download_dir'], $theme_type['branch_slash'] );
			// Copy to the prototype directory so we can work with it.
			copy( self::$file_data['server']['download_dir'] . $theme_type['zip_file'], self::$file_data['server']['prototype_dir'] . $theme_type['zip_file'] );
			// Unzip to the prototype directory so we can work with it.
			$this->components_generator_unzip( self::$file_data['server']['prototype_dir'] . $theme_type['zip_file'] );
			// Delete the old file, we don't need it.
			$this->components_generator_delete_file( self::$file_data['server']['root'] . $theme_type['zip_file'] );
		endforeach;
	}

	/**
	 * Let's take the form input, generate and zip of the theme.
	 */
	function components_generator_zippity_zip() {
		if ( ! isset( $_REQUEST['components_generate'], $_REQUEST['components_name'] ) )
			return;

		if ( empty( $_REQUEST['components_name'] ) )
			wp_die( 'Please enter a theme name. Go back and try again.' );

		if ( empty( $_REQUEST['theme-type'] ) ) {
			wp_die( 'Please select a theme type. Go back and try again.' );
		} elseif ( ! empty( $_REQUEST['theme-type'] ) ) {
			if ( $_REQUEST['theme-type'] == 'Base' ) {
				$this->set_theme( self::$theme_types['base']['title'] );
				$this->set_prototype_dir( self::$theme_types['base']['prototype_dir'] );
				$this->set_type_branch( self::$theme_types['base']['branch'] );
			} elseif ( $_REQUEST['theme-type'] == 'Modern Blog' ) {
				$this->set_theme( self::$theme_types['modern']['title'] );
				$this->set_prototype_dir( self::$theme_types['modern']['prototype_dir'] );
				$this->set_type_branch( self::$theme_types['modern']['branch'] );
			} elseif ( $_REQUEST['theme-type'] == 'Classic Blog' ) {
				$this->set_theme( self::$theme_types['classic']['title'] );
				$this->set_prototype_dir( self::$theme_types['classic']['prototype_dir'] );
				$this->set_type_branch( self::$theme_types['classic']['branch'] );
			} elseif ( $_REQUEST['theme-type'] == 'Magazine' ) {
				$this->set_theme( self::$theme_types['magazine']['title'] );
				$this->set_prototype_dir( self::$theme_types['magazine']['prototype_dir'] );
				$this->set_type_branch( self::$theme_types['magazine']['branch'] );
			} elseif ( $_REQUEST['theme-type'] == 'Portfolio' ) {
				$this->set_theme( self::$theme_types['portfolio']['title'] );
				$this->set_prototype_dir( self::$theme_types['portfolio']['prototype_dir'] );
				$this->set_type_branch( self::$theme_types['portfolio']['branch'] );
			} elseif ( $_REQUEST['theme-type'] == 'Business' ) {
				$this->set_theme( self::$theme_types['business']['title'] );
				$this->set_prototype_dir( self::$theme_types['business']['prototype_dir'] );
				$this->set_type_branch( self::$theme_types['business']['branch'] );
			}
		}

		$this->theme = array(
			'name'		  => 'Theme Name',
			'slug'		  => 'theme-name',
			'uri'		  => 'http://components.underscores.me/',
			'author'	  => 'Components.Underscores.me',
			'author_uri'  => 'http://components.underscores.me/',
			'description' => 'Description',
		);

		$this->theme['name']  = trim( $_REQUEST['components_name'] );
		$this->theme['slug']  = sanitize_title_with_dashes( $this->theme['name'] );
		if ( ! empty( $_REQUEST['components_slug'] ) ) {
			$this->theme['slug'] = sanitize_title_with_dashes( $_REQUEST['components_slug'] );
		}

		// Let's check if the slug can be a valid function name.
		if ( ! preg_match( '/^[a-z_]\w+$/i', str_replace( '-', '_', $this->theme['slug'] ) ) ) {
			wp_die( 'Theme slug could not be used to generate valid function names. Please go back and try again.' );
		}
		// Let's check if the name can be a valid theme name.
		if ( preg_match( '/[\'^£$%&*()}{@#~?><>,|=+¬"]/', $this->theme['name'] ) ) {
			wp_die( 'Theme slug could not be used to generate valid theme name. Please go back and try again.' );
		}
		if ( ! empty( $_REQUEST['components_description'] ) ) {
			$this->theme['description'] = trim( $_REQUEST['components_description'] );
		}
		if ( ! empty( $_REQUEST['components_author'] ) ) {
			$this->theme['author'] = trim( $_REQUEST['components_author'] );
		}
		if ( ! empty( $_REQUEST['components_author_uri'] ) ) {
			$this->theme['author_uri'] = trim( $_REQUEST['components_author_uri'] );
		}

		$zip = new ZipArchive;
		$zip_filename = sprintf( self::$file_data['server']['download_dir'] . '-' . str_replace( '/', '-', $this->type_branch ) . '-%s.zip', md5( print_r( $this->theme, true ) ) );
		$res = $zip->open( $zip_filename, ZipArchive::CREATE && ZipArchive::OVERWRITE );
		$exclude_files = array( '.travis.yml', 'codesniffer.ruleset.xml', 'CONTRIBUTING.md', '.git', '.svn', '.DS_Store', '.gitignore', '.', '..' );
		$exclude_directories = array( '.git', '.svn', '.', '..' );

		$iterator = new RecursiveDirectoryIterator( $this->prototype_dir );
		foreach ( new RecursiveIteratorIterator( $iterator ) as $filename ) {
			if ( in_array( basename( $filename ), $exclude_files ) )
				continue;
			foreach ( $exclude_directories as $directory )
				if ( strstr( $filename, "/{$directory}/" ) )
					continue 2; // continue the parent foreach loop
			$local_filename = str_replace( trailingslashit( $this->prototype_dir ), '', $filename );
			if ( 'languages/component_s.pot' == $local_filename )
				$local_filename = sprintf( 'languages/%s.pot', $this->theme['slug'] );
			$contents = file_get_contents( $filename );
			$contents = apply_filters( 'components_generator_file_contents', $contents, $local_filename );
			$zip->addFromString( trailingslashit( $this->theme['slug'] ) . $local_filename, $contents );
		}
		$zip->close();
		header( 'Content-type: application/zip' );
		header( sprintf( 'Content-Disposition: attachment; filename="%s.zip"', $this->theme['slug'] ) );
		readfile( $zip_filename );
		unlink( $zip_filename );
		die();
	}

	/**
	 * Runs when looping through files contents, does the replacements fun stuff.
	 */
	function components_generator_do_replacements( $contents, $filename ) {
		// Replace only text files, skip png's and other stuff.
		$valid_extensions = array( 'php', 'css', 'scss', 'js', 'txt' );
		$valid_extensions_regex = implode( '|', $valid_extensions );
		if ( ! preg_match( "/\.({$valid_extensions_regex})$/", $filename ) )
			return $contents;
		// Special treatment for style.css
		if ( in_array( $filename, array( 'style.css', 'assets/stylesheets/style.scss' ), true ) ) {
			$theme_headers = array(
				'Theme Name'  => $this->theme['name'],
				'Theme URI'	=> esc_url_raw( $this->theme['uri'] ),
				'Author'	  => $this->theme['author'],
				'Author URI'  => esc_url_raw( $this->theme['author_uri'] ),
				'Description' => $this->theme['description'],
				'Text Domain' => $this->theme['slug'],
			);
			foreach ( $theme_headers as $key => $value ) {
				$contents = preg_replace( '/(' . preg_quote( $key ) . ':)\s?(.+)/', '\\1 ' . $value, $contents );
			}
			$contents = preg_replace( '/\bComponents\b/', $this->theme['name'], $contents );
			return $contents;
		}
		// Special treatment for footer.php
		if ( 'footer.php' == $filename ) {
			// <?php printf( __( 'Theme: %1$s by %2$s.', '_s' ), '_s', '<a href="http://automattic.com/" rel="designer">Automattic</a>' );
			$contents = str_replace( 'http://automattic.com/', esc_url( $this->theme['author_uri'] ), $contents );
			$contents = str_replace( 'Automattic', $this->theme['author'], $contents );
			$contents = preg_replace( "#printf\\((\\s?__\\(\\s?'Theme:[^,]+,[^,]+,)([^,]+),#", sprintf( "printf(\\1 '%s',", esc_attr( $this->theme['name'] ) ), $contents );
		}
		// Function names can not contain hyphens.
		$slug = str_replace( '-', '_', $this->theme['slug'] );
		// Regular treatment for all other files.
		$contents = str_replace( "@package Components", sprintf( "@package %s", str_replace( ' ', '_', $this->theme['name'] ) ), $contents ); // Package declaration.
		$contents = str_replace( "components-", sprintf( "%s-",  $this->theme['slug'] ), $contents ); // Script/style handles.
		$contents = str_replace( "'components'", sprintf( "'%s'",  $this->theme['slug'] ), $contents ); // Textdomains.
		$contents = str_replace( "components_", $slug . '_', $contents ); // Function names.
		$contents = preg_replace( '/\bComponents\b/', $this->theme['name'], $contents );
		// Special treatment for readme.txt
		if ( 'readme.txt' == $filename ) {
			$contents = preg_replace('/(?<=Description ==) *.*?(.*(?=(== Installation)))/s', "\n\n" . $this->theme['description'] . "\n\n", $contents );
			$contents = str_replace( 'Components, or components', $this->theme['name'], $contents );
		}
		return $contents;
	}

	/**
	 * Let's set an expiration on the last generation of the theme types and get current time.
	 */
	// We only need to grab the file info of one type zip file since all files are created at once.
	function components_generator_set_expiration_and_go() {
		// We only need to grab the file info of one type zip file since all files are created at once.
		$file_name = self::$file_data['server']['download_dir'] . self::$theme_types['base']['prototype_dir'] . '.zip';
		if ( file_exists( $file_name ) ) {
			$file_time_stamp = date( filemtime( $file_name ) );
			$time = time();
			$expired = 1800; /* Equal to 30 minutes. */
		}

		/**
		 * Let's fire the function as late as we can, and every 30 minutes.
		 * No need to fetch the pattern library and generate types all the time.
		 * If no files exist, let's generate types anyway.
		 */
		if ( file_exists( $file_name ) && $expired <= $time - $file_time_stamp  || ! file_exists( $file_name ) ) {
			add_action( 'wp_footer', array( $this, 'components_generator_init' ) );
		}
	}
}
new Components_Generator_Plugin;
