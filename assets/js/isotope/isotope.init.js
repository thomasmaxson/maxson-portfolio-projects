;(function( $ ){ 

	$( window ).on( 'load', function(){ 
		function portfolioIsotope()
		{ 
			if( $.isFunction( $.fn.isotope ) )
			{ 
				var $container = $( '#portfolio-isotope' ),
					$filter    = $( '#portfolio-filter' );

				if( $container.length )
				{ 
					var options = { 
							filter       : '*',
							itemSelector : '.project',
							resizable    : false,
							hiddenStyle  : { 
								opacity : 0
							},
							visibleStyle: { 
								opacity : 1.0
							}
						};

					$container.imagesLoaded( function(){ 
						$container.isotope( options );
					});

					$filter.on( 'change', function( event ){ 
						event.preventDefault();

						$( document ).trigger({ 'type' : 'portfolioProjectsFilter.before' });

						$container.isotope({ 
							filter: $( this ).find( ':selected' ).attr( 'data-filter' )
						});

						$( document ).trigger({ 'type' : 'portfolioProjectsFilter.after' });
					});

				} // endif

			} // endif
		}

		portfolioIsotope();

	});

})( jQuery );