<?php
/**
 * This file adds functionality to make the different theme pattern types.
 */

/**
 * This downloads a file at a URL.
 */
function theme_pattern_library_download_file( $URI, $filename ) {
	$fp = fopen( $filename, 'w' );
	$ch = curl_init( $URI );
	curl_setopt( $ch, CURLOPT_FILE, $fp );
	$data = curl_exec( $ch );
	curl_close( $ch );
	fclose( $fp );
}

/**
 * This deletes a file.
 */
function theme_pattern_library_delete_file( $URI ) {
	unlink( $URI );
}

/**
 * Delete a directory of files.
 */
function theme_pattern_library_delete_directory( $directory ) {
	$files = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator( $directory, RecursiveDirectoryIterator::SKIP_DOTS ),
		RecursiveIteratorIterator::CHILD_FIRST
	);
	foreach ( $files as $fileinfo ) {
		$todo = ( $fileinfo->isDir() ? 'rmdir' : 'unlink' );
		$todo( $fileinfo->getRealPath() );
	}
	if ( rmdir( $directory ) ):
		return true;
	else:
		return false;
	endif;
}

/**
 * Copies a directory of files.
 */
function theme_pattern_library_copy_directory( $src, $dst ) {
	$dir = opendir( $src );
	if ( ! file_exists( $dst ) ) {
		@mkdir( $dst );
	}
	while( false !== ( $file = readdir( $dir ) ) ) {
		if ( ( $file != '.' ) && ( $file != '..' ) ) {
			if ( is_dir( $src . '/' . $file ) ) {
				theme_pattern_library_copy_directory( $src . '/' . $file, $dst . '/' . $file );
			}
			else {
				copy( $src . '/' . $file, $dst . '/' . $file );
			}
		}
	}
	closedir( $dir );
}

/**
 * This gets our zip from the Github repo.
 */
function theme_pattern_library_get_download( $branch, $destination ) {
	// Our repo name.
	$repo = 'theme-pattern-library';
	// Our file name.
	$repofilename = $repo . '-' . $branch . '.zip';
	// Grab the file.
	theme_pattern_library_download_file( 'https://codeload.github.com/Automattic/theme-pattern-library/zip/' . $branch, $repofilename );
	// Copy the file to its new directory.
	copy( $_SERVER[ 'DOCUMENT_ROOT' ] . '/' . $repofilename, $destination . $repofilename );
	// Delete the old file, we don't need it.
	theme_pattern_library_delete_file( $_SERVER[ 'DOCUMENT_ROOT' ] . '/' . $repofilename );
}

/**
 * This unzips our zip from the Github repo.
 */
function theme_pattern_library_unzip( $zipfile ) {
	$path = pathinfo( realpath( $zipfile ), PATHINFO_DIRNAME );
	$zip = new ZipArchive;
	$res = $zip->open( $zipfile );
	if ( $res === TRUE ) {
		// Extract it to the path we determined above.
		$zip->extractTo( $path );
		$zip->close();
		theme_pattern_library_delete_file( $zipfile );
	} else {
		exit( "Oh no! I couldn't open the zip." );
	}
}

/**
 * Creates a compressed zip file
 * Reference: https://davidwalsh.name/create-zip-php
 *
 * @param string $type Type for project starter type..
 * @return zip file
 */
function theme_pattern_library_create_zip( $directory = '', $destination = '', $overwrite = false ) {
	// If the zip file already exists and overwrite is false, return false
	if ( file_exists( $directory . '.zip' ) && ! $overwrite ) { return false; }
	// Get the real path for our folder.
	$rootPath = realpath( $directory );
	// Initialize archive object
	$zip = new ZipArchive();
	$zip->open( $directory . '.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE );
	// Create recursive directory iterator to get all the files.
	$files = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator($rootPath),
		RecursiveIteratorIterator::LEAVES_ONLY
	);

	foreach ( $files as $name => $file ) {
		// Skip directories (they would be added automatically)
		if ( ! $file->isDir() ) {
			// Get real and relative path for current file
			$filePath = $file->getRealPath();
			$relativePath = substr( $filePath, strlen( $rootPath ) + 1 );
			// Add current file to archive
			$zip->addFile( $filePath, $relativePath );
		}
	}
	// Debug
	// echo 'The zip archive contains ', $zip->numFiles, ' files with a status of ', $zip->status;
	//echo '<br>';
	//var_dump( $files );
	// Close the zip
	$zip->close();

	// Check to make sure the file exists
	//return file_exists( $destination );
	if ( file_exists( $destination ) ) {
		return $destination;
	} else {
		echo 'Something went wrong and your zip was not created.';
	}
}

