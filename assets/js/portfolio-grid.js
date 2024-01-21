;( function( $ )
{ 
	"use strict";

	var MAXSON_PORTFOLIO = window.MAXSON_PORTFOLIO || {};

	window.MAXSON_PORTFOLIO = MAXSON_PORTFOLIO;

	MAXSON_PORTFOLIO.components = MAXSON_PORTFOLIO.components || {};
	MAXSON_PORTFOLIO.helpers    = MAXSON_PORTFOLIO.helpers || {};


	/**
	 * Main Site Init
	 */

	MAXSON_PORTFOLIO.siteInit = function()
	{
		this.defaultConfig();
		this.bindEvents();
	};


	/**
	 * Default Variables Init
	 */

	MAXSON_PORTFOLIO.defaultConfig = function()
	{ 
		this.config = { 
			$document     : $( document ),
			$window       : $( window ),
			$windowWidth  : $( window ).width(),
			$windowHeight : $( window ).height(),

			$html : $( 'html' ),
			$body : $( 'body' )
		};
	};


	/**
	 * Update Config Dom
	 */

	MAXSON_PORTFOLIO.updateConfig = function()
	{ 
		this.config.$html = $( 'html' );
		this.config.$body = $( 'body' );
	};


	/**
	* Bind Events of site
	*/

	MAXSON_PORTFOLIO.bindEvents = function()
	{ 
		var self = this;

		// Document Ready
		self.config.$document.ready( function()
		{ 
			self.updateConfig();

		} );

		// Window Load
		self.config.$window.on( 'load', function()
		{ 
			self.portfolioGrid();

		} );
	};


	/**
	 * Parse and sanitize value
	 */

	MAXSON_PORTFOLIO.helpers.parseData = function( val, fallback )
	{ 
		return ( typeof val !== 'undefined' ) ? val : fallback;
	};


	/**
	 * Portfolio Grid Init
	 */

	MAXSON_PORTFOLIO.portfolioGrid = function( $el )
	{ 
		var $default = $( MaxsonPortfolioParams.selector.portfolio_grid );
		var $element = MAXSON_PORTFOLIO.helpers.parseData( $el, $default );

		$element.each( function()
		{ 
			MAXSON_PORTFOLIO.components.isotope( $( this ) );
	
		} );
	};


	/**
	 * Portfolio Grid Filters Init
	 */

	MAXSON_PORTFOLIO.components.portfolioGridFilters = function( $container, $teasers )
	{ 
		var self = this;

		var $filters = $container.find( MaxsonPortfolioParams.selector.portfolio_filters );
		var filterVal;

	//	if( $( 'body' ).hasClass( 'customize-preview') )
	//	{ 
	//		return false;

	//	} // endif

		if( $filters.find( 'select' ).length )
		{ 
			var $select = $filters.find( 'select' );

			// Set first option as selected
			$select.find( 'option:first' ).attr( 'selected', 'selected' );

			$select.on( 'change', function( event )
			{ 
				event.preventDefault();

				var $this     = $( this );
				var $children = $this.find( 'option' );
				var value     = $this.val();

				filterVal = $this.find( 'option[value="' + value + '"]' ).attr( 'data-filter' );

				MAXSON_PORTFOLIO.components.isotopeFilter( $teasers, filterVal );

				$this.find( 'option[value="' + value + '"]' ).attr( 'selected', 'selected' ).siblings().removeAttr( 'selected' );

			} );
		} else
		{ 
			// Set first list item as active
			$filters.find( 'li:first' ).addClass( 'active' );

			$filters.on( 'click', 'button', function( event )
			{ 
				event.preventDefault();

				var $this     = $( this );
				var $parentEl = $this.parent();

				filterVal = $this.attr( 'data-filter' );

				MAXSON_PORTFOLIO.components.isotopeFilter( $teasers, filterVal );

				$parentEl.addClass( 'active' ).siblings().removeClass( 'active' );

			} );
		} // endif
	};


	/**
	 * Isotope Init
	 */

	MAXSON_PORTFOLIO.components.isotope = function( $container )
	{ 
		var self     = this;
		var $teasers = $container.find( MaxsonPortfolioParams.selector.portfolio_teasers );

		if( ! $.isFunction( $.fn.isotope ) )
		{ 
			console.error( 'jQuery Isotope plugin does not exist.' );
			return false;

		} // endif

	//	if( ! $.isFunction( $.fn.imagesLoaded ) )
	//	{ 
	//		console.error( 'jQuery Images Loaded plugin does not exist.' );
	//		return false;

	//	} // endif

		if( $teasers.length )
		{ 
			$teasers.each( function()
			{ 
				var $grid = $( this );
				var $data = $container.data();

				var isotopeDuration = MAXSON_PORTFOLIO.helpers.parseData( $data.transitionduration, 350 );
				var isotopeMode     = MAXSON_PORTFOLIO.helpers.parseData( $data.layoutmode, 'masonry' );

				var isotopeOptions = { 
					filter             : '*',
					itemSelector       : MaxsonPortfolioParams.selector.project_teaser,
					percentPosition    : true,
					resizesContainer   : true,
					resizable          : false,
					transitionDuration : isotopeDuration,
					layoutMode         : isotopeMode,
					hiddenStyle        : { 
						opacity : 0
					},
					visibleStyle       : { 
						opacity : 1.0
					}
				};

			//	$grid.imagesLoaded( function()
			//	{ 
					$teasers.isotope( isotopeOptions );
				//	$teasers.isotope( 'arrange' );

					MAXSON_PORTFOLIO.components.portfolioGridFilters( $container, $teasers );
			//	} );

			} );
		} // endif
	};


	/**
	 * Isotope Filters Init
	 */

	MAXSON_PORTFOLIO.components.isotopeFilter = function( $teasers, filterVal )
	{ 
		$teasers = MAXSON_PORTFOLIO.helpers.parseData( $teasers, false );

		if( false == $teasers || ! $teasers.length )
		{ 
			return false;

		} // endif


		var isotopeOptions = { 
			filter : filterVal,
			sortBy : ( filterVal === '*' ) ? 'original-order' : 'random'
		};

		$teasers.isotope( isotopeOptions );
	//	$teasers.isotope( 'arrange' );

	//	this.config.$document.trigger( 'portfolio.gridFilters.sorted', $container, $teasers, filterVal );

	};


	MAXSON_PORTFOLIO.siteInit();

} )( jQuery );