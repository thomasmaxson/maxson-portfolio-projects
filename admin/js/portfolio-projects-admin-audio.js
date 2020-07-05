;(function( $ ){ 
	"use strict";

	var frame;

	$( '.project-audio-media-button' ).on( 'click', function( event ){ 
		event.preventDefault();

		var selection, attachment,
			$el        = $( this ),
			$parent    = $el.parent(),
			file_type  = $el.data( 'file-type' ),
			media_type = $el.data( 'media-type' );

		var project_media_id = $el.parent().siblings( 'input[type="hidden"]' ).val();

		var media_title  = ( 'audio' == media_type ) ? portfolio_projects_audio_params.audio_title : portfolio_projects_audio_params.poster_title,
			media_button = ( 'audio' == media_type ) ? portfolio_projects_audio_params.audio_button.replace( '%%filetype%%', '.' + file_type ) : portfolio_projects_audio_params.poster_button;

		frame = wp.media({ 
			title    : media_title,
			button   : { text: media_button },
			multiple : false
		});


		frame.on( 'open', function(){ 
			frame.state().trigger( 'insert', selection ).reset();

			if( '' != project_media_id )
			{ 
				attachment = wp.media.attachment( project_media_id );
				attachment.fetch();

				frame.state().get( 'selection' ).add( attachment );

			} // endif

		}).on( 'select', function(){ 
			selection = frame.state().get( 'selection' );

			selection.map( function( attachment ){ 
				attachment = attachment.toJSON();

				if( 'audio' == media_type )
				{ 
					var extension = attachment.filename.substr( ( attachment.filename.lastIndexOf( '.' ) +1 ) );

					if( ( typeof file_type !== typeof undefined ) && ( file_type !== false ) )
					{ 
						if( file_type != extension )
						{ 
							var message = portfolio_projects_audio_params.audio_type_invalid.replace( '%%filetype%%', '.' + file_type );

							alert( message );

							return false;

						} // endif
					} // endif
				} // endif

				$parent.siblings( '.project-audio-media-url' ).val( attachment.url );
				$parent.siblings( '.project-audio-media-id' ).val( attachment.id );

				return false;
			});

		}).open();

	});


	$( '.project-audio-media-url' ).on( 'blur', function( event ){ 
		var $this = $( this );

		if( ! $this.val() )
		{ 
			$this.siblings( '.project-audio-media-id' ).val( '' );

		} // endif
	});

})( jQuery );