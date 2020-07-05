;(function( $ ){ 
	"use strict";


	function parse_date_in_seconds( stringyDate )
	{ 
		var d = new Date( stringyDate ),
			s = 0;

		if( stringyDate.length > 0 )
		{ 
			s = ( d.valueOf() / 1000 );

		} // endif

		return s;
    }


	/**
	 * Tiptip Tooltip
	 */

	var tip_args = { 
		attribute  : 'data-tip',
		fadeIn     : 100,
		fadeOut    : 100,
		delay      : 250,
		edgeOffset : 10
	};


	if( $.isFunction( $.fn.tipTip ) )
	{ 
		$( '[data-tip]' ).tipTip( tip_args );

	} // endif


	/**
	 * Chosen Select
	 */

	if( $.isFunction( $.fn.chosen ) )
	{ 
		$( '.chosen-portfolio-select select, select.chosen-portfolio-select' ).each( function( index, value ){ 
			var $this = $( this );

			if( $this.hasClass( 'chosen-disable-search' ) )
				$this.attr( 'data-disable-search', 'true' );

			if( $this.hasClass( 'chosen-multiple' ) )
				$this.attr( 'multiple', 'multiple' );

			var $width          = $this.data( 'width' ) || $this.width() + 'px',
				$disable_search = $this.data( 'disable-search' ) || false,
				$multiple_text  = $this.data( 'multiple-text' ),
				$single_text    = $this.data( 'single-text' ) || maxson_portfolio_chosen_params.single_text,
				$no_result_text = $this.data( 'no-result-text' ) || maxson_portfolio_chosen_params.no_result_text,
				$placeholder_text_multiple = $this.data( 'placeholder-text-multiple' ) || maxson_portfolio_chosen_params.placeholder_text_multiple;

			$this.chosen({ 
				width                     : $width,
				multiple_text             : $multiple_text,
				placeholder_text_multiple : $placeholder_text_multiple,
				single_text               : $single_text,
				no_results_text           : $no_result_text,
				disable_search            : $disable_search,
				inherit_select_classes    : true
			});
		});

	} // endif


	/**
	 * jQuery Datepicker
	 */

	if( $.isFunction( $.fn.datepicker ) )
	{ 
		$( '#meta_box_project_details input[type="date"]' ).attr( 'type', 'text' );

		var $start_date     = $( '#project-start-date' ),
			$start_date_raw = $( '#project-start-date-raw' ),
			$end_date       = $( '#project-end-date' ),
			$end_date_raw   = $( '#project-end-date-raw' );

		var datepicker_generic_args = { 
			dateFormat      : maxson_portfolio_admin_params.dateFormat,
			showButtonPanel : maxson_portfolio_admin_params.showButtonPanel,
			closeText       : maxson_portfolio_admin_params.i18n_closeText,
			currentText     : maxson_portfolio_admin_params.i18n_currentText,
			nextText        : maxson_portfolio_admin_params.i18n_nextText,
			prevText        : maxson_portfolio_admin_params.i18n_prevText,
			numberOfMonths  : parseInt( maxson_portfolio_admin_params.numberOfMonths ),
			monthNames      : maxson_portfolio_admin_params.monthNames,
			monthNamesShort : maxson_portfolio_admin_params.monthNamesShort,
			dayNames        : maxson_portfolio_admin_params.dayNames,
			dayNamesShort   : maxson_portfolio_admin_params.dayNamesShort,
			dayNamesMin     : maxson_portfolio_admin_params.dayNamesMin,
			firstDay        : maxson_portfolio_admin_params.firstDay,
			isRTL           : maxson_portfolio_admin_params.isRTL,
			changeMonth     : true,
			changeYear      : true,
			maxDate         : null,
			minDate         : null
		};


		/**
		 * Project Datepicker - Start Date
		 */

		var datepicker_start_date_args = $.extend( datepicker_generic_args, { 
			onClose : function( dateText ){ 
				$end_date.datepicker( 'option', 'minDate', dateText );

				if( '' != dateText )
				{ 
					var altDate       = new Date( dateText ),
						altDateFormat = maxson_portfolio_admin_params.altFormat,
						altDateText   = $.datepicker.formatDate( altDateFormat , altDate );

					$start_date_raw.val( altDateText );

				} else
				{ 
					$start_date_raw.val( '' );

				} // endif
			},
			minDate : null,
			maxDate : new Date( $end_date.val() )
		});

		$start_date.datepicker( datepicker_start_date_args );


		/**
		 * Project Datepicker - End Date
		 */

		var datepicker_end_date_args = $.extend( datepicker_generic_args, { 
			onClose : function( dateText ){ 
				$start_date.datepicker( 'option', 'maxDate', dateText );

				if( '' != dateText )
				{ 
					var altDate       = new Date( dateText ),
						altDateFormat = maxson_portfolio_admin_params.altFormat,
						altDateText   = $.datepicker.formatDate( altDateFormat , altDate );

					$end_date_raw.val( altDateText );

				} else
				{ 
					$end_date_raw.val( '' );

				} // endif
			},
			minDate : new Date( $start_date.val() ),
			maxDate : null
		});

		$end_date.datepicker( datepicker_end_date_args );

	} // endif


//	var unsavedFlag = false;

//	$( 'form[id^="maxson-portfolio"]' ).on( 'change', 'input, textarea, select', function( event ){ 
//		unsavedFlag = true;
//	});


//	$( window ).on( 'beforeunload', function(){ 
//		if( unsavedFlag )
//		{ 
//			if( ! confirm( maxson_portfolio_admin_params.i18n_messageUnsavedChanges ) )
//			{  
//				return false;

//			} // endif
//		} // endif
//	});

	/**
	 * Project Metabox Toggle
	 */

	$( '.js .toggle-metabox' ).each( function( index, value ){ 
		var $this      = $( this ),
			term_type  = $this.data( 'metabox-type' ),
			$metabox   = $( '#' + term_type ),
			$screen_opt = $( '.metabox-prefs #' + term_type + '-hide' ),
			term_is_checked       = $this.is( ':checked' ),
			screen_opt_is_checked = $screen_opt.is( ':checked' );

		if( true == screen_opt_is_checked )
		{ 
			$screen_opt.trigger( 'click' );

		} // endif

		$metabox.hide();

		if( true == term_is_checked )
		{ 
			$screen_opt.trigger( 'click' );
			$metabox.show();

		} // endif
	}).on( 'click', function( event ){ 
		var $this = $( this ), 
			type  = $this.data( 'metabox-type' );

		$( '.metabox-prefs [id^="meta_box_project_type_"]:checked' ).trigger( 'click' );
		$( '.metabox-prefs #' + type + '-hide' ).trigger( 'click' );
	});


	/**
	 * Project Metabox Tabs
	 */

	$( '.js .project-tabs' ).on( 'change', 'input[type="radio"]', function( event ){ 
		var $this    = $( this ),
			$parent  = $this.parents( 'li' ),
			$content = $parent.attr( 'id' );

		$parent.addClass( 'active' ).siblings().removeClass( 'active' );

		$( '#' + $content + '_content' ).addClass( 'active' ).siblings().removeClass( 'active' );

	}).find( 'input[type="radio"]:checked' ).prop( 'checked', false ).click();


	/**
	 * Project Admin Column Featured 
	 */

	$( '.js .column-promoted' ).on( 'click', '.icon-promoted, .icon-not-promoted', function( event ){ 
		event.preventDefault();

		var $this   = $( this ), 
			post_id = $this.closest( 'tr' ).attr( 'id' ).replace( 'post-', '' ),
			inline_data = $( '#maxson_portfolio_project_inline_' + post_id );

		$.ajax({ 
			url      : ajaxurl,
			cache    : false,
			dataType : 'json',
			data     : { 
				action   : 'portfolio_project_promoted_callback', 
				nonce    : $this.data( 'nonce' ), 
				post_id  : post_id
			},
			success: function( data_response ){ 
				if( true == data_response.success )
				{ 
					$this.toggleClass( 'icon-not-promoted' );

					$this.html( data_response.data.label );

					if( 'promoted' == data_response.data.type )
					{ 
						tip_args['defaultPosition'] = 'bottom';

						$this.attr( 'data-tip', data_response.data.label ).tipTip( tip_args );

						inline_data.find( '.project-promoted' ).text( '1' );
						inline_data.find( '.project-promoted-label' ).text( data_response.data.label );

					} else 
					{ 
						$this.data( 'tip', false ).tipTip( 'destroy' );

						inline_data.find( '.project-promoted' ).text( '0' );
						inline_data.find( '.project-promoted-label' ).empty();

					} // endif

				} else if( false == data_response.success )
				{ 
					console.log( data_response.data.message );

				} // endif
			},
			error: function( request, status, error ){ 
				console.log( request.responseText );
				console.log( error );
			}

		});
	});


	/**
	 * Project Admin Term Featured Image 
	 */

	$( document ).on( 'click', '.portfolio-taxonomy-thumbnail-upload', function( event ){ 
		event.preventDefault();

		var file_frame;

		// If the media frame already exists, reopen it.
		if( file_frame )
		{ 
			file_frame.open();
			return;

		} // endif

		// Create the media frame.
		file_frame = wp.media.frames.downloadable_file = wp.media({ 
			title: maxson_portfolio_admin_params.i18n_taxonomy_term_image_title,
			button: {
				text: maxson_portfolio_admin_params.i18n_taxonomy_term_image_button
			},
			multiple: false
		});

		file_frame.on( 'open', function(){ 
			var selection    = file_frame.state().get( 'selection' ),
				thumbnail_id = $( '#portfolio-taxonomy-thumbnail-id' ).val(),
				attachment   = wp.media.attachment( thumbnail_id );

			attachment.fetch();
			selection.add( attachment ? [ attachment ] : [] );

		}).on( 'select', function(){ 
			var attachment = file_frame.state().get( 'selection' ).first().toJSON();

			$( '#portfolio-taxonomy-thumbnail-id' ).val( attachment.id );
			$( '.portfolio-taxonomy-thumbnail' ).find( 'img' ).attr( 'src', attachment.sizes.thumbnail.url );
			$( '.portfolio-taxonomy-thumbnail-remove' ).removeClass( 'hidden' );
		});

		// Finally, open the modal.
		file_frame.open();
	});


	$( document ).on( 'click', '.portfolio-taxonomy-thumbnail-remove', function( event){ 
		event.preventDefault();

		$( '.portfolio-taxonomy-thumbnail' ).find( 'img' ).attr( 'src', maxson_portfolio_admin_params.taxonomy_term_image_default );
		$( '#portfolio-taxonomy-thumbnail-id' ).val( '' );
		$( '.portfolio-taxonomy-thumbnail-remove' ).addClass( 'hidden' );
	});


	$( document ).ajaxComplete( function( event, request, options ){ 
		if( request && 4 === request.readyState && 200 === request.status
			&& options.data && 0 <= options.data.indexOf( 'action=add-tag' ) )
		{ 
			var res = wpAjax.parseAjaxResponse( request.responseXML, 'ajax-response' );

			if( ! res || res.errors )
			{ 
				return;

			} // endif

			$( '.portfolio-taxonomy-thumbnail' ).find( 'img' ).attr( 'src', maxson_portfolio_admin_params.taxonomy_term_image_default );
			$( '#portfolio-taxonomy-thumbnail-id' ).val( '' );
			$( '.portfolio-taxonomy-thumbnail-remove' ).addClass( 'hidden' );
			return;

		} // endif
	});

})( jQuery );