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

		// Scroll to form
		$('html, body').stop().animate({
			'scrollTop': $( '#generator' ).offset().top
		}, 900, 'swing', function () {
			window.location.hash = 'generator';
			$( '.js #generator' ).attr( {
				'tabindex': '-1'
			} ).focus();
		});
	} );

	// Basic client-side form validation.
	var form = $( '#generator-form' );
	var typeInput = $( 'input[name="components_theme_type"]:checked' );
	var nameInput = $( '#components-types-name' );
	var uriInput = $( '#components-types-author-uri' );
	var slugInput = $( '#components-types-slug' );
	// Set form inputs we check via JavaScript as not having errors because we have not checked for errors yet.
	typeInput.attr( 'aria-invalid', 'false' );
	nameInput.attr( 'aria-invalid', 'false' );
	uriInput.attr( 'aria-invalid', 'false' );
	slugInput.attr( 'aria-invalid', 'false' );

	// Listen for submit
	form.submit( function( e ) {

		// Get our values so we can check them.
		var type = $( 'input[name="components_theme_type"]:checked' ).val();
		var name = $( '#components-types-name' ).val();
		var uri = $( '#components-types-author-uri' ).val();
		var slug = $( '#components-types-slug' ).val();
		var errors = '';

		// Supply our error messages.
		// If theme type is empty.
		if ( ! type || 0 === type.length ) {
			errors += '<li>Please specify a theme type.</li>\n';
			typeInput.attr( 'aria-invalid', 'true' );
		} else {
			// Reset aria-invalid attribue from any previous attempts.
			typeInput.attr( 'aria-invalid', 'false' );
		}
		// If theme name is empty.
		if ( ! name || 0 === name.length ) {
			errors += '<li>Please specify a theme name.</li>\n';
			nameInput.attr( 'aria-invalid', 'true' );
		} else {
			// Reset aria-invalid attribue from any previous attempts.
			nameInput.attr( 'aria-invalid', 'false' );
		}
		// If the theme name is not empty, make sure it has no special characters.
		if ( name || 0 < name.length ) {
			if ( /[\'^£$%&*()}{@#~?><>,|=+¬"]/.test( name.trim() ) === true ) {
				errors += '<li>Theme name could not be used to generate valid theme name. Please go back and try again.</li>\n';
				nameInput.attr( 'aria-invalid', 'true' );
			} else {
				// Reset aria-invalid attribue from any previous attempts.
				nameInput.attr( 'aria-invalid', 'false' );
			}
		}
		// If the theme slug is not empty, make sure it has no special characters.
		if ( slug || 0 < slug.length ) {
			if ( /^[a-z_]\w+$/i.test( slug.trim() ) === false ) {
				errors += '<li>Theme slug could not be used to generate valid function names. Please go back and try again.</li>\n';
				slugInput.attr( 'aria-invalid', 'true' );
			} else {
				// Reset aria-invalid attribue from any previous attempts.
				slugInput.attr( 'aria-invalid', 'false' );
			}
		}
		// If the author uri is not empty, make sure it is a valid uri.
		if ( uri || 0 < uri.length ) {
			if ( /^(https?:\/\/)([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/.test( uri.trim() ) === false ) {
				errors += '<li>Author URI is not valid. Please go back and try again.</li>\n';
				uriInput.attr( 'aria-invalid', 'true' );
			} else {
				// Reset aria-invalid attribue from any previous attempts.
				uriInput.attr( 'aria-invalid', 'false' );
			}
		}

		// If we have errors from a previous try, let's remove them.
		if ( errors !== '' &&  $( '#error > ul li' ).length !== 0 ) {
			$( '#error > ul li' ).remove();
		}
		// If we have errors, let's show them.
		if ( errors !== '' ) {
			// We only create the error div and ul if we don't already have them.
			if ( ! $( '#error' ).length ) {
				var errorDiv = $( '<div>', { id: 'error', class: 'error', tabindex: '-1' } );
				$( '#generator-form' ).prepend( errorDiv );
				$( '#error' ).append( '<ul>' );
			}
			// Let's place our errors and shift focus there.
			$( '#error > ul' ).append( errors );
			$( '#error' ).focus();
			e.preventDefault();
		}
		// If we have no errors, let's reset them.
		else if ( errors === '' ) {
			$( '#error > ul li' ).remove();
			typeInput.attr( 'aria-invalid', 'false' );
			nameInput.attr( 'aria-invalid', 'false' );
			uriInput.attr( 'aria-invalid', 'false' );
			slugInput.attr( 'aria-invalid', 'false' );
		}
	} );

} )( jQuery );
