( function( $ ) {

	// The following happens when a user clicks 'download'
	$( '.js .download' ).on( 'click', function( e ) {
		e.preventDefault();

		// Update the radio buttons to reflect current type
		if ( $( this ).is( '[data-type="base"]' ) ) {
			$( '#type-base' ).attr( 'checked', true );
		}

		if ( $( this ).is( '[data-type="blog-modern"]' ) ) {
			$( '#type-blog-modern' ).attr( 'checked', true );
		}

		if ( $( this ).is( '[data-type="blog-classic"]' ) ) {
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

		// Scroll to form
		$( 'html, body' ).stop().animate( {
			'scrollTop': $( '#generator' ).offset().top
		}, 900, 'swing', function () {
			window.location.hash = 'generator';
			$( '.js #generator' ).attr( {
				'tabindex': '-1'
			} ).focus();
		});

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

	// Hide/Show panels
	$( 'a.toggle' ).click(function( e ) {
		e.preventDefault();

		var toggled = $( this ).attr( 'href' );

		if ( ( '#' + $( '.panel:visible' ).attr( 'id' ) ) === toggled ) {
			$( toggled ).slideUp( 300 );
			return false;
		}

		if ( $( '.panel:visible' ).length === 0 ) {
			$( toggled ).slideDown( 600 );

			$( 'html, body' ).stop().animate( {
				'scrollTop': $( toggled ).offset().top
			}, 900, 'swing' );
		} else {
			$( '.panel:visible' ).slideUp( 600, function() {
				$( toggled ).slideDown( 900 );

				$( 'html, body' ).stop().animate( {
					'scrollTop': $( toggled ).offset().top
				}, 900, 'swing' );
			});
		}
	});

} )( jQuery );
