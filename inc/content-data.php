<?php
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

/**
 * Outputs the default content data for the intro message
 */
function get_content_intro() {
	$intro_data = array();
	$intro_data['title'] = get_bloginfo( 'description', 'display' );
	$intro_data['content'] = '<p>Components is a library of <strong>shareable, reusable patterns</strong> for WordPress themes. Instead of starting from scratch, mix and match from a collection of pre-made components to build your own <strong>custom starter theme</strong>.</p>
							<p>If you&rsquo;re just starting out, it&rsquo;ll get you <strong>booted up</strong> without needing to reinvent the wheel or write a lot of custom code. If you&rsquo;re an experienced theme developer, you&rsquo;ll find <strong>well-organized, easy-to use code</strong> that you can remix to your heart&rsquo;s delight!</p>';
	return $intro_data;
}

/**
 * Outputs the default content data for the types section
 */
function get_content_types( $types_category = 'all' ) {
	$types_data = array();
	if ( 'hero' === $types_category ) {
		$types_data = array(
		  'modern' => array (
		    'title'    => esc_html__( 'Just the basics, please', 'components' ),
		    'filename' => 'base',
		    'text'     => esc_html__( 'Want to concoct your own starter theme? Don&rsquo;t need any bells or whistles? Our base package is for you.', 'components' ),
		  )
		);
	} else {
		$types_data = array(
			  'modern' => array (
			    'title'    => esc_html__( 'Modern Blog', 'components' ),
			    'filename' => 'blog-modern',
			    'text'     => esc_html__( 'You think the world could be a little bit less cluttered. You like clean lines, simple shapes, and contemporary design. You want the focus to be on your content, not a lot of other stuff. With a single-column layout, a large featured image, and a slide-out panel for widgets and navigation, a modern blog is the perfect fit.', 'components' ),
			  ),

			  'classic' => array (
			    'title'    => esc_html__( 'Classic Blog', 'components' ),
			    'filename' => 'blog-classic',
			    'text'     => esc_html__( 'Maybe you prefer the classics. Austen. Dickens. AC/DC. A classic blog is your perfect match. Great for food blogs, schools, or anyone else who has a lot of content to display in a sidebar, a classic blog features widgets in sidebar, just like you remember.', 'components' ),
			  ),

			  'magazine' => array (
			    'title'    => esc_html__( 'Magazine', 'components' ),
			    'filename' => 'magazine',
			    'text'     => esc_html__( 'Stop the press! A magazine theme is ideal for showcasing content and images in a dynamic way. This theme features a front-page template with a grid of featured images, and a two-column blog layout that displays excerpts for added interest.', 'components' ),
			  ),

			  'portfolio' => array (
			    'title'    => esc_html__( 'Portfolio', 'components' ),
			    'filename' => 'portfolio',
			    'text'     => esc_html__( 'If you&rsquo;re the creative type, this is the theme package for you. Image-focused, the portfolio layout uses a portfolio custom post type to easily keep your portfolio items separate from regular posts. It features a gridded portfolio layout, a simple one-column blog template, and a large featured image header.', 'components' ),
			  ),

			  'business' => array (
			    'title'    => esc_html__( 'Business', 'components' ),
			    'filename' => 'business',
			    'text'     => esc_html__( 'You&rsquo;ve got a million things to worry about; don&rsquo;t let your theme be another. A business starter theme comes with a front page template featuring a custom header, prominent testimonials, and a custom content area. Testimonials can be displayed throughout the theme to add authenticity to your business.', 'components' ),
			  ),
		);
	}

	return $types_data;
}

/**
 * Outputs the default content data for the extra info section
 */
function get_content_extra_info() {
	$extra_info_data = array(	array( 	'title' 	=> 	'What&rsquo;s in the box?',
										'content' 	=> 	'<p>Every Components package comes with:</p>
														<ul>
															<li>Design-agnostic layout patterns</li>
															<li>Well-organized SCSS</li>
															<li>Mobile-first layouts</li>
															<li>Mobile and desktop menus</li>
															<li>A simple base</li>
														</ul>'
										),
								array( 	'title' 	=> 'Want to contribute?',
										'content' 	=> '<p>Components is a new project, and we&rsquo;re looking for your input! Have a pattern to share? Want to add a new feature? Found a bug in the code? Head over to the <a href="https://github.com/Automattic/theme-components">GitHub repo</a>, check out the <a href="https://github.com/Automattic/theme-components/blob/master/CONTRIBUTING.md">contributor guidelines</a>, and get involved!</p>'
										)
								);
	return $extra_info_data;
}