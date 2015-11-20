( function( $ ) {


	/**
	 * Slideshow
	 */

	// Initalize Slick Slider

	// only initalize slider if we're on a large enough screen -- this is checked by checking one of the mobile styles

	function slideshowCheck() {
		if( 'none' == $( '.theme-text' ).css( 'float' ) ) {
			// don't make a slideshow - we're on mobile
			if( $('.theme-slider').hasClass('slick-initialized') ) {
				$( '.theme-slider' ).slick('unslick');
			}
			console.log( 'remove slideshow' );

		} else if( $('.theme-slider').hasClass('slick-initialized') ) {
			// check if it's already initalized
			console.log( 'already initalized' );
		} else {
			console.log( 'add that slideshow' );

			$( '.theme-slider' ).slick( {
				arrows: false,
				speed: 500,
			} );
		}
	}

	$( document ).ready( function() {
		slideshowCheck();
	} );

	$( window ).resize( function() {
		slideshowCheck();
	} );




		// Add prev/next arrows to individual slides
		$( '.theme-slider .theme-text h3').append('<span class="next-type"><span class="screen-reader-text">Next</span></span>');
		$( '.theme-slider .theme-text h3').prepend('<span class="prev-type"><span class="screen-reader-text">Previous</span></span>');

		$('body').on('click', '.prev-type', function() {
			$('.theme-slider').slick('slickNext');
		});

		$('body').on('click', '.next-type', function() {
			$('.theme-slider').slick('slickPrev');
		});

		// toggle navigation for dropping robots

		// run before slide changes
		$('.theme-slider').on('beforeChange', function(event, slick, currentSlide, nextSlide ){

			var nextBot = $(".slick-slide[data-slick-index=" + nextSlide + "] .theme-image");
			// add class to position next bot prior to animation
			$( nextBot ).addClass( 'hide-bot' );

			// add same to 'cloned' slide bots, since they are kind of out of the regular slide flow
			$( ".slick-cloned.slick-slide .theme-image" ).addClass( 'hide-bot' );

			// toggle class to turn on gears
			$( '.slider-nav' ).addClass( 'gears-spinning' );

		} );

		//Run after slide changes
		$('.theme-slider').on('afterChange', function(event, slick, currentSlide){
			var currentBot = $(".slick-slide[data-slick-index=" + currentSlide + "] .theme-image");

			// recreate element to restart CSS3 animation
			var el = $( currentBot ),
			newone = el.clone( true );
			el.before( newone );
			$(el).remove();

			// remove class that's hiding bot
			$( newone ).removeClass( 'hide-bot' );

			// toggle class to turn off gears
			$( '.slider-nav' ).removeClass( 'gears-spinning' );

		} );
//	}


} )( jQuery );