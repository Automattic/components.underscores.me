<?php
/**
 * Jetpack Compatibility File
 * See: https://jetpack.me/
 *
 * @package components
 */

/**
 * Add theme support for Infinite Scroll.
 * See: https://jetpack.me/support/infinite-scroll/
 *
 * @since components 1.0
 */
function components_jetpack_setup() {
	/**
	 * Add theme support for Jetpack portfolio
	 */
	add_theme_support( 'jetpack-portfolio' );

	/**
	 * Add theme support for Responsive Videos.
	 */
	add_theme_support( 'jetpack-responsive-videos' );

} // end function components_jetpack_setup

add_action( 'after_setup_theme', 'components_jetpack_setup' );
