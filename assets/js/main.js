( function( $ ) {

	$('.theme-slider').slick( {
		'autoplay': true,
	} );

	//On before slide change
	$('.theme-slider').on('afterChange', function(event, slick, currentSlide, nextSlide){
		$(".slick-slide[data-slick-index=" + currentSlide + "]").siblings().removeClass('drop-bot');
		$(".slick-slide[data-slick-index=" + currentSlide + "]").addClass('drop-bot');
	} );

	//
	$('.theme.slider').on('init', function(event, slick, currentSlide, nextSlide){
		$(".slick-slide[data-slick-index=0]").addClass('drop-bot');
	} );

} )( jQuery );