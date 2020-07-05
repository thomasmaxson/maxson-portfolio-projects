jQuery(function( $ ){ 
	$( '#the-list' ).on( 'click', '.editinline', function(){

//		inlineEditPost.revert();

		var $this     = $( this ), 
			post_id   = $this.closest( 'tr' ).attr( 'id' );

			post_id = post_id.replace( 'post-', '' );

		var $project_data  = $( '#maxson_portfolio_project_inline_' + post_id ), 
			promoted       = $project_data.find( '.project-promoted' ).text(), 
			promoted_label = $project_data.find( '.project-promoted-label' ).text();

		if( 1 == promoted && '' != promoted_label )
		{ 
			$( 'input[name="project_promoted"]', '.inline-edit-row' ).prop( 'checked', true );
			$( 'input[name="project_promoted_label"]', '.inline-edit-row' ).val( promoted_label );

		} // endif
	});

});