/**
 * Let's set our types.
 */
define( 'BASE', 'base', true );
define( 'BLOGMODERN', 'blog-modern', true );
define( 'BLOGTRADITIONAL', 'blog-traditional', true );
define( 'BUSINESS', 'business', true );
define( 'MAGAZINE', 'magazine', true );
define( 'PORTFOLIO', 'portfolio', true );


function theme_pattern_library_make_working_copies() {
	$parent_directory = get_template_directory() . '/downloads/';
	// Make a directory and copy the file to its new directory.
	theme_pattern_library_copy_directory( $parent_directory = get_template_directory() . '/downloads/theme-pattern-library-master', get_template_directory() . '/downloads/' . BASE );
	// Make a directory and copy the file to its new directory.
	theme_pattern_library_copy_directory( $parent_directory = get_template_directory() . '/downloads/theme-pattern-library-master', get_template_directory() . '/downloads/' . BLOGMODERN );
	// Make a directory and copy the file to its new directory.
	theme_pattern_library_copy_directory( $parent_directory = get_template_directory() . '/downloads/theme-pattern-library-master', get_template_directory() . '/downloads/' . BLOGTRADITIONAL );
	// Make a directory and copy the file to its new directory.
	theme_pattern_library_copy_directory( $parent_directory = get_template_directory() . '/downloads/theme-pattern-library-master', get_template_directory() . '/downloads/' . BUSINESS );
	// Make a directory and copy the file to its new directory.
	theme_pattern_library_copy_directory( $parent_directory = get_template_directory() . '/downloads/theme-pattern-library-master', get_template_directory() . '/downloads/' . MAGAZINE );
	// Make a directory and copy the file to its new directory.
	theme_pattern_library_copy_directory( $parent_directory = get_template_directory() . '/downloads/theme-pattern-library-master', get_template_directory() . '/downloads/' . PORTFOLIO );
}

function theme_pattern_library_make_base() {
	// Set the current directory.
	$current_directory = get_template_directory() . '/downloads/base/';
	// Delete the /types/ directory.
	theme_pattern_library_delete_directory( $current_directory . 'types' );
}

function theme_pattern_library_make_blogmodern() {
	// Set the current directory.
	$current_directory = get_template_directory() . '/downloads/blog-modern/';
	// Move the main theme files that need moving.
	copy( $current_directory . 'types/blog-modern/header.php', $current_directory . 'header.php' );
	copy( $current_directory . 'types/blog-modern/functions.php', $current_directory . 'functions.php' );
	copy( $current_directory . 'types/blog-modern/inc/custom-header.php', $current_directory . 'inc/custom-header.php' );
	// And yet, more files, this time the template parts.
	copy( $current_directory . 'types/blog-modern/components/content-none.php', $current_directory . 'components/content-none/content-none.php' );
	copy( $current_directory . 'types/blog-modern/components/content-page.php', $current_directory . 'components/content-page/content-page.php' );
	copy( $current_directory . 'types/blog-modern/components/content-search.php', $current_directory . 'components/content-search/content-search.php' );
	copy( $current_directory . 'types/blog-modern/components/content-single.php', $current_directory . 'components/content-single/content-single.php' );
	copy( $current_directory . 'types/blog-modern/components/content.php', $current_directory . 'components/content/content.php' );
	// Move the JS files.
	copy( $current_directory . 'types/blog-modern/assets/js/thememodern.js', $current_directory . 'assets/js/thememodern.js' );
	// And finally, stylesheets.
	// Make the missing directory we need.
	if ( ! file_exists( $current_directory . 'assets/stylesheets/components' ) ) {
		mkdir( $current_directory . 'assets/stylesheets/components', 0755 );
	}
	copy( $current_directory . 'types/blog-modern/assets/stylesheets/components/_slidepanel.scss', $current_directory . 'assets/stylesheets/components/_slidepanel.scss' );
	copy( $current_directory . 'types/blog-modern/assets/stylesheets/layout/_content.scss', $current_directory . 'assets/stylesheets/layout/_content.scss' );
	copy( $current_directory . 'types/blog-modern/assets/stylesheets/layout/_structure.scss', $current_directory . 'assets/stylesheets/layout/_structure.scss' );
	copy( $current_directory . 'types/blog-modern/assets/stylesheets/shared/_animation.scss', $current_directory . 'assets/stylesheets/shared/_animation.scss' );
	copy( $current_directory . 'types/blog-modern/assets/stylesheets/shared/_navigation.scss', $current_directory . 'assets/stylesheets/shared/_navigation.scss' );
	copy( $current_directory . 'types/blog-modern/assets/stylesheets/shared/_queries.scss', $current_directory . 'assets/stylesheets/shared/_queries.scss' );
	// Oh, one more! Our main style.scss.
	copy( $current_directory . 'types/blog-modern/assets/stylesheets/style.scss', $current_directory . 'assets/stylesheets/style.scss' );
	// Delete the /types/ directory.
	theme_pattern_library_delete_directory( $current_directory . 'types' );
}

