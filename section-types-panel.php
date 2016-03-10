<section id="theme-types-panel" class="panel">

	<?php
	$type_sections = array( 'base', 'types' );
	foreach ( $type_sections as $type_section ) {
		get_template_part( 'section', $type_section );
	}
	?>

	<?php do_action( 'components_generator_print_form' ); ?>

</section><!-- #theme-types-panel -->