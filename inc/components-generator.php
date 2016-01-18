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

	protected $theme, $theme_types;
	private $selected_theme, $prototype_dir, $type_branch;

	function __construct() {
		// All the black magic is happening in these actions.
		add_action( 'wp_footer', array( $this, 'components_generator_init' ) );
		add_action( 'init', array( $this, 'components_generator_zippity_zip' ) );
		add_filter( 'components_generator_file_contents', array( $this, 'components_generator_do_replacements' ), 10, 2 );
		// Use do_action( 'components_generator_print_form' ); in your theme to render the form.
		add_action( 'components_generator_print_form', array( $this, 'components_generator_print_form' ) );
	}

	public function set_theme( $the_theme ) {
		$this->selected_theme = $the_theme;
	}

	public function set_prototype_dir( $dir ) {
		$this->prototype_dir = $dir;
	}

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
			exit( 'Oh no! I couldn\'t open the zip:' . $zip_file . '.' );
		}
	}

	/**
	 * This gets our zip from the Github repo.
	 */
	function components_generator_get_download( $branch, $destination, $branch_slash ) {
		// Our repo name.
		$repo = 'theme-pattern-library';
		// Our file name.
		$repo_file_name = $repo . '-' . $branch . '.zip';
		// Grab the file.
		// Github changes forward slashes to dashes to file names, so we account for that.
		if ( $branch_slash == true ) {
			$this->components_generator_download_file( 'https://codeload.github.com/Automattic/theme-pattern-library/zip/' . $branch, str_replace( '/', '-', $repo_file_name ) );
		} else {
			$this->components_generator_download_file( 'https://codeload.github.com/Automattic/theme-pattern-library/zip/' . $branch, $repo_file_name );
		}
		if ( ! file_exists( 'downloads' ) && ! is_dir( 'downloads' ) ) {
			mkdir( $_SERVER[ 'DOCUMENT_ROOT' ] . '/downloads/',  0755 );
		}
		if ( ! file_exists( 'prototype' ) && ! is_dir( 'prototype' ) ) {
			mkdir( $_SERVER[ 'DOCUMENT_ROOT' ] . '/prototype/',  0755 );
		}
		// Copy the file to its new directory.
		if ( $branch_slash == true ) {
			copy( $_SERVER[ 'DOCUMENT_ROOT' ] . '/' . str_replace( '/', '-', $repo_file_name ), $destination . str_replace( '/', '-', $repo_file_name ) );
		} else {
			copy( $_SERVER[ 'DOCUMENT_ROOT' ] . '/' . $repo_file_name, $destination . $repo_file_name );
		}
	}

	/**
	 * Renders the generator form
	 */
	function components_generator_print_form() {
		/*
		 * This contains all the data for our different types.
		 *
		 */
		 $this->theme_types = array(
			'base' => array (
				'title'	=> esc_html__( 'Base', 'components' ),
				'id' => 'type-base',
			),
			'modern' => array (
				'title'	=> esc_html__( 'Modern Blog', 'components' ),
				'id' => 'type-blog-modern',
			),

			'classic' => array (
				'title'	=> esc_html__( 'Classic Blog', 'components' ),
				'id' => 'type-classic',
			),

			'magazine' => array (
				'title'	=> esc_html__( 'Magazine', 'components' ),
				'id' => 'type-magazine',
			),

			'portfolio' => array (
				'title'	=> esc_html__( 'Portfolio', 'components' ),
				'id' => 'type-portfolio',
			),

			'business' => array (
				'title'	=> esc_html__( 'Business', 'components' ),
				'id' => 'type-business',
			),
		); ?>
		<div id="generator-form" class="generator-form">
			<form method="POST">
				<input type="hidden" name="components_generate" value="1" />

				<div class="theme-input">
					<div class="generator-form-primary">
						<fieldset>
							<legend>Choose a theme type:</legend>
							<?php foreach ( $this->theme_types as $this->theme_type ) : ?>
							<input id="<?php echo $this->theme_type['id']; ?>" type="radio" name="theme-type" value="<?php echo $this->theme_type['title']; ?>">
							<label for="<?php echo $this->theme_type['id']; ?>"><?php echo $this->theme_type['title']; ?></label>
							<?php endforeach; ?>
						</fieldset>
						<label for="components-name">Theme Name</label>
						<input type="text" id="components-name" name="components_name" placeholder="Awesome Theme" />
					</div><!-- .generator-form-primary -->

					<div class="generator-form-secondary">
						<label for="components-slug">Theme Slug</label>
						<input type="text" id="components-slug" name="components_slug" placeholder="awesome-theme" />

						<label for="components-author">Author</label>
						<input type="text" id="components-author" name="components_author" placeholder="Your Name" />

						<label for="components-author-uri">Author URI</label>
						<input type="url" id="components-author-uri" name="components_author_uri" placeholder="https://awesometheme.whatever" />

						<label for="components-description">Description</label>
						<input type="text" id="components-description" name="components_description" placeholder="A brief description of your awesome theme" />
					</div><!-- .generator-form-secondary -->
				</div><!-- .generator-form-inputs -->

				<div class="generator-form-submit">
					<input type="submit" name="components_generate_submit" value="Build" />
					<span class="generator-form-version">Based on <a href="https://github.com/Automattic/theme-pattern-library">The Theme Pattern Library from Github</a></span>
				</div><!-- .generator-form-submit -->
			</form>
		</div><!-- .generator-form -->
		<?php
	}

	/**
	 * Let's fire the needed functions to set things up.
	 */
	function components_generator_init() {
		// Base
		$this->components_generator_get_download( 'master', $_SERVER[ 'DOCUMENT_ROOT' ] . '/downloads/', false );
		// Copy to the prototype directory so we can work with it.
		copy( $_SERVER[ 'DOCUMENT_ROOT' ] . '/downloads/theme-pattern-library-master.zip', $_SERVER[ 'DOCUMENT_ROOT' ] . '/prototype/theme-pattern-library-master.zip' );
		$this->components_generator_unzip( $_SERVER[ 'DOCUMENT_ROOT' ] . '/prototype/theme-pattern-library-master.zip' );
		// Delete the old file, we don't need it.
		$this->components_generator_delete_file( $_SERVER[ 'DOCUMENT_ROOT' ] . '/theme-pattern-library-master.zip' );

		// Modern Blog
		$this->components_generator_get_download( 'types/blog-modern', $_SERVER[ 'DOCUMENT_ROOT' ] . '/downloads/', true );
		// Copy to the prototype directory so we can work with it.
		copy( $_SERVER[ 'DOCUMENT_ROOT' ] . '/downloads/theme-pattern-library-types-blog-modern.zip', $_SERVER[ 'DOCUMENT_ROOT' ] . '/prototype/theme-pattern-library-types-blog-modern.zip' );
		$this->components_generator_unzip( $_SERVER[ 'DOCUMENT_ROOT' ] . '/prototype/theme-pattern-library-types-blog-modern.zip' );
		// Delete the old file, we don't need it.
		$this->components_generator_delete_file( $_SERVER[ 'DOCUMENT_ROOT' ] . '/theme-pattern-library-types-blog-modern.zip' );

		// Traditional Blog
		$this->components_generator_get_download( 'types/blog-traditional', $_SERVER[ 'DOCUMENT_ROOT' ] . '/downloads/', true );
		// Copy to the prototype directory so we can work with it.
		copy( $_SERVER[ 'DOCUMENT_ROOT' ] . '/downloads/theme-pattern-library-types-blog-traditional.zip', $_SERVER[ 'DOCUMENT_ROOT' ] . '/prototype/theme-pattern-library-types-blog-traditional.zip' );
		$this->components_generator_unzip( $_SERVER[ 'DOCUMENT_ROOT' ] . '/prototype/theme-pattern-library-types-blog-traditional.zip' );
		// Delete the old file, we don't need it.
		$this->components_generator_delete_file( $_SERVER[ 'DOCUMENT_ROOT' ] . '/theme-pattern-library-types-blog-traditional.zip' );

		// Magazine
		$this->components_generator_get_download( 'types/magazine', $_SERVER[ 'DOCUMENT_ROOT' ] . '/downloads/', true );
		// Copy to the prototype directory so we can work with it.
		copy( $_SERVER[ 'DOCUMENT_ROOT' ] . '/downloads/theme-pattern-library-types-magazine.zip', $_SERVER[ 'DOCUMENT_ROOT' ] . '/prototype/theme-pattern-library-types-magazine.zip' );
		$this->components_generator_unzip( $_SERVER[ 'DOCUMENT_ROOT' ] . '/prototype/theme-pattern-library-types-magazine.zip' );
		// Delete the old file, we don't need it.
		$this->components_generator_delete_file( $_SERVER[ 'DOCUMENT_ROOT' ] . '/theme-pattern-library-types-magazine.zip' );

		// Portfolio
		$this->components_generator_get_download( 'types/portfolio', $_SERVER[ 'DOCUMENT_ROOT' ] . '/downloads/', true );
		// Copy to the prototype directory so we can work with it.
		copy( $_SERVER[ 'DOCUMENT_ROOT' ] . '/downloads/theme-pattern-library-types-portfolio.zip', $_SERVER[ 'DOCUMENT_ROOT' ] . '/prototype/theme-pattern-library-types-portfolio.zip' );
		$this->components_generator_unzip( $_SERVER[ 'DOCUMENT_ROOT' ] . '/prototype/theme-pattern-library-types-portfolio.zip' );
		// Delete the old file, we don't need it.
		$this->components_generator_delete_file( $_SERVER[ 'DOCUMENT_ROOT' ] . '/theme-pattern-library-types-portfolio.zip' );

		// Business
		$this->components_generator_get_download( 'types/business', $_SERVER[ 'DOCUMENT_ROOT' ] . '/downloads/', true );
		// Copy to the prototype directory so we can work with it.
		copy( $_SERVER[ 'DOCUMENT_ROOT' ] . '/downloads/theme-pattern-library-types-business.zip', $_SERVER[ 'DOCUMENT_ROOT' ] . '/prototype/theme-pattern-library-types-business.zip' );
		$this->components_generator_unzip( $_SERVER[ 'DOCUMENT_ROOT' ] . '/prototype/theme-pattern-library-types-business.zip' );
		// Delete the old file, we don't need it.
		$this->components_generator_delete_file( $_SERVER[ 'DOCUMENT_ROOT' ] . '/theme-pattern-library-types-business.zip' );
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
				$this->set_theme( 'Base' );
				$this->set_prototype_dir( $_SERVER[ 'DOCUMENT_ROOT' ] . '/prototype/theme-pattern-library-master/' );
				$this->set_type_branch( 'master' );
			} elseif ( $_REQUEST['theme-type'] == 'Modern Blog' ) {
				$this->set_theme( 'Modern Blog' );
				$this->set_prototype_dir( $_SERVER[ 'DOCUMENT_ROOT' ] . '/prototype/theme-pattern-library-types-blog-modern/' );
				$this->set_type_branch( 'types-blog-modern' );
			} elseif ( $_REQUEST['theme-type'] == 'Classic Blog' ) {
				$this->set_theme( 'Classic Blog' );
				$this->set_prototype_dir( $_SERVER[ 'DOCUMENT_ROOT' ] . '/prototype/theme-pattern-library-types-blog-traditional/' );
				$this->set_type_branch( 'types-blog-traditional' );
			} elseif ( $_REQUEST['theme-type'] == 'Magazine' ) {
				$this->set_theme( 'Magazine' );
				$this->set_prototype_dir( $_SERVER[ 'DOCUMENT_ROOT' ] . '/prototype/theme-pattern-library-types-magazine/' );
				$this->set_type_branch( 'types-magazine' );
			} elseif ( $_REQUEST['theme-type'] == 'Portfolio' ) {
				$this->set_theme( 'Portfolio' );
				$this->set_prototype_dir( $_SERVER[ 'DOCUMENT_ROOT' ] . '/prototype/theme-pattern-library-types-portfolio/' );
				$this->set_type_branch( 'types-portfolio' );
			} elseif ( $_REQUEST['theme-type'] == 'Business' ) {
				$this->set_theme( 'Business' );
				$this->set_prototype_dir( $_SERVER[ 'DOCUMENT_ROOT' ] . '/prototype/theme-pattern-library-types-business/' );
				$this->set_type_branch( 'types-business' );
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
		$zip_filename = sprintf( $_SERVER[ 'DOCUMENT_ROOT' ] . '/downloads/theme-pattern-library-' . $this->type_branch . '-%s.zip', md5( print_r( $this->theme, true ) ) );
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
				'Theme URI'   => esc_url_raw( $this->theme['uri'] ),
				'Author'	  => $this->theme['author'],
				'Author URI'  => esc_url_raw( $this->theme['author_uri'] ),
				'Description' => $this->theme['description'],
				'Text Domain' => $this->theme['slug'],
			);
			foreach ( $theme_headers as $key => $value ) {
				$contents = preg_replace( '/(' . preg_quote( $key ) . ':)\s?(.+)/', '\\1 ' . $value, $contents );
			}
			$contents = preg_replace( '/\bcomponent_s\b/', $this->theme['name'], $contents );
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
		$contents = str_replace( "@package component_s", sprintf( "@package %s", str_replace( ' ', '_', $this->theme['name'] ) ), $contents ); // Package declaration.
		$contents = str_replace( "component_s-", sprintf( "%s-",  $this->theme['slug'] ), $contents ); // Script/style handles.
		$contents = str_replace( "'component_s'", sprintf( "'%s'",  $this->theme['slug'] ), $contents ); // Textdomains.
		$contents = str_replace( "component_s_", $slug . '_', $contents ); // Function names.
		$contents = preg_replace( '/\bcomponent_s\b/', $this->theme['name'], $contents );
		// Special treatment for readme.txt
		if ( 'readme.txt' == $filename ) {
			$contents = preg_replace('/(?<=Description ==) *.*?(.*(?=(== Installation)))/s', "\n\n" . $this->theme['description'] . "\n\n", $contents );
			$contents = str_replace( 'component_s, or components', $this->theme['name'], $contents );
		}
		return $contents;
	}

	/**
	 * Let's set an expiration on the last generation of the theme types and get current time.
	 */
	// We only need to grab the file info of one type zip file since all files are created at once.
	function components_generator_set_expiration_and_go() {
		// We only need to grab the file info of one type zip file since all files are created at once.
		$filename = get_template_directory() . '/downloads/' . 'base.zip';
		if ( file_exists( $filename ) ) {
			$fileTimeStamp = date( filemtime( $filename ) );
			$time = time();
			$expired = 1800; /* Equal to 30 minutes. */
		}

		/**
		 * Let's fire the function as late as we can, and every 30 minutes.
		 * No need to fetch the pattern library and generate types all the time.
		 * If no files exist, let's generate types anyway.
		 */
		if ( file_exists( $filename ) && $expired <= $time - $fileTimeStamp  || ! file_exists( $filename ) ) {
			add_action( 'wp_footer', array( $this, 'components_generator_init' ) );
		}
	}
}
new Components_Generator_Plugin;
