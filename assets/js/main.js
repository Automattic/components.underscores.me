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
				// if there is already a slideshow, destroy it
				$( '.theme-slider' ).slick('unslick');
			}

		} else if( $('.theme-slider').hasClass('slick-initialized') ) {
			// check if slideshow is already initalized
		} else {
			// not mobile, not initalized -- add that slideshow!
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
	$( '.theme-slider .theme-text .theme-type-title').prepend('<span class="next-type"><span class="screen-reader-text">Next</span></span>');
	$( '.theme-slider .theme-text .theme-type-title').prepend('<span class="prev-type"><span class="screen-reader-text">Previous</span></span>');

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

		//dim spotlight
		$( '.spotlight-controls' ).addClass( 'dim' );

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

		//increase spotlight
		$( '.spotlight-controls' ).removeClass( 'dim' );

	} );

	$('.slider-lever').click( function() {
		$( this ).addClass( 'turned' );

		function releaseLever() {
			$( '.slider-lever').removeClass( 'turned' );
		}
		function advanceRobots() {
			$('.theme-slider').slick('slickPrev');
		}

		setTimeout( releaseLever, 250 );
		setTimeout( advanceRobots, 300 );
	} );

	// open/close text input for name
	/*
	$( '.theme-text .button' ).click( function( e ){
		e.preventDefault();
		$( '.theme-input' ).toggleClass( 'open-input' );
	} );
	*/

} )( jQuery );
