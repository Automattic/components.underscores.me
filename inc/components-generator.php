<?php
/**
 * Plugin Name: Components Generator
 * Description: Generates themes based on Components by Automattic.
 */

class Components_Generator_Plugin {

	var $build_dir = 'build';
	var $repo_url = 'https://codeload.github.com/Automattic/theme-components/zip/master';
	var $repo_file_name = 'theme-components-master.zip';
	var $components_dir;
	var $selected_theme;
	var $prototype_dir;
	var $bypass_cache = false;
	var $logging = true;

	function __construct() {
		// Initialize class properties.
		$this->bypass_cache = apply_filters( 'components_bypass_cache', false );
		$this->logging = apply_filters( 'components_logging', true );
		$this->build_dir = sprintf( '%s/%s', get_stylesheet_directory(), $this->build_dir );
		$this->repo_url = esc_url_raw( $this->repo_url );
		$this->components_dir = $this->build_dir . '/' . str_replace( '.zip', '', $this->repo_file_name );

		// Let's run a few init functions to set things up.
		// Using a low priority to make sure it runs before other actions.
		add_action( 'init', array( $this, 'set_expiration_and_go' ), 1 );

		// Create zips when we need them.
		add_action( 'init', array( $this, 'create_zippity_zip' ), 2 );

		// A filter to run replacements on.
		add_filter( 'components_generator_file_contents', array( $this, 'replace_theme_fields' ), 10, 2 );

		// Use do_action( 'render_types_form' ); in your theme to render the form.
		add_action( 'render_types_form', array( $this, 'render_types_form' ), 3 );
	}

	/**
	 * This is an init function to grab theme components so we can control when it's called by the generator.
	 */
	public function get_theme_components_init() {
		// Ensure build directory exists.
		$this->ensure_directory( $this->build_dir );

		// Grab theme components from its Github repo.
		$this->get_theme_components( $this->build_dir );

		// Generate our types cache.
		$this->gen_types_cache();

		// Grab our type data.
		$types = $this->get_types();

		// Build each type directory, so we can work with it.
		foreach ( $types as $type => $title ) {
			$this->build_type( $type );
		}
	}

	/**
	 * Creates an array and a JSON file from type configs to cache type data.
	 * The generator form uses this to render type choices automatically.
	 */
	public function gen_types_cache() {
		// Our array of data that we use to cache the types.
		$types = array();

		// Scan our config files and grab the file names.
		$configs = $this->components_dir . '/configs/*.json';
		$files = glob( $configs );

		// Check for valid config files, pull out the type names, capitalize them, and use them for data.
		if ( is_array( $files ) && ! empty( $files ) ) {
			foreach( $files as $file ) {
				preg_match( '%/type-([^\.]+)\.json$%', $file, $matches );
				if ( ! empty( $matches ) ) {
					$id = $matches[1];
					$title = join( ' ', array_map( 'ucwords', explode( '-', $matches[1] ) ) );
					$types[$id] = $title;
				}
			}
			// Create a JSON file from our $types array so that we can use it for as a cache for rendering the generator form.
			file_put_contents( $this->build_dir . '/types.json', json_encode( $types, JSON_PRETTY_PRINT ) );
		} else {
			$this->log_message( __( 'Error: type.json was not rebuilt successfully because configs were not able to be read.', 'components' ) );
		}

	}

	/**
	 * Places data in JSON files in an array for later use.
	 */
	public function read_json( $file ) {
		$json = file_get_contents( $file );
		return json_decode( $json, TRUE );
	}

	/**
	 * Builds a given type from theme components.
	 */
	public function build_type( $type ) {
		// The target directory where we will be working on.
		$target_dir = $this->build_dir . '/' . $type;

		// Get type config.
		if ( 'base' === $type ) {
			$config = array();
		} else {
			$config_path = sprintf( '%s/configs/type-%s.json', $this->components_dir, $type );
			$config = $this->read_json( $config_path );
		}

		// Copy just build files we need to start with so we can work with them.
		$exclude_from_build = array( 'assets', 'components', 'configs', 'CONTRIBUTING.md', 'README.md', 'templates', 'types' );
		$this->copy_build_files( $this->components_dir, $target_dir, $exclude_from_build );

		// Handle config.
		$this->handle_config( $type, $config, $target_dir );
	}

	/**
	 * This gets our zip from the Github repo.
	 */
	public function get_theme_components( $destination ) {
		if ( has_filter( 'components_local_dev' ) && true === apply_filters( 'components_local_dev', true ) ) {
			// Components Local Dev plugin is running, so let the generator use a local copy of Components.
			// The zip file path in the root of WordPress install, created by Components Local Dev plugin.
			$zipfile = $this->repo_file_name;

			// Copy the local copy of Components to build directory.
			copy( $zipfile, $destination . '/' . $zipfile );

			// Rename the variable to the file in the build directory so the generator can work with it.
			$zipfile = $destination . '/' . $this->repo_file_name;
		} else {
			// Let's use the latest copy of Components from Github.
			// The zip file path.
			$zipfile = $destination . '/' . $this->repo_file_name;

			// Get our download from Github.
			$this->download_file( $this->repo_url, $zipfile );
		}

		// Unzip the file.
		$this->unzip_file( $zipfile );
	}

