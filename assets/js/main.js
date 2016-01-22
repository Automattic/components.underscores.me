
( function( $ ) {

	$( document ).ready( function() {
		// Close the form on page load
		$( '#generator' ).slideUp( 0 );
	} );

	// Define some variables
	 var form = $( '#generator' ).slideUp( 0 ).clone( true ),
	 	triggerElement;

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

		// Find out what type we're downloading so's we can count that
		var type = $( this ).parents( '.theme-type' ).data( 'type' );

		// Which button did we click to open generator form?
		triggerElement = $( this );

		// Find out if we're on mobile. If yes, it'll change where we add the form.
		var mobile = false;
		if ( $( this ).parents( '.theme-type' ).css( 'float' ) == 'none' ) {
			mobile = true;
		}

		setTimeout( function() {
				$( '#generator' ).focus();
			}, 1400 );

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


		// since we've clicked 'download', let's close the generator if it exists
		$( '#generator' ).slideUp( 500, function() {

			$( '#generator' ).remove();

			// now that any existing generator is gone, we can re-add to the correct spot
			// if we're using the mobile layout, we want to move the form immediately below the current theme
			if( true == mobile ) {
				form.insertAfter( $( triggerElement ).parents( '.theme-type' ) ).slideDown( 500 );
			} else {
				// otherwise, we want to move the form under the 'type row'
				form.insertAfter( $( triggerElement ).parents( '.types-row' ) ).slideDown( 500 );
			}
		} );

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


	// The form has to be inserted in different spots depending on layout/resolution.
	// So let's make sure the form closes if the window's resized and passes over that threshold
	// Otherwise it could end up sitting in a wacky spot
	var ww = $(window).width();
	var timeOut;
	var tabletWidth = 1024;

	function closeIt() {
		ww = $(window).width();
		var w =  ww < tabletWidth ? ( console.log( 'passed it - mobile') ) :  ( ww > tabletWidth ? ( console.log( 'passed it - desktop' ) ) : ww = tabletWidth );

		$( '#generator' ).slideUp( 500 );
	}

	$( window ).resize( function() {
		var resW = $( window ).width();
		clearTimeout( timeOut );
		if ( ( ww > tabletWidth && resW < tabletWidth ) || ( ww < tabletWidth && resW > tabletWidth ) ) {
			timeOut = setTimeout( closeIt, 100 );
		}
	} );

} )( jQuery );
