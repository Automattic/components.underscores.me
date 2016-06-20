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
			$( '#type-blog-classic' ).attr( 'checked', true );
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
	} );

	// Smooth scrolling for anchor links.
	$( 'a[href^="#"]' ).on( 'click', function ( e ) {
		e.preventDefault();
		var target = this.hash;
		var $target = $( target );

		$( 'html, body' ).stop().animate({
			'scrollTop': $target.offset().top
		}, 900, 'swing', function () {
			window.location.hash = target;
			$( $target ).focus();
		} );
		return false;
	} );
} )( jQuery );