	/**
	 * Read files to process from base. Stores files on array for processing.
	 */
	public function read_base_dir( $dir ) {
		$files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir ) );
		$filelist = array();
		$exclude = array( '.travis.yml', 'codesniffer.ruleset.xml', 'README.md', 'CONTRIBUTING.md', '.git', '.svn', '.DS_Store', '.gitignore', '.', '..' );
		foreach( $files as $file ) {
			if ( ! in_array( basename( $file ), $exclude )	) {
				$filelist[] = $file;
			}
		}
		return $filelist;
	}

	/**
	 * Handles the configuration and coordinates everything.
	 */
	public function handle_config( $type, $config, $target_dir ) {
		// Set default configuration options.
		$config = wp_parse_args( $config, array(
			'replacement_files' => array(),
			'sass_replace' => array(),
			'components' => array(),
			'templates' => array(),
			'js' => array(),
		) );

		// Add default components to base.
		if ( 'base' === $type ) {
			$config['components'] = array(
				'header/site-branding',
				'navigation/navigation-top',
				'footer/site-info',
				'post/content-footer',
				'post/content-meta',
			);
		}

		// Iterate over each config section and process individually.
		foreach ( $config as $section => $args ) {
			switch ( $section ) {
				case 'replacement_files';
					$this->add_replacement_files( $type, $args, $target_dir );
					break;
				case 'sass_replace';
					$this->add_sass_includes( $type, $args, $target_dir );
					break;
				case 'components';
					if ( isset( $config['replacement_files'] ) ) {
						$replacements = (array) $config['replacement_files'];
					} else {
						$replacements = array();
					}
					$this->add_component_files( $args, $target_dir, $replacements );
					break;
				case 'templates';
					$this->add_templates( $args, $target_dir );
					break;
				case 'js';
					$this->add_javascript( $type, $args, $target_dir );
					break;
			}
		}
	}

	/**
	 * Adds component files needed for the build.
	 */
	public function add_component_files( $components, $target_dir, $replacements ) {
		 // Ensure components directory exists.
		 $this->ensure_directory( $target_dir . '/components' );

		 // Insert array, used to insert components.
		 $insert = array();

		// Iterate over each component.
		foreach ( $components as $comp ) {
			// Check if PHP extension has been specified.
			$is_phpfile = preg_match( '/\\.php$/', $comp );

			// Get the component path.
			$path = sprintf( '%s/components/%s', $this->components_dir, $comp );

			// If extension is not specified, check if file exists with extension.
			if ( ! $is_phpfile && file_exists( $path . '.php' ) ) {
				$phpfile_exists = true;
				$is_phpfile = true;
				$path .= '.php';
			} else {
				$phpfile_exists = false;
			}

			// If it's a file, copy it over to the target directory.
			if ( $is_phpfile && ( $phpfile_exists || file_exists( $path ) ) ) {
				$file = array_pop( ( explode( '/components/', $path ) ) ); // Enclose in parens to pass by reference.
				$dest = $target_dir . '/components/' . $file;
				$this->ensure_directory( dirname( $dest ) );
				$insert[] = preg_replace( '%/+%', '/', $path );
				copy( $path, $dest );

			// If it's a directory, copy all files contained within.
			} else if ( is_dir( $path ) ) {

				// Get files in component directory, excluding unwanted paths.
				$files = $this->read_dir( $path );

				// Add files into insertion array for later.
				foreach ( $files as $file ) {
					$insert[] = preg_replace( '%/+%', '/', $path . '/' . $file );
				}

				// Copy files to build.
				$dest =	 $target_dir . '/components/' . $comp;
				$this->ensure_directory( dirname( $dest ) );
				$this->copy_files( $path, $files, $dest );
			}
		}

		// Get build sources.
		$sources = $this->get_build_sources( $target_dir );

		// Make sure all template files included via `get_template_part()` are in the build.
		foreach ( $sources as $file ) {

			// Get the file source we'll be working with.
			$src = $file['source'];
			preg_match_all( '/get_template_part([^;]+);/', $src, $matches );

			// If we have calls to `get_template_parts()`, proceed.
			if ( is_array( $matches ) && ! empty( $matches[1] ) ) {

				// Process each call individually.
				foreach ( $matches[1] as $line ) {
					$line = preg_replace( '/(^\(\s*|\s*\)$)/', '', $line );
					$parts = preg_split( '/\s*,\s*/', $line );
					$first = trim( preg_replace( '/(\'|")/', '', $parts[0] ) );
					$second = isset( $parts[1] ) ? $parts[1] : null;

					// If the second parameter is a string, then we only need one file.
					if ( preg_match( '/^(\'|")/', $second ) ) {
						$second = trim( preg_replace( '/(\'|")/', '', $second ) );
						$src_file = sprintf( '%s/%s-%s.php', $this->components_dir, $first, $second );
						$target_file = sprintf( '%s/%s-%s.php', $target_dir, $first, $second );
						$this->ensure_directory( dirname( $target_file ) );
						if ( file_exists( $src_file ) ) {
							copy( $src_file, $target_file );
						}

					// If the second parameter is a function call, then we need to copy all files,
					// since there is no way to accurately determine which file will be included.
					// .e.g `get_template_part( 'components/post/content', get_post_type() );`
					} else {
						$parts = preg_split( '%/+%', $first );
						$prefix = trim( array_pop( $parts ) );
						$src_dir = sprintf( '%s/%s', $this->components_dir, implode( '/', $parts ) );
						$files = $this->read_dir( $src_dir );
						$regex = sprintf( '/^%s/', $prefix );
						$matches = preg_grep( $regex, $files );
						$dest = $target_dir . '/' . dirname( $first );
						$this->copy_files( $src_dir, $matches, $dest );
					}
				}
			}
		}

		// Get updated build sources.
		$sources = $this->get_build_sources( $target_dir );

		// Replace components in sources.
		foreach ( $sources as $file ) {
			$src = $file['source'];
			$filename = str_replace( $target_dir . '/', '', $file['path'] );

			// Boolean variable, which determines if we must copy over the components included
			// in any of the files that are overridden by the type. If a type overrides a template,
			// then it is assumed that the insertion comments in that file must be in the build.
			$copy_over = in_array( $filename, $replacements );

			// Get all of the insertion comments in source.
			preg_match_all( '%<!-- components/([^\s]+)\s+-->%', $src, $matches );

			// If we have matches, then proceed
			if ( is_array( $matches ) && ! empty( $matches ) ) {
				$comments = $matches[0];

				// Iterate over each of the insertion comments.
				foreach ( $comments as $comment ) {

					// Generate the `get_teplate_part()` calls needed to insert components.
					$comp = preg_replace( '%(<!--\s+|\s+-->)%', '', $comment );
					$template = preg_replace( '/\\.php$/', '', basename( $comp ) );
					$parts = explode( '-', $template );

					// Depending on the number of words in the template filename, we generate the PHP code.
					if ( 1 === count( $parts ) ) {
						$code = sprintf( "<?php get_template_part( '%s', '%s' ); ?>", dirname( $comp ), $parts[0] );
						pre( $code );
					} else {
						$tpl = array_shift( $parts );
						$code = sprintf( "<?php get_template_part( '%s/%s', '%s' ); ?>", dirname( $comp ), $tpl, implode( '-', $parts ) );
					}

					// The component file we're working with.
					$compfile = preg_replace( '%/+%', '/', $this->components_dir . '/' . $comp );

					// If the component file is included in the `components` config -OR- we're copying over, proceed.
					if ( in_array( $compfile, $insert ) || $copy_over ) {

						// Replace the insertion comment with the actual `get_template_part()` call.
						$src = str_replace( $comment, $code, $src );

						// If we're copying over, make sure the component is copied in the build.
						if ( $copy_over ) {
							$comp_path = $this->components_dir . '/' . $comp;
							$comp_target = $target_dir . '/' . $comp;
							$this->ensure_directory( dirname( $comp_target ) );
							copy( $comp_path, $comp_target );
						}
					}
				}

				// Remove any insertion comments that are not needed.
				$src = preg_replace( '/\s*<!--\s+[^\s]+\s+-->\s*\n/', "\n", $src );

				// If the original source has been modified, then write the file. Otherwise don't.
				if ( $src !== $file['source'] ) {
					file_put_contents( $file['path'], $src );
				}
			}
		}

		// Move templates to theme's root where applicable.
		$components_dir = $target_dir . '/components';
		$component_files = $this->read_dir_recursive( $components_dir );
		$component_directories = array();
		foreach( $component_files as $file ) {
			$filename = basename( $file );
			if ( preg_match( '/^(archive|single|taxonomy|category|tag|page|author|embed)-/', $filename ) ) {
				rename( $file, $target_dir . '/' . $filename );
				$component_directories[] = dirname( $file );
			}
		}

		// After moving, delete any empty directories inside components/.
		$component_directories = array_unique( array_merge( $component_directories, $this->read_dir( $components_dir, true ) ) );
		foreach ( $component_directories as $dir ) {
			if ( is_dir( $dir ) && 0 === count( $this->read_dir( $dir ) ) ) {
				rmdir( $dir );
			}
		}
	}

	/**
	 * Reads a directory excluding wildcards.
	 */
	public function read_dir( $path, $fullpath=false ) {
		$files = preg_grep( '/^[\\.]{1,2}$/', scandir( $path ), PREG_GREP_INVERT );
		sort( $files ); // Ensure indexes start from zero.
		if ( $fullpath ) {
			foreach ( $files as $i => $file ) {
				$files[$i] = $path . '/' . $file;
			}
		}
		return $files;
	}

	/**
	 * Recursively reads a directory.
	 */
	public function read_dir_recursive( $path, $regex_filter=null ) {
		$iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $path ) );
		$files = array();
		foreach( $iterator as $file ) {
			$files[] = (string) $file->getPathName();
		}
		$files = preg_grep( '%/[\\.]{1,2}$%', $files, PREG_GREP_INVERT );
		if ( $regex_filter ) {
			$files = preg_grep( $regex_filter, $files );
		}
		return $files;
	}

	/**
	 * Gets the build sources and associated file data.
	 */
	public function get_build_sources( $dir) {
		// Get all files recursively in build dir, filtering only PHP files.
		$files = $this->read_dir_recursive( $dir, '/\\.php$/' );

		// Process file data.
		$data = array();
		foreach ( $files as $path ) {
			$data[] = array(
				'path' => $path,
				'source' => file_get_contents( $path ),

			);
		}

		// Return processed data.
		return $data;
	}

	/**
	 * Replaces files in the build from those specified by type.
	 */
	public function add_replacement_files( $type, $files, $target_dir ) {
		// Copy files to the target directory.
		$src_dir = sprintf( '%s/types/%s', $this->components_dir, $type );
		$this->copy_files( $src_dir, $files, $target_dir );
	}

	/**
	 * Adds sass includes to the build and takes care of file overrides.
	 */
	public function add_sass_includes( $type, $files, $target_dir ) {
		// Get stylesheets directory from type.
		$style_dir = sprintf( '%s/types/%s/assets/stylesheets', $this->components_dir, $type );
		$dest = $target_dir . '/assets/stylesheets';

		// Ensure stylesheets directory exists.
		$this->ensure_directory( $dest );

		// Check if style.scss is being overridden.
		if ( file_exists( $style_dir . '/style.scss' ) ) {
			$this->copy_files( $style_dir, $files, $dest );

		// Check if we have a stylesheets directory.
		} else if ( is_dir( $style_dir ) ) {

			// Copy files to target directory.
			$this->copy_files( $style_dir, $files, $dest );

			// Copy stylesheet to target directory.
			copy( $this->components_dir . '/assets/stylesheets/style.scss', $dest . '/style.scss' );

		// Otherwise, we need to append.
		} else {

			// Generate the SASS import codes for the stylesheets.
			$imports = array();
			foreach( $files as $item ) {
				$parts = explode( '/', $item );
				$file = array_pop( $parts );
				$file = preg_replace( '/(^_|\.scss$)/', '', $file );
				$path = implode( '/', $parts );
				$title = explode( '-', preg_replace( '/(^_|\.scss$)/', '', $file ) );
				$title = join( ' ', array_map( 'ucwords', $title ) );
				$css = array();
				$css[] = '/*--------------------------------------------------------------';
				$css[] = sprintf( '# %s', $title );
				$css[] = '--------------------------------------------------------------*/';
				$css[] = sprintf( '@import "%s/%s";', $path, $file );
				$imports[] = implode( "\n", $css );

				// Copy the included file.
				$src_file = $this->components_dir . '/assets/stylesheets/' . $item;
				if ( file_exists( $src_file ) ) {
					$dest_file = $dest . '/' . $item;
					$this->ensure_directory( dirname( $dest_file ) );
					copy( $src_file, $dest_file );
				} else {
					$this->log_message( sprintf( 'Stylesheet not found: %s', $src_file ) );
				}
			}

			// Read style.scss source.
			copy( $this->components_dir . '/assets/stylesheets/style.scss', $dest . '/style.scss' );
			$src = file_get_contents( $target_dir . '/assets/stylesheets/style.scss' );

			// Add import calls.
			foreach ( $imports as $item ) {
				$src .= "\n" . $item;
			}

			// Update style.scss with the new source.
			file_put_contents( $dest . '/style.scss', $src );
		}

		// Get the stylesheets paths included in the sass file.
		$paths = $this->get_stylesheet_paths( $target_dir, $dest . '/style.scss' );

		// Copy the paths to the build directory.
		foreach ( $paths as $path ) {

			// To avoid overriding the type's original stylesheets, we check if the file to be copied
			// is not in the files array. This means we will keep the one the type overrides.
			if ( in_array( $path, $files ) ) {
				continue;
			}

			$src_file = $this->components_dir . '/assets/stylesheets/' . $path;
			$target_file = $dest . '/' . $path;

			// Make sure the directory of the file we're copying exists.
			$this->ensure_directory( dirname( $target_file ) );

			// Only copy the file if it exists.
			if ( file_exists( $src_file ) ) {
				copy( $src_file, $target_file );
			}
		}
	}

	/**
	 * Gets list of stylesheets to include
	 */
	public function get_stylesheet_paths( $target_dir, $stylesheet, $basedir=null ) {
		// Initialize variables.
		$paths = array();
		$assets_dir = $this->components_dir . '/assets/stylesheets';
		$src = file_get_contents( $stylesheet );

		// Match all import statements using regex.
		preg_match_all( '%^(\s*//\s*)?@import\s*"([^"]+)"%m', $src, $matches );
		if ( is_array( $matches ) && isset( $matches[2] ) ) {

			// Array containing matching comments of import statements.
			$comments = $matches[1];

			// Iterate over each of the matches, getting the index of the match,
			// used to determine if the import statement is commented out.
			foreach ( $matches[2] as $idx => $file ) {

				// Ignore commented imports.
				if ( ! empty( $comments[$idx] ) ) {
					continue;
				}

				// If we have a base directory (calling recursively, prepend it to the filename)
				if ( isset( $basedir ) ) {
					$file = $basedir . '/' . $file;
				}

				// Build the path based on the parts we created above.
				$parts = explode( '/', $file );
				if ( 1 === count( $parts ) ) {
					$file = sprintf( '_%s.scss', $parts[0] );
				} else {
					$filename = array_pop( $parts );
					$file = sprintf( '%s/_%s.scss', implode( '/', $parts ), $filename );
				}

				// Append the detected file to our paths array.
				$paths[] = $file;

				// Now we need to get the basedir to pass to recursive calls.
				$dirname = dirname( $file );
				$path = $assets_dir . '/' . $file;

				// If the file doesn't exist in the components directory, then this means
				// the type is providing the file, so use the type's path instead.
				if ( ! file_exists( $path ) ) {
					$path = $target_dir . '/assets/stylesheets/' . $file;
				}

				// Get inner paths of file, calling the method recursively.
				if ( '.' === $dirname ) {
					$inner_paths = $this->get_stylesheet_paths( $target_dir, $path, null );
				} else {
					$inner_paths = $this->get_stylesheet_paths( $target_dir, $path, $dirname );
				}

				// Add the paths we detected above with our paths array.
				$paths = array_merge( $paths, $inner_paths );
			}
		}
		return $paths;
	}

	/**
	 * Adds templates to the build.
	 */
	public function add_templates( $files, $target_dir ) {
		// Copy files to the target directory.
		$this->copy_files( $this->components_dir . '/templates', $files, $target_dir . '/templates' );
	}

	/**
	 * Removes component insertion comments from source.
	 */
	public function add_javascript( $type, $files, $target_dir ) {
		// The type's assets/js directory.
		$src_dir = sprintf( '%s/types/%s/assets/js', $this->components_dir, $type );

		// Update target dir to point to JavaScript assets directory.
		$target_dir .= '/assets/js';

		// Copy the base JavaScript files.
		$js_path = $this->components_dir . '/assets/js';
		$js_files = preg_grep( '/\\.js$/',  $this->read_dir( $js_path ) );
		$this->copy_files( $js_path, $js_files, $target_dir );

		// Copy over the type files (overriding base files).
		$this->copy_files( $src_dir, $files, $target_dir );
	}

	/**
	 * Renders the generator type form.
	 */
	function render_types_form() {
		// Get the types available.
		$types = $this->get_types(); ?>
		<section id="generator">
			<div class="hide-overflow">

				<div class="gear-set-one">
					<?php echo file_get_contents( get_template_directory() . '/assets/img/gear-set.svg' ); ?>
				</div><!-- .gear-set-one -->

				<div class="gear-set-two">
					<?php echo file_get_contents( get_template_directory() . '/assets/img/gear-set.svg' ); ?>
				</div><!-- .gear-set-two -->
			</div><!-- .hide-overflow -->

			<div class="wrap">
				<h2>Build your own Components theme</h2>
				<p>Pick a type, fill out the information about your new theme, and download it.</p>
				<div id="generator-form" class="generator-form">
					<form method="POST" novalidate>
						<input type="hidden" name="components_types_generate" value="1" />

						<div class="theme-input clear">
							<div class="generator-form-primary">
								<fieldset>
									<legend class="components-label">Theme type <span class="required">(Required)</span></legend>
									<div class="components-radio-block">
										<?php
											$i = 0;
											foreach ( $types as $type => $title ) :
												// Check our first radio as a default and add a required to it.
												if ( 0 == $i ) {
													$checked = 'checked="checked"';
													$required = ' required';
												} else {
													$checked = null;
													$required = null;
												}
												$i++;
										?>
											<div class="components-radio-group">
												<input id="<?php echo 'type-' . $type; ?>" class="components-input" type="radio" name="components_theme_type" value="<?php echo $title; ?>" <?php echo $checked; echo $required; ?>>
												<label class="components-label" for="<?php echo 'type-' . $type; ?>"><?php echo $title; ?></label>
											</div>
										<?php endforeach; ?>
									</div>
								</fieldset>
							</div><!-- .generator-form-primary -->

							<div class="generator-form-secondary">
								<div class="components-form-field">
									<label class="components-label" for="components-types-name">Theme Name <span class="required">(Required)</span></label>
									<input type="text" id="components-types-name" class="components-input" name="components_types_name" placeholder="Awesome Theme" required>
								</div>

								<div class="components-form-field">
									<label class="components-label" for="components-types-slug">Theme Slug</label>
									<input type="text" id="components-types-slug" class="components-input" name="components_types_slug" placeholder="awesome-theme">
								</div>

								<div class="components-form-field">
									<label class="components-label" for="components-types-author">Author Name</label>
									<input type="text" id="components-types-author" class="components-input" name="components_types_author" placeholder="Your Name">
								</div>

								<div class="components-form-field">
									<label class="components-label" for="components-types-author-uri">Author URI</label>
									<input type="url" id="components-types-author-uri" class="components-input" name="components_types_author_uri" placeholder="http://themeshaper.com/">
								</div>

								<div class="components-form-field">
									<label class="components-label" for="components-types-description">Theme description</label>
									<input type="text" id="components-types-description" class="components-input" name="components_types_description" placeholder="A brief description of your awesome theme">
								</div>

								<div class="generator-form-submit">
									<input type="submit" name="components_types_generate_submit" value="Download Theme">
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
	 * Runs when looping through files contents, does the replacements fun stuff.
	 */
	function replace_theme_fields( $contents, $filename ) {
		// Replace only text files, skip png's and other stuff.
		$valid_extensions = array( 'php', 'css', 'scss', 'js', 'txt' );
		$valid_extensions_regex = implode( '|', $valid_extensions );
		if ( ! preg_match( "/\.({$valid_extensions_regex})$/", $filename ) ) {
			return $contents;
		}

		// Special treatment for style.css
		if ( in_array( $filename, array( 'style.css', 'assets/stylesheets/style.scss' ), true ) ) {
			$theme_headers = array(
				'Theme Name'  => $this->theme['name'],
				'Theme URI'	=> esc_url_raw( $this->theme['uri'] ),
				'Author'		=> $this->theme['author'],
				'Author URI'  => esc_url_raw( $this->theme['author_uri'] ),
				'Description' => $this->theme['description'],
				'Text Domain' => $this->theme['slug'],
			);
			foreach ( $theme_headers as $key => $value ) {
				$contents = preg_replace( '/(' . preg_quote( $key ) . ':)\s?(.+)/', '\\1 ' . $value, $contents );
			}
			$contents = preg_replace( '/\bComponents\b/', $this->theme['name'], $contents );
			// Grab the GPL statement in stylesheets and re-replace with Components
			$contents = preg_replace( '/\b' . preg_quote( $this->theme['name'] ) . ' is distributed\b/', 'Components is distributed', $contents );
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
	 * Let's take the form input, generate and zip of the theme.
	 */
	function create_zippity_zip() {
		if ( ! isset( $_REQUEST['components_types_generate'], $_REQUEST['components_types_name'] ) ) {
			return;
		}

		// Grab our type data.
		$types = $this->get_types();

		$tmp = $this->build_dir . '/tmp';

		$this->ensure_directory( $tmp );

		if ( empty( $_REQUEST['components_types_name'] ) ) {
			wp_die( 'Please enter a theme name. Go back and try again.' );
		}

		$this->theme = array(
			'name'		  => 'Theme Name',
			'slug'		  => 'theme-name',
			'uri'		  => 'http://components.underscores.me/',
			'author'	  => 'Automattic',
			'author_uri'  => 'http://automattic.com/',
			'description' => 'Description',
		);

		if ( empty( $_REQUEST['components_theme_type'] ) ) {
			wp_die( 'Please select a theme type. Go back and try again.' );
		} elseif ( ! empty( $_REQUEST['components_theme_type'] ) ) {
			foreach ( $types as $type => $title ) {
				switch ( $_REQUEST['components_theme_type'] ) {
					case $title:
						$hash = md5( print_r( $this->theme, true ) );
						$this->prototype_dir = $tmp . '/' . $type . '-' . $hash;
						$this->copy_build_files( $this->build_dir . '/' . $type, $this->prototype_dir );
						$this->selected_theme = $title;
						break;
				}
			}
		}

		$this->theme['name']  = trim( $_REQUEST['components_types_name'] );
		$this->theme['slug']  = sanitize_title_with_dashes( $this->theme['name'] );
		if ( ! empty( $_REQUEST['components_types_slug'] ) ) {
			$this->theme['slug'] = sanitize_title_with_dashes( $_REQUEST['components_types_slug'] );
		}

		// Let's check if the slug can be a valid function name.
		if ( ! preg_match( '/^[a-z_]\w+$/i', str_replace( '-', '_', $this->theme['slug'] ) ) ) {
			wp_die( 'Theme slug could not be used to generate valid function names. Special characters are not allowed. Please go back and try again.' );
		}
		// Let's check if the name can be a valid theme name.
		if ( preg_match( '/[\'^£$%&*()}{@#~?><>,|=+¬"]/', $this->theme['name'] ) ) {
			wp_die( 'Theme name could not be used to generate valid theme name. Special characters are not allowed. Please go back and try again.' );
		}
		if ( ! empty( $_REQUEST['components_types_description'] ) ) {
			$this->theme['description'] = trim( $_REQUEST['components_types_description'] );
		}
		if ( ! empty( $_REQUEST['components_types_author'] ) ) {
			$this->theme['author'] = trim( $_REQUEST['components_types_author'] );
		}
		if ( ! empty( $_REQUEST['components_types_author_uri'] ) ) {
			$this->theme['author_uri'] = trim( $_REQUEST['components_types_author_uri'] );
			// Let's check if the uri is valid.
			if ( ! preg_match( '|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $this->theme['author_uri'] ) ) {
				wp_die( 'Author URI is not valid. Be sure to include <code>http://</code>. Please go back and try again.' );
			}
		}

		$zip = new ZipArchive;
		$zip_filename = $this->prototype_dir . sprintf( 'components-%s.zip', md5( print_r( $this->theme, true ) ) );
		$res = $zip->open( $zip_filename, ZipArchive::CREATE && ZipArchive::OVERWRITE );
		$exclude_files = array( '.travis.yml', 'codesniffer.ruleset.xml', 'README.md', 'CONTRIBUTING.md', '.git', '.svn', '.DS_Store', '.gitignore', '.', '..' );
		$exclude_directories = array( '.git', '.svn', '.', '..' );

		$iterator = new RecursiveDirectoryIterator( $this->prototype_dir );
		foreach ( new RecursiveIteratorIterator( $iterator ) as $filename ) {
			if ( in_array( basename( $filename ), $exclude_files ) ) {
				continue;
			}
			foreach ( $exclude_directories as $directory ) {
				if ( strstr( $filename, "/{$directory}/" ) ) {
					continue 2; // continue the parent foreach loop
				}
			}
			$local_filename = str_replace( trailingslashit( $this->prototype_dir ), '', $filename );
			$contents = file_get_contents( $filename );
			$contents = apply_filters( 'components_generator_file_contents', $contents, $local_filename );
			$zip->addFromString( trailingslashit( $this->theme['slug'] ) . $local_filename, $contents );
		}
		$zip->close();
		$this->do_tracking();
		header( 'Content-type: application/zip' );
		header( sprintf( 'Content-Disposition: attachment; filename="%s.zip"', $this->theme['slug'] ) );
		readfile( $zip_filename );
		unlink( $zip_filename );
		$this->delete_directory( $this->prototype_dir );
		exit();
	}

	/**
	 * Returns an array with the available types.
	 */

	public function get_types() {
		static $types;
		if ( ! isset( $types ) ) {
			$types = $this->read_json( $this->build_dir . '/types.json' );
			// Prepend Base theme data to the $types array.
			// It isn't a type, but should still be included in the generator processes.
			$types = array_merge( array( 'base' => 'Base' ), $types );
		}
		return $types;
	}

	// Utility functions: These help the generator do its work.

	/**
	 * Copies files to a given directory
	 */
	public function copy_files( $src_dir, $files, $target_dir ) {
		// Do nothing if no files to copy
		if ( empty( $files ) ) return;

		// Make sure target directory exists.
		$this->ensure_directory( $target_dir );

		// Copy over the files
		foreach( $files as $file ) {

			// If the files specified are inside a directory, we need to make sure these
			// directories exist before copying the files, otherwise we get a warning.
			if ( preg_match( '%/%', $file ) ) {
				$this->ensure_directory( $target_dir . '/' . dirname( $file ) );
			}

			copy( $src_dir . '/' . $file, $target_dir . '/' . $file );
		}
	}

	/**
	 * Copy files to temporary build directory.
	 */
	public function copy_build_files( $source_dir, $target_dir, $exclude = array() ) {
		// Bail if source directory is not a directory.
		if ( ! is_dir( $source_dir ) ) {
			return;
		}

		// Make sure target directory exists.
		$this->ensure_directory( $target_dir, true );

		// Add current and previous directory wildcards to excludes.
		$exclude = array_merge( array( '.', '..' ), $exclude );

		// Open directory handle.
		$dir = opendir( $source_dir );

		// Iterate, as long as we have files.
		$file = readdir( $dir );
		while ( false !== $file ) {
			if ( ! in_array( $file, $exclude ) ) {
				if ( is_dir( $source_dir . '/' . $file ) ) {
					// Calling the method recursively, without passing the files to exclude.
					// This has the side effect of only excluding files in the root, and not the ones in subdirectories.
					$this->copy_build_files( $source_dir . '/' . $file, $target_dir . '/' . $file );
				} else {
					copy( $source_dir . '/' . $file, $target_dir . '/' . $file );
				}
			}
			$file = readdir( $dir ); // Set file for next iteration.
		}

		// Close directory handle.
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
		if ( true === $res	) {
			$zip->extractTo( $path ); // Extract it to the path we determined above.
			$zip->close();
		} else {
			die( 'Oh no! I couldn\'t open the zip: ' . $zip_file . '.' );
		}
	}

	/**
	 * Checks if a directory exists, creates it otherwise.
	 *
	 * @see http://php.net/mkdir
	 */
	public function ensure_directory( $directory, $delete_if_exists=false ) {
		if ( ! file_exists( $directory ) && ! is_dir( $directory ) ) {

			// Create the directory recursively
			if ( ! mkdir( $directory, 0755, true ) ) {
				$this->log_message( sprintf( __( 'Error: %s directory was not able to be created.', 'components' ), $directory ) );
			}

		} else if ( $delete_if_exists && is_dir( $directory ) ) {
			$this->delete_directory( $directory );
			$this->ensure_directory( $directory );
		}
	}

	/**
	 * This deletes a file.
	 */
	public function delete_file( $URI ) {
		if ( ! unlink( $URI ) ) {
			$this->log_message( sprintf( __( 'Error: %s file was not able to be deleted.', 'components' ), $URI ) );
		}
	}

	/**
	 * Delete a directory of files.
	 */
	 function delete_directory( $directory ) {
		$files = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator( $directory, RecursiveDirectoryIterator::SKIP_DOTS ),
			RecursiveIteratorIterator::CHILD_FIRST );
		foreach ( $files as $fileinfo ) {
			$fname = $fileinfo->isDir() ? 'rmdir' : 'unlink';
			if ( ! call_user_func( $fname, $fileinfo->getRealPath() ) ) {
				$this->log_message( sprintf( __( 'Error: %1$s function was not able to be executed. Arguments were: %2$s.', 'components' ), $fname, $fileinfo->getRealPath() ) );
			}
		}
		return rmdir( $directory );
	}

	/**
	 * Track total downloads and type downloads.
	 */
	function do_tracking() {
		// Let's not fire stats on localhost.
		if ( 'components.underscores.me' !== $_SERVER['HTTP_HOST'] ) {
			return;
		}

		$types = $this->get_types();
		$output = null;

		// Track total downloads.
		$user_agent = 'regular';
		if ( '_sh' == $_SERVER['HTTP_USER_AGENT'] ) {
			$user_agent = '_sh';
		}

		// Track downloads of certain types.
		foreach ( $types as $type => $title ) {
			if ( $title == $this->selected_theme ) {
				$track_type = $this->selected_theme;
				// Remove the first word of types so we keep stats consistent with previous versions of generator.
				$output = preg_replace( '%^[a-z]+\s+(.+)$%i', '$1', $track_type );
				$track_type = $output;
				break;
			}
		}

		if ( isset( $track_type ) ) {
			wp_remote_get( add_query_arg( array(
				'v'									=> 'wpcom-no-pv',
				'x_component_total_downloads' => $user_agent,
				'x_component_downloads'		  => $track_type,
			), 'http://stats.wordpress.com/g.gif' ),
			array( 'blocking' => false ) );
		}
	}

	/**
	 * Let's set an expiration on the last download and get current time.
	 */
	function set_expiration_and_go() {
		// We only need to grab the file info of one type zip file since all files are created at once.
		$file_name = $this->build_dir . '/' . $this->repo_file_name;

		// Determine if we need to hook the init method
		$init = ! is_dir( $this->build_dir );
		if ( file_exists( $file_name ) ) {
			$file_time_stamp = date( filemtime( $file_name ) );
			$time = time();
			$expired = 1800; // Expire cache after 30 minutes.
			if ( $this->bypass_cache ) {
				$init = true; // Bypass the cache if debug filter is true
			} else {
				$init = $expired <= ( $time - $file_time_stamp ) ? true : false;
			}
		} else {
			// If no file exists run the init function anyway.
			$init = true;
		}

		if ( $init ) {
			$this->get_theme_components_init();
		}
	}

	/**
	 * Logs messages to debug.log in wp-content folder
	 */
	public function log_message ( $data )  {
		if ( $this->logging ) {
			if ( is_array( $data ) || is_object( $data ) ) {
				error_log( print_r( $data, true ) );
			} else {
				error_log( $data );
			}
		}
	}
}

if ( ! is_admin() ) {
	new Components_Generator_Plugin;
}
