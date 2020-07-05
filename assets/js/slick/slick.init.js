;(function( $ ){ 

	$(function(){

		if( $.isFunction( $.fn.slick ) )
		{ 
			var options = { 
				dots          : true,
				mobileFirst   : true,
				prevArrow     : '<a class="slick-prev" aria-label="Previous" tabindex="0">Previous</a>',
				nextArrow     : '<a class="slick-next" aria-label="Next" tabindex="0">Next</a>',
				customPaging : function( slider, i ){ 
					return '<a href="#" aria-required="false" tabindex="0">' + ( i + 1 ) + '</a>';
				}
			};

			$( '.single-portfolio_project .project-gallery ul' ).slick( options );

		} // endif

	});
})( jQuery );