function theme_pattern_library_make_blogtraditional() {
	// Set the current directory.
	$current_directory = get_template_directory() . '/downloads/blog-traditional/';
	// Move the main theme files that need moving.
	copy( $current_directory . 'types/blog-traditional/header.php', $current_directory . 'header.php' );
	copy( $current_directory . 'types/blog-traditional/sidebar.php', $current_directory . 'sidebar.php' );
	// And finally, stylesheets.
	copy( $current_directory . 'types/blog-traditional/assets/stylesheets/shared/_queries.scss', $current_directory . 'assets/stylesheets/shared/_queries.scss' );
	copy( $current_directory . 'types/blog-traditional/assets/stylesheets/variables/_structure.scss', $current_directory . 'assets/stylesheets/variables/_structure.scss' );
	// Delete the /types/ directory.
	theme_pattern_library_delete_directory( $current_directory . 'types' );
}

function theme_pattern_library_make_business() {
	// Set the current directory.
	$current_directory = get_template_directory() . '/downloads/business/';
	// Move the main theme files that need moving.
	copy( $current_directory . 'types/business/header.php', $current_directory . 'header.php' );
	copy( $current_directory . 'types/business/functions.php', $current_directory . 'functions.php' );
	copy( $current_directory . 'types/business/inc/jetpack.php', $current_directory . 'inc/jetpack.php' );
	// Make the missing directory we need.
	if ( ! file_exists( $current_directory . 'page-templates' ) ) {
		mkdir( $current_directory . 'page-templates', 0755 );
	}
	copy( $current_directory . 'types/business/page-templates/template-front.php', $current_directory . 'page-templates/template-front.php' );
	// Make the missing directory we need.
	if ( ! file_exists( $current_directory . 'components/content-hero' ) ) {
		mkdir( $current_directory . 'components/content-hero', 0755 );
	}
	copy( $current_directory . 'types/business/components/content-hero/content-hero.php', $current_directory . 'components/content-hero/content-hero.php' );
	// Make the missing directory we need.
	if ( ! file_exists( $current_directory . 'components/content-testimonial' ) ) {
		mkdir( $current_directory . 'components/content-testimonial', 0755 );
	}
	copy( $current_directory . 'types/business/components/content-testimonial/content-testimonial.php', $current_directory . 'components/content-testimonial/content-testimonial.php' );
	// Make the missing directory we need.
	if ( ! file_exists( $current_directory . 'components/testimonials' ) ) {
		mkdir( $current_directory . 'components/testimonials', 0755 );
	}
	copy( $current_directory . 'types/business/components/testimonials/testimonials.php', $current_directory . 'components/testimonials/testimonials.php' );
	// Delete the /types/ directory.
	theme_pattern_library_delete_directory( $current_directory . 'types' );
}

function theme_pattern_library_make_magazine() {
	// Set the current directory.
	$current_directory = get_template_directory() . '/downloads/magazine/';
	// Move the main theme files that need moving.
	copy( $current_directory . 'types/magazine/index.php', $current_directory . 'index.php' );
	copy( $current_directory . 'types/magazine/inc/jetpack.php', $current_directory . 'inc/jetpack.php' );
	// Make the missing directory we need.
	if ( ! file_exists( $current_directory . 'components/featured' ) ) {
		mkdir( $current_directory . 'components/featured', 0755 );
	}
	copy( $current_directory . 'types/magazine/components/featured.php', $current_directory . 'components/featured/featured.php' );
	// Make the missing directory we need.
	if ( ! file_exists( $current_directory . 'components/content-featured' ) ) {
		mkdir( $current_directory . 'components/content-featured', 0755 );
	}
	copy( $current_directory . 'types/magazine/components/content-featured.php', $current_directory . 'components/content-featured/content-featured.php' );
	// And finally, stylesheets.
	copy( $current_directory . 'types/magazine/assets/stylesheets/shared/_queries.scss', $current_directory . 'assets/stylesheets/shared/_queries.scss' );
	copy( $current_directory . 'types/magazine/assets/stylesheets/variables/_structure.scss', $current_directory . 'assets/stylesheets/variables/_structure.scss' );
	// Delete the /types/ directory.
	theme_pattern_library_delete_directory( $current_directory . 'types' );
}

