( function( $ ) {

	$('.theme-slider').slick( {
		'autoplay': true,
	} );

	//On before slide change
	$('.theme-slider').on('afterChange', function(event, slick, currentSlide, nextSlide){
		console.log( currentSlide );
		$(".slick-slide[data-slick-index=" + currentSlide + "]").siblings().removeClass('drop-bot');
		$(".slick-slide[data-slick-index=" + currentSlide + "]").addClass('drop-bot');
		$( currentSlide ).addClass('hey');
	} );

} )( jQuery );