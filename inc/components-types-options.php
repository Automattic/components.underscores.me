<?php
/**
 * Plugin Name: Components Types Options
 * Description: Adds extra fields to portfolio post type for adding theme type images, and allows SVGs in the media library.
 */

class Components_Types_Options {

	function __construct() {
		/**
		 * Add Custom field support to portfolio post types
		 */
		function components_add_custom_fields_to_portfolio() {
			add_post_type_support( 'jetpack-portfolio', 'custom-fields' );
		}
		add_action( 'init', 'components_add_custom_fields_to_portfolio' );

		/**
		 * Adds two default fields to Custom Fields - one for the robot SVG path and one for the mockup SVG path
		 */
		function components_set_default_custom_fields( $post_id ) {
			if ( get_post_type( $post_id ) == 'jetpack-portfolio' ) {
				add_post_meta( $post_id, 'robot_svg', '', true );
				add_post_meta( $post_id, 'mockup_svg', '', true );
			}
			return true;
		}
		add_action('wp_insert_post', 'components_set_default_custom_fields', 1 );

		/**
		 * Allow SVGs in Media Uploader
		 */
		function components_mime_types($mimes) {
			$mimes['svg'] = 'image/svg+xml';
			return $mimes;
		}
		add_filter('upload_mimes', 'components_mime_types' );

	}
}
new Components_Types_Options;
