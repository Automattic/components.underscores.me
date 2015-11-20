<?php
/**
 * The front page template file.
 *
 * If the user has selected a static page for their homepage, this is what will
 * appear. Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 *
 * @package components
 */

get_header(); ?>

	<div id="primary" class="content-area">

		<section id="intro" role="main">
			<div class="wrap">
				<p>Components is a library of shareable, reusable patterns for WordPress themes. Instead of starting from scratch, mix and match to build your own custom starter theme. Think of it as your scaffolding. We build the foundation and wire everything up—you move in and paint the walls.</p>

				<h2>Is Components right for me?</h2>
				<p>Components is designed for WordPress theme enthusiasts of all levels. If you're just starting out, it'll get you started without needing to reinvent the wheel or build a lot of custom code. If you're an experienced theme developer, you'll find well-organised, easy-to use code that you can remix to your heart's delight!</p>
			</div><!-- .wrap -->
		</section><!-- #intro -->

		<section id="types" role="main">
			<div class="wrap">
				<h2>Let's get you a theme!</h2>
				<p>We've got some pre-fab packages for five common theme types. If that's not quite what you're after, you can [download everything instead.](#download-all)</p>
				<a href="#">Download All</a>

				<div class="theme-slider">
					<div class="theme-type">
						<div class="theme-image"></div>
						<div class="theme-text">
							<h3>Modern blog</h3>
							<p>You think the world could be a little bit less cluttered. You like clean lines, simple shapes, and xxx. You want the focus to be on your content, not a lot of other stuff. With a single-column layout, a large featured image, and a slide-out panel for widgets and navigation, a modern blog is the perfect fit for you.</p>
							<a href="#">Download Modern Blog Theme Package</a>
						</div>
					</div><!-- .theme-type -->

					<div class="theme-type">
						<div class="theme-image"></div>
						<div class="theme-text">
							<h3>Classic blog</h3>
							<p>Maybe you prefer the classics. Austen. Dickens. AC/DC. A classic blog is your perfect match. Great for food blogs, xxx, or anyone else who has a lot of content they'd like to display in a sidebar, a classic blog features widgets in sidebar, just like you remembered.</p>
							<a href="#">Download Classic Blog Theme Package</a>
						</div>
					</div><!-- .theme-type -->

					<div class="theme-type">
						<div class="theme-image"></div>
						<div class="theme-text">
							<h3>Magazine</h3>
							<p>Stop the presses! A magazine theme is ideal for showcasing content and images in a dynamic way. This theme features a front page template with a grid of featured images, and a two-column blog layout that relies on excerpts for some added interest.</p>
							<a href="#">Download Magazine Theme Package</a>
						</div>
					</div><!-- .theme-type -->

					<div class="theme-type">
						<div class="theme-image"></div>
						<div class="theme-text">
							<h3>Portfolio</h3>
							<p>If you're the creative type, this is the theme package for you. Image-focused, the portfolio package uses a custom portfolio post type to easily keep your portfolio items separate from your posts. It uses a gridded portfolio layout, a simple one-column blog template, and a large featured image header.</p>
							<a href="#">Download Portfolio Theme Package</a>
						</div>
					</div><!-- .theme-type -->

					<div class="theme-type">
						<div class="theme-image"></div>
						<div class="theme-text">
							<h3>Business</h3>
							<p>You've got a million things to worry about; don't let your theme be another. A business starter theme comes with a front page template featuring a custom header, prominent testimonials, and a custom content area. Testimonials can be used throughout the theme to add authenticity to your business.</p>
							<a href="#">Download Business Theme Package</a>
						</div>
					</div><!-- .theme-type -->
				</div><!-- .theme-slider -->

			</div><!-- .wrap -->
		</section><!-- #types -->

		<section id="extra-info" role="main">
			<div class="wrap">
				<h2>What's in the box?</h2>
				<p>Right now, we're offering five different base themes. A little later, you'll be able to mix up your own custom theme, on the fly. Every Components package comes with:</p>
				<ul>
					<li>Design-agnostic layout patterns</li>
					<li>Well-organised SCSS</li>
					<li>Mobile first layouts</li>
					<li>Mobile and desktop menus</li>
				</ul>
			</div><!-- .wrap -->
		</section><!-- #extra-info -->

	</div><!-- #primary -->

<?php get_footer(); ?>
