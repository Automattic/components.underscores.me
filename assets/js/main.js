( function( $ ) {

	$('.theme-slider').slick( {
		//'autoplay': true,
	} );

	$('.theme-slider').on('beforeChange', function(event, slick, currentSlide, nextSlide ){

		var nextBot = $(".slick-slide[data-slick-index=" + nextSlide + "] .theme-image");
		$( nextBot ).addClass( 'hide-bot' );

		console.log( 'next slide: ' + nextSlide );

		$(".slick-cloned.slick-slide .theme-image").addClass( 'hide-bot' );

	} );

	//On before slide change
	$('.theme-slider').on('afterChange', function(event, slick, currentSlide){
		var currentBot = $(".slick-slide[data-slick-index=" + currentSlide + "] .theme-image");

		var el = $( currentBot ),
		newone = el.clone( true );

		el.before( newone );
		$(el).remove();

		$( newone ).removeClass( 'hide-bot' );

		console.log( 'current slide: ' + currentSlide );

	} );

	//
	$('.theme.slider').on('init', function(event, slick, currentSlide, nextSlide){
		$(".slick-slide[data-slick-index=0]").addClass('drop-bot');
	} );

} )( jQuery );