( function( $ ) {

	// Load the g.gif image in order to track downloads
	$( '.download' ).on( 'click', function(e) {
		// Find out what type we're downloading so's we can count that
		var type = $( this ).parents( '.theme-type' ).data( 'type' );

		// Generate the URL for our tracking image, with proper parameters
		var imageURL = document.location.protocol + '//pixel.wp.com/b.gif?v=wpcom-no-pv&amp;x_component_downloads=' + type + '&amp;baba=' + Math.random();

		// Finally, append the image to our body
		$( 'body' ).append( '<img src="' + imageURL + '">' );
	} );

} )( jQuery );
