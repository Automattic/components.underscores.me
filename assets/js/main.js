( function( $ ) {

	$( document ).ready( function() {
		// Close the form on page load
		//$( '#generator' ).addClass( 'closed' );
	} );

	var form;
	$( '.download' ).on( 'click', function(e) {
		e.preventDefault();

		// Find out what type we're downloading so's we can count that
		var type = $( this ).parents( '.theme-type' ).data( 'type' );
		var mobile = false;
		if( $( this ).parents( '.theme-type' ).css( 'float' ) == 'none' ) {
			mobile = true;
		}

		form = $( '#generator' ).detach();

		// if we're using the mobile layout, we want to move the form immediately below the current theme
		if( true == mobile ) {
			form.insertAfter( $( this ).parents( '.theme-type' ) );

		} else {
			// otherwise, we want to move the form under the 'type row'
			form.insertAfter( $( this ).parents( '.types-row' ) );
		}


		/**
		 *Load the g.gif image in order to track downloads
		 */
		// Generate the URL for our tracking image, with proper parameters
		var imageURL = document.location.protocol + '//pixel.wp.com/b.gif?v=wpcom-no-pv&amp;x_component_downloads=' + type + '&amp;baba=' + Math.random();

		// Finally, append the image to our body
		$( 'body' ).append( '<img src="' + imageURL + '">' );
	} );

} )( jQuery );
