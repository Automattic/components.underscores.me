( function( $ ) {

	$('.theme-slider').slick( {
		//'autoplay': true,
	} );

	$('.theme-slider').on('beforeChange', function(event, slick, currentSlide, nextSlide ){

		var nextBot = $(".slick-slide[data-slick-index=" + nextSlide + "] .theme-image");
		$( nextBot ).addClass( 'hide-bot' );

	} );

	//On before slide change
	$('.theme-slider').on('afterChange', function(event, slick, currentSlide){
		//$(".slick-slide[data-slick-index=" + currentSlide + "]").siblings().removeClass('');

		var currentBot = $(".slick-slide[data-slick-index=" + currentSlide + "] .theme-image");

		var el = $( currentBot ),
		newone = el.clone( true );

		el.before( newone );
		$(el).remove();

		$( newone ).removeClass( 'hide-bot' );

	} );

	//
	$('.theme.slider').on('init', function(event, slick, currentSlide, nextSlide){
		$(".slick-slide[data-slick-index=0]").addClass('drop-bot');
	} );

} )( jQuery );