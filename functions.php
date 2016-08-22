<?php
/**
 * components functions and definitions
 *
 * @package components
 */

if ( ! function_exists( 'components_setup' ) ) {
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function components_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on components, use a find and replace
		 * to change 'components' to the name of your theme in all the template files
		 */
		load_theme_textdomain( 'components', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );
	}
	add_action( 'after_setup_theme', 'components_setup' );
} // components_setup


if ( ! function_exists( 'components_content_width' ) ) {
	/**
	 * Set the content width in pixels, based on the theme's design and stylesheet.
	 *
	 * Priority 0 to make it available to lower priority callbacks.
	 *
	 * @global int $content_width
	 */
	function components_content_width() {
		$GLOBALS['content_width'] = apply_filters( 'components_content_width', 640 );
	}
	add_action( 'after_setup_theme', 'components_content_width', 0 );
} // components_content_width

if ( ! function_exists( 'components_typekit' ) ) {
	/**
	 * Enqueue TypeKit fonts.
	 */
	function components_typekit() {
		wp_enqueue_script( 'components_typekit', '//use.typekit.net/adl7prd.js' );
	}
	add_action( 'wp_enqueue_scripts', 'components_typekit' );
} // components_typekit

if ( ! function_exists( 'components_typekit_inline' ) ) {
	/**
	 * Inject Typekit code into header
	 */
	function components_typekit_inline() {
		if ( wp_script_is( 'components_typekit', 'done' ) ) : ?>
			<script type="text/javascript">try{Typekit.load();}catch(e){}</script>
	<?php endif;
	}
	add_action( 'wp_head', 'components_typekit_inline' );
} // components_typekit_inline

if ( ! function_exists( 'components_javascript_detection' ) ) {
	/**
	 * Handles JavaScript detection.
	 *
	 * Adds a `js` class to the root `<html>` element when JavaScript is detected.
	 */
	function components_javascript_detection() {
		echo "<script>(function(html){html.className = html.className.replace(/\bno-js\b/,'js')})(document.documentElement);</script>\n";
	}
	add_action( 'wp_head', 'components_javascript_detection', 0 );
} // components_javascript_detection

if ( ! function_exists( 'components_scripts' ) ) {
	/**
	 * Enqueue scripts and styles.
	 */
	function components_scripts() {
		wp_enqueue_style( 'components-style', get_stylesheet_uri() );

		wp_enqueue_script( 'components', get_template_directory_uri() . '/assets/js/main.js', array( 'jquery' ), '20120206', true );

		wp_enqueue_script( 'components-skip-link-focus-fix', get_template_directory_uri() . '/assets/js/skip-link-focus-fix.js', array(), '20130115', true );

	}
	add_action( 'wp_enqueue_scripts', 'components_scripts' );
} // components_scripts

if ( ! function_exists( 'components_get_contributors' ) ) {
	/**
	 * Returns an array of contributors from Github.
	 */
	function components_get_contributors() {
		$transient_key = 'components_contributors';
		$contributors = get_transient( $transient_key );
		if ( false !== $contributors )
			return $contributors;
		$response = wp_remote_get( 'https://api.github.com/repos/Automattic/theme-components/contributors?per_page=100' );
		if ( is_wp_error( $response ) )
			return array();
		$contributors = json_decode( wp_remote_retrieve_body( $response ) );
		if ( ! is_array( $contributors ) )
			return array();
		set_transient( $transient_key, $contributors, HOUR_IN_SECONDS );
		return (array) $contributors;
	}
} // components_get_contributors

if ( ! function_exists( 'components_theme_types' ) ) {
	/**
	 * components_theme_types Returns an array of types
	 * @return $types Array
	 */
	function components_theme_types() {
		// Get the theme types
		require get_template_directory() . '/components/theme-types.php';
		if ( isset( $types ) && is_array( $types ) ) {
			// Randomise order of types so as not to favour any in particular
			shuffle( $types );
			return $types;
		} else {
			return array();
		}
	}
} // components_theme_types

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';

/**
 * Load Components Generator pseudo-plugin.
 */
require get_template_directory() . '/inc/components-generator.php';
