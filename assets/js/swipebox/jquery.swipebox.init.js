;(function( $ ){ 

	$( function(){

		function portfolioSwipebox()
		{ 
			if( $.isFunction( $.fn.swipebox ) )
			{ 
				$( '.project-gallery .zoom' ).swipebox();

			} // endif
		}

		portfolioSwipebox();

	});

})( jQuery );