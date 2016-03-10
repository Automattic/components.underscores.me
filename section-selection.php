<section id="selection">

	<div class="types">
		<div class="svg-container">
			<?php echo file_get_contents( get_template_directory() . '/assets/img/type-cart.svg' ); ?>
		</div>
		<?php $selection_data = get_content_selection('types'); ?>
		<h2><?php echo $selection_data['title']; ?></h2>
		<?php echo $selection_data['content']; ?>
		<a href="#theme-types-panel" class="toggle button">Choose a Type</a>
	</div>
	<div class="custom">
		<div class="svg-container">
			<?php echo file_get_contents( get_template_directory() . '/assets/img/toolbox.svg' ); ?>
		</div>
		<?php $selection_data = get_content_selection('custom'); ?>
		<h2><?php echo $selection_data['title']; ?></h2>
		<?php echo $selection_data['content']; ?>
		<a href="#custom-build-panel" class="toggle button">Configure</a>
	</div>
</section><!-- #selection -->