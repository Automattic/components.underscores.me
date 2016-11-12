<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package components
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="hfeed site">
	<a class="skip-link screen-reader-text" href="<?php echo esc_url( __( '#content', 'components' ) ); ?>"><?php esc_html_e( 'Skip to content', 'components' ); ?></a>


	<?php if ( is_front_page() ) { ?>

		<header id="masthead" class="site-header" role="banner">
			<div class="top-robot-container">
				<?php echo file_get_contents( get_template_directory() . '/assets/img/robot-plain.svg' ); ?>
			</div>

			<?php
			/* Commenting out the menu, since it's not gonna be used in initial version of the site.

			<nav id="site-navigation" class="main-navigation" role="navigation">
				<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false"><?php esc_html_e( 'Primary Menu', 'components' ); ?></button>
				<?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_id' => 'primary-menu' ) ); ?>
			</nav><!-- #site-navigation -->
			*/
			?>

			<div class="wrap">
				<div class="stretchy-pipe stretchy-pipe-left"></div>
				<div class="stretchy-pipe stretchy-pipe-center"></div>
				<div class="stretchy-pipe stretchy-pipe-right"></div>
				<?php echo file_get_contents( get_template_directory() . '/assets/img/pipe-main-left.svg' ); ?>
				<?php echo file_get_contents( get_template_directory() . '/assets/img/pipe-main-right.svg' ); ?>
				<?php echo file_get_contents( get_template_directory() . '/assets/img/tap.svg' ); ?>
				<?php echo file_get_contents( get_template_directory() . '/assets/img/tap-2.svg' ); ?>
				<div class="content-wrapper">
					<div class="intro-content">
						<div class="site-branding">
							<!-- Logo acts as an image -->
							<?php echo file_get_contents( get_template_directory() . '/assets/img/components.svg' ); ?>
							<!-- Hidden h1 for screen readers -->
							<h1 class="site-title screen-reader-text"><?php bloginfo( 'name' ); ?></h1>
							<?php $intro_data = get_content_intro();
							if ( $intro_data['title'] || is_customize_preview() ) : ?>
								<h2 class="site-description"><?php echo $intro_data['title']; ?></h2>
							<?php endif; ?>
						</div><!-- .site-branding -->
						<div id="intro">
							<?php echo $intro_data['content']; ?>
						</div><!-- #intro -->
					</div><!-- .intro-content -->
				</div><!-- .content-wrapper -->
			</div><!-- .wrap -->
		</header><!-- #masthead -->
	<?php } ?>

	<div id="content" class="site-content">
