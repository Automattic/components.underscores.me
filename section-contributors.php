<section id="contributors">
	<div class="wrap">
		<h2>Made with <?php echo file_get_contents( get_template_directory() . '/assets/img/love-and-labour.svg' ); ?> by</h2>
		<ul id="github-contributors">
			<?php foreach ( components_get_contributors() as $contributor ) : ?>
				<?php
					$name = '@' . $contributor->login;
					$contributions = sprintf( '%d %s', $contributor->contributions, _n( 'contribution', 'contributions', $contributor->contributions ) );
					$url = sprintf( 'http://github.com/%s', $contributor->login );
					$avatar_url = add_query_arg( 's', 280, $contributor->avatar_url );
					$avatar_url = add_query_arg( 'd', esc_url_raw( 'https://secure.gravatar.com/avatar/ad516503a11cd5ca435acc9bb6523536?s=280' ), $avatar_url );
				?>
				<li><a href="<?php echo esc_url( $url ); ?>"><img class="avatar" src="<?php echo esc_url( $avatar_url ); ?>" alt="" /><div class="contributor"><?php echo esc_html( $name ); ?><span><?php echo esc_html( $contributions ); ?></span></div></a></li>
			<?php endforeach; ?>
		</ul><!-- #team -->
	</div><!-- .wrap -->
</section><!-- #contribute -->