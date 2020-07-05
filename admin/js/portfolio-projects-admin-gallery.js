;(function( $ ){ 
	"use strict";

	// http://wordpress.stackexchange.com/questions/112599/pre-populate-wordpress-wp-media-modal-with-image-selection

	$( function(){ 
		var frame,
			images    = $( '#project-gallery-image-ids' ).val(),
			selection = loadImages( images );

		$( document ).on( 'click', '#project-gallery-upload-button', function( event ){ 
			event .preventDefault();

			// Set options for 1st frame render
			var options = { 
				title     : portfolio_projects_gallery_params.create_gallery_title,
				toolbar   : false,
				frame     : 'post',
				state     : 'gallery-edit',
				editing   : true,
				multiple  : true,
				selection : selection
			};

			// Check if frame or gallery already exist
			if( frame || selection )
			{ 
				options['title'] = portfolio_projects_gallery_params.edit_gallery_title;

			} // endif


			frame = wp.media( options ).open();
			
			// Tweak views
			frame.menu.get( 'view' ).unset( 'cancel' );
			frame.menu.get( 'view' ).unset( 'separateCancel' );

			// Hide "Gallery Settings" in sidebar
			frame.content.get( 'view' ).sidebar.unset( 'gallery' );

			// Tweak title text
			frame.menu.get( 'view' ).get( 'gallery-edit' ).el.innerHTML = options['title'];

			// Tweak title text
			frame.menu.get( 'view' ).get( 'gallery-library' ).el.innerHTML = portfolio_projects_gallery_params.add_to_gallery_title;

			// When we are editing a gallery
			overrideGalleryInsert();


			frame.on( 'toolbar:render:gallery-edit', function(){ 
				overrideGalleryInsert();


			}).on( 'content:render:browse', function( browser ){ 
				if( ! browser )
				{ 
					return;

				} // endif

				// Hide Gallery Settings in sidebar
				browser.sidebar.on( 'ready', function(){ 
					browser.sidebar.unset( 'gallery' );
				});

				// Hide filter/search as they don't work
				browser.toolbar.on( 'ready', function(){ 
					if( browser.toolbar.controller._state == 'gallery-library' )
					{ 
						browser.toolbar.$el.hide();

					} // endif
				});
			});


			// All images removed
			frame.state().get( 'library' ).on( 'remove', function(){ 
				var models = frame.state().get( 'library' );

				if( models.length == 0 )
				{ 
					selection = false;

					$.post( ajaxurl, { 
						ids     : '', 
						action  : 'portfolio_project_save_gallery_images', 
						nonce   : portfolio_projects_gallery_params.nonce
					});

				} // endif
			});


			// Override insert button
			function overrideGalleryInsert()
			{ 
				frame.toolbar.get( 'view' ).set({ 
					insert: { 
						style : 'primary',
						text  : portfolio_projects_gallery_params.set_gallery,
						click : function(){ 
							var models      = frame.state().get( 'library' ),
								gallery_ids = [];

							models.each( function( attachment ){ 
								gallery_ids.push( attachment.id );
							});

							this.el.innerHTML = portfolio_projects_gallery_params.saving_gallery;

							$.ajax({ 
								type : 'POST',
								url  : ajaxurl,
								data : { 
									ids     : gallery_ids,
									action  : 'portfolio_project_process_gallery_images',
									nonce   : portfolio_projects_gallery_params.nonce
								},
								success : function( response ){ 
									if( response.success != true )
									{ 
										frame.close();

										return false;

									} // endif

									var gallery_string = gallery_ids.join( ',' );

									selection = loadImages( gallery_string );

									$( '#project-gallery-image-ids' ).val( gallery_string );
									$( '#project-gallery-images' ).html( response.data.gallery_html );

									frame.close();

								},
								error: function( request, status, error ){ 
									console.log( request.responseText );
									console.log( error );

								}
							});
						}
					}
				});
			}
		});


		// Load images
		// http://wordpress.stackexchange.com/questions/83363/insert-wp-gallery-shortcode-into-custom-textarea
		function loadImages( images )
		{ 
			if( images )
			{ 
				var shortcode  = new wp.shortcode({ 
					tag   : 'gallery',
					attrs : { 
						ids : images
					},
					type  : 'single'
				});

				var attachments = wp.media.gallery.attachments( shortcode ),
					selection   = new wp.media.model.Selection( attachments.models, { 
					props    : attachments.props.toJSON(),
					multiple : true
				});

				selection.gallery = attachments.gallery;

				// Fetch the query's attachments, and then break ties from the query to allow for sorting
				selection.more().done( function(){ 
					// Break ties with the query.
					selection.props.set({ query: false });
					selection.unmirror();
					selection.props.unset( 'orderby' );

				});

				return selection;

			} // endif

			return false;
		}

	});


	// http://wordpress.itsprite.com/wordpressis-it-possible-to-re-use-the-image-details-popup/
	// http://wordpress.stackexchange.com/questions/215979/wp-media-view-imagedetails-save-settings-as-html5-data-attributes-for-image
	// http://somepieceof.info/question/wordpress/wp-media-view-imagedetails-save-settings-as-html5-data-attributes-for-image/

	/*$( '.gallery-image' ).on( 'click', function( event ){ 
		event.preventDefault();

		var $this           = $( this ),
			$attachment_id  = $this.data( 'attachment-id' );

		// execute the query, this will return an deferred object attach callback, executes after the ajax call succeeded
		wp.media.query({ post__in : [$attachment_id] }).more().done( function(){ 

			// inside the callback 'this' refers to the result collection there should be only one model, assign it to a var
			var attachment = this.first();

			// To access 'sizes' OR 'image editor/replace image' in the modal function attachment_id must be set
			attachment.set( 'attachment_id', attachment.get( 'id' ) );

			// create a frame, bind 'update' callback and open in one step
			wp.media({ 
				frame: 'image', 
				state: 'image-details', 
				metadata: attachment.toJSON(), // where the initial informations come from 

		//	}).on( 'update', function( attachmentObj ){ 
		//		console.log( $attachment_id );

			}).open();
		});

	});*/

})( jQuery );