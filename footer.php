<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package components
 */

?>

	</div><!-- #content -->

	<footer id="colophon" class="site-footer" role="contentinfo">

		<div class="site-info">
	<a href="<?php echo esc_url( __( 'http://wordpress.org/', 'components' ) ); ?>"><?php printf( esc_html__( 'Proudly powered by %s', 'components' ), 'WordPress' ); ?></a>
	<span class="sep"> | </span>
	<?php printf( esc_html__( 'Theme: %1$s by %2$s.', 'components' ), 'components', '<a href="http://wordpress.com" rel="designer">Automattic</a>' ); ?>
</div><!-- .site-info -->

	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
