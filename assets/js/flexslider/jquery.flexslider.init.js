;(function( $ ){ 

	$( function(){

		function portfolioFlexslider()
		{ 
			if( $.isFunction( $.fn.flexslider ) )
			{ 
				var options = { 
					animation    : 'slide',
					smoothHeight : true,
					nextText     : flexslider_params.labelNext,
					prevText     : flexslider_params.labelPrev
				};

				$( '.project-gallery' ).flexslider( options );

			} // endif
		}

		portfolioFlexslider();

	});
})( jQuery );