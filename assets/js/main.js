
( function( $ ) {

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

	// Only show the cancel button if JS is working
	$( '.js .components-form-cancel' ).css('display','inline-block');

	// Now let's close the form on page load
	$( document ).ready( function() {
		$( '#generator' ).slideUp( 0 );
	} );

	// Define some variables
	 var form = $( '#generator' ).slideUp( 0 ).clone( true, true ),
	 	triggerElement;

	// The following happens when a user clicks 'download'
	$( '.js .download' ).on( 'click', function( e ) {
		e.preventDefault();

		// Find out what type we're downloading so's we can count that
		var type = $( this ).parents( '.theme-type' ).data( 'type' );

		// Also, get the button we clicked
		triggerElement = $( this );

		// Find out if we're on mobile. If yes, it'll change where we add the form.
		var mobile = false;
		if ( $( this ).parents( '.theme-type' ).css( 'float' ) == 'none' ) {
			mobile = true;
		}

		// Checking to see where we are on the page, and where the form currently is
		var thisRow = $( triggerElement ).parents( '.types-row' ),
		sameRow = $( thisRow ).next( '#generator' ).length;

		// If we're on desktop, and the form is already open and it's in the same place as we'd be adding it
		// we don't want to re-add it.
		if ( ! ( false == mobile && 1 == sameRow && 'true' == $( '#generator' ).attr( 'aria-expanded' ) ) ) {
			// since we've clicked 'download', let's close the generator if it exists
			$( '#generator' ).slideUp( 500, function() {
				// ... and get rid of it.
				$( '#generator' ).remove();

				// Now let's create a nice little function to update our Aria states
				function updateAria() {
					$( '.download' ).not( triggerElement ).attr( {
						'aria-expanded': 'false'
					} );

					$( triggerElement ).attr( {
						'aria-expanded': 'true'
					} );

					$( '.js #generator' ).attr( {
						'aria-expanded': 'true',
						'tabindex': '-1'
					} ).focus();
				}

				// And re-add the generator to the correct spot, depending whether we're on mobile or not.
				if( true == mobile ) {
					// If on mobile, just add that sucka!
					form.insertAfter( $( triggerElement ).parents( '.theme-type' ) ).slideDown( 500, function() {
						updateAria();
					} );
				} else {
					// If on desktop, we want to make sure the form isn't already where we want it to be.
					// Opening and closing it in the same spot is annoying ಠ_ಠ

					form.insertAfter( $( thisRow ) ).slideDown( 500, function() {
						updateAria();
					} );
				}
			} );
		}

		// Update the radio buttons to reflect current type
		if ( $( this ).is( '[data-type="base"]' ) ) {
			$( '#type-base' ).attr( 'checked', true );
		}

		if ( $( this ).is( '[data-type="blog-modern"]' ) ) {
			$( '#type-blog-modern' ).attr( 'checked', true );
		}

		if ( $( this ).is( '[data-type="blog-traditional"]' ) ) {
			$( '#type-classic' ).attr( 'checked', true );
		}

		if ( $( this ).is( '[data-type="magazine"]' ) ) {
			$( '#type-magazine' ).attr( 'checked', true );
		}

		if ( $( this ).is( '[data-type="portfolio"]' ) ) {
			$( '#type-portfolio' ).attr( 'checked', true );
		}

		if ( $( this ).is( '[data-type="business"]' ) ) {
			$( '#type-business' ).attr( 'checked', true );
		}

		/**
		 *Load the g.gif image in order to track downloads
		 */
		// Generate the URL for our tracking image, with proper parameters
		/*
		var imageURL = document.location.protocol + '//pixel.wp.com/b.gif?v=wpcom-no-pv&amp;x_component_downloads=' + type + '&amp;baba=' + Math.random();

		// Finally, append the image to our body
		$( 'body' ).append( '<img src="' + imageURL + '">' );
		*/
	} );


	// Cancel button.
	$( document ).on( 'click', '.js .components-form-cancel', function( e ) {

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

		$( '#generator' ).slideUp( 500, function() {
			// Return focus to the original 'build' button
			$( triggerElement ).focus();
		} );
	} );


	// The form has to be inserted in different spots depending on layout/resolution.
	// So let's make sure the form closes if the window's resized and passes over that threshold
	// Otherwise it could end up sitting in a wacky spot
	var windowWidth = $(window).width();
	var timeOut;
	var tabletWidth = 1024;

	function closeIt() {
		windowWidth = $( window ).width();
		if ( windowWidth > tabletWidth ) {
			if ( windowWidth < tabletWidth ) {
				windowWidth = tabletWidth;
			}
		}

		// Close the generator
		$( '#generator' ).slideUp( 0 );

		// Change our ARIA states
		$( '.js .download' ).attr( {
			'aria-expanded': 'false'
		} );

		$( '.js #generator' ).attr( {
			'aria-expanded': 'false'
		} );

		$( '.js #generator' ).removeAttr(
			'tabindex'
		);
	}

	$( window ).resize( function() {
		var resWidth = $( window ).width();
		clearTimeout( timeOut );
		if ( ( windowWidth > tabletWidth && resWidth < tabletWidth ) || ( windowWidth < tabletWidth && resWidth > tabletWidth ) ) {
			timeOut = setTimeout( closeIt, 100 );
		}
	} );

} )( jQuery );
