(function( $ ) {
	'use strict';

	$(function() {
		$('.wp-block-easy-slider-slider-block').each(function(i){
			let $elem = $(this),
			centered = $elem.attr('data-centered') || false,
			showArrows = $elem.attr('data-showarrows') || false,
			showDots = $elem.attr('data-showdots') || false,
			rtl = $elem.attr('data-rtl') || false,
			autoplay = $elem.attr('data-autoplay') || true,
			loop = $elem.attr('data-loop') || true,
			slideCount = parseInt( $elem.attr('data-slidecount') ) || 1 ;
			
			$elem.slick({
				slidesToShow: slideCount==NaN ? 1 : slideCount,
				centerMode: centered,
				arrows: showArrows,
				dots: showDots,
				rtl: rtl,
				autoplay: autoplay,
				infinite: loop,
				responsive: [
					{
					  breakpoint: 768,
					  settings: {
						slidesToShow: 1,
						slidesToScroll: 1
					  }
					}
				]
			});
		});
	})

})( jQuery );