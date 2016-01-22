( function( $ ) {

	$( document ).ready( function() {
		// Close the form on page load
		//$( '#generator' ).addClass( 'closed' );
	} );

	var form, triggerElement;

	// Let's set up our ARIA for the Generator controls.
	// We need these only if JavaScript fires.
	$( '.js .download' ).attr( {
		'role': 'button',
		'aria-controls': 'generator',
		'aria-expanded': 'false'
	} );

	$( '.js #generator' ).attr( {
		'aria-expanded': 'false'
	} );

	$( '.js .components-form-cancel' ).css('display','inline-block');

	$( '.js .download' ).on( 'click', function( e ) {
		e.preventDefault();

		// Change our ARIA states.
		$( '.js .download' ).attr( {
			'aria-expanded': 'true'
		} );

		$( '.js #generator' ).attr( {
			'aria-expanded': 'true',
			'tabindex': '-1'
		} );

		// Wait a bit before shifting focus since our generator form moves in the source.
		setTimeout( function() {
			$( '.js #generator' ).focus();
		}, 25 );

		if ( $( this ).is( '[data-type="base"]' ) ) {
			$( '#type-base' ).attr( 'checked', true );
			console.log( 'Base!' );
		}

		if ( $( this ).is( '[data-type="blog-modern"]' ) ) {
			$( '#type-blog-modern' ).attr( 'checked', true );
			console.log( 'Modern!' );
		}

		if ( $( this ).is( '[data-type="blog-traditional"]' ) ) {
			$( '#type-classic' ).attr( 'checked', true );
			console.log( 'Classic!' );
		}

		if ( $( this ).is( '[data-type="magazine"]' ) ) {
			$( '#type-magazine' ).attr( 'checked', true );
			console.log( 'Magazine!' );
		}

		if ( $( this ).is( '[data-type="portfolio"]' ) ) {
			$( '#type-portfolio' ).attr( 'checked', true );
			console.log( 'Portfolio!' );
		}

		if ( $( this ).is( '[data-type="business"]' ) ) {
			$( '#type-business' ).attr( 'checked', true );
			console.log( 'Business!' );
		}

		// Find out what type we're downloading so's we can count that
		var type = $( this ).parents( '.theme-type' ).data( 'type' );
		var mobile = false;
		if ( $( this ).parents( '.theme-type' ).css( 'float' ) == 'none' ) {
			mobile = true;
		}

		form = $( '#generator' ).detach();

		// if we're using the mobile layout, we want to move the form immediately below the current theme
		if ( true == mobile ) {
			form.insertAfter( $( this ).parents( '.theme-type' ) );

		} else {
			// otherwise, we want to move the form under the 'type row'
			form.insertAfter( $( this ).parents( '.types-row' ) );
		}

		// Which button did we click to open generator form?
		triggerElement = $( this );

		/**
		 *Load the g.gif image in order to track downloads
		 */
		// Generate the URL for our tracking image, with proper parameters
		var imageURL = document.location.protocol + '//pixel.wp.com/b.gif?v=wpcom-no-pv&amp;x_component_downloads=' + type + '&amp;baba=' + Math.random();

		// Finally, append the image to our body
		$( 'body' ).append( '<img src="' + imageURL + '">' );
	} );

	// Cancel button.
	$( '.js .components-form-cancel' ).on( 'click', function( e ) {

		// Uncheck our theme choice.
		$( 'input[name="theme-type"]' ).attr( 'checked', false );

		// Change our ARIA states.
		$( '.js .download' ).attr( {
			'aria-expanded': 'false'
		} );

		$( '.js #generator' ).attr( {
			'aria-expanded': 'false'
		} );

		$( '.js #generator' ).removeAttr(
			'tabindex'
		);

		// Wait a bit before shifting focus back to button since items move in the source.
		setTimeout( function() {
			$( triggerElement ).focus();
		}, 25 );

	} );

} )( jQuery );