function theme_pattern_library_make_portfolio() {
	// Set the current directory.
	$current_directory = get_template_directory() . '/downloads/portfolio/';
	// Move the main theme files that need moving.
	copy( $current_directory . 'types/portfolio/header.php', $current_directory . 'header.php' );
	copy( $current_directory . 'types/portfolio/functions.php', $current_directory . 'functions.php' );
	copy( $current_directory . 'types/portfolio/single-jetpack-portfolio.php', $current_directory . 'single-jetpack-portfolio.php' );
	// Make the missing directory we need.
	if ( ! file_exists( $current_directory . 'page-templates' ) ) {
		mkdir( $current_directory . 'page-templates', 0755 );
	}
	copy( $current_directory . 'types/portfolio/template-front.php', $current_directory . 'page-templates/template-front.php' );
	copy( $current_directory . 'types/portfolio/inc/jetpack.php', $current_directory . 'inc/jetpack.php' );
	// Make the missing directory we need.
	if ( ! file_exists( $current_directory . 'components/content-portfolio-single' ) ) {
		mkdir( $current_directory . 'components/content-portfolio-single', 0755 );
	}
	copy( $current_directory . 'types/portfolio/components/content-portfolio-single.php', $current_directory . 'components/content-portfolio-single/content-portfolio-single.php' );
	// Make the missing directory we need.
	if ( ! file_exists( $current_directory . 'components/content-portfolio' ) ) {
		mkdir( $current_directory . 'components/content-portfolio', 0755 );
	}
	copy( $current_directory . 'types/portfolio/components/content-portfolio.php', $current_directory . 'components/content-portfolio/content-portfolio.php' );
	// Make the missing directory we need.
	if ( ! file_exists( $current_directory . 'components/portfolio' ) ) {
		mkdir( $current_directory . 'components/portfolio', 0755 );
	}
	copy( $current_directory . 'types/portfolio/components/portfolio.php', $current_directory . 'components/portfolio/portfolio.php' );
	// And finally, stylesheets.
	// Make the missing directory we need.
	if ( ! file_exists( $current_directory . 'assets/stylesheets/components' ) ) {
		mkdir( $current_directory . 'assets/stylesheets/components', 0755 );
	}
	copy( $current_directory . 'types/portfolio/assets/stylesheets/components/_portfolio.scss', $current_directory . 'assets/stylesheets/components/_portfolio.scss' );
	copy( $current_directory . 'types/portfolio/assets/stylesheets/layout/_structure.scss', $current_directory . 'assets/stylesheets/layout/_structure.scss' );
	copy( $current_directory . 'types/portfolio/assets/stylesheets/shared/_queries.scss', $current_directory . 'assets/stylesheets/shared/_queries.scss' );
	// Delete the /types/ directory.
	theme_pattern_library_delete_directory( $current_directory . 'types' );
}

function theme_pattern_library_create_type_zips() {
	$downloads = get_template_directory() . '/downloads/';
	// Base
	$base = get_template_directory() . '/downloads/' . BASE;
	theme_pattern_library_create_zip( $base, $downloads, true );
	// Blog Modern
	$blogModern = get_template_directory() . '/downloads/' . BLOGMODERN;
	theme_pattern_library_create_zip( $blogModern, $downloads, true );
	// Blog Traditional
	$blogTraditional = get_template_directory() . '/downloads/' . BLOGTRADITIONAL;
	theme_pattern_library_create_zip( $blogTraditional, $downloads, true );
	// Business Traditional
	$business = get_template_directory() . '/downloads/' . BUSINESS;
	theme_pattern_library_create_zip( $business, $downloads, true );
	// Magazine Traditional
	$magazine = get_template_directory() . '/downloads/' . MAGAZINE;
	theme_pattern_library_create_zip( $magazine, $downloads, true );
	// Portfolio Traditional
	$portfolio = get_template_directory() . '/downloads/' . PORTFOLIO;
	theme_pattern_library_create_zip( $portfolio, $downloads, true );

}

/**
 * Let's fire the needed functions.
 */
function theme_pattern_library_init() {
	theme_pattern_library_get_download( 'master', get_template_directory() . '/downloads/' );
	theme_pattern_library_unzip( get_template_directory() . '/downloads/' . 'theme-pattern-library-master.zip' );
	theme_pattern_library_make_working_copies();
	theme_pattern_library_make_base();
	theme_pattern_library_make_blogmodern();
	theme_pattern_library_make_business();
	theme_pattern_library_make_magazine();
	theme_pattern_library_make_portfolio();
	theme_pattern_library_create_type_zips();
}
// Let's fire the function as late as we can.
add_action( 'wp_footer', 'theme_pattern_library_init' );
