;(function( $ ){ 
	"use strict";


	// https://github.com/petenelson/wp-backbone-gallery
	// https://github.com/kadamwhite/backbone-wordpress-demo

	// http://adrianmejia.com/blog/2012/09/11/backbone-dot-js-for-absolute-beginners-getting-started/


	var portfolioProjectGallery = Backbone.View.extend({
		// el - stands for element. Every view has a element associate in with HTML content will be rendered
		el : '#project-gallery-images',

		// Template which has the placeholder 'who' to be substitute later
		template : _.template( $( '#maxson-portfolio-gallery-slide' ).html() ),


		// It's the first function called when this view it's instantiated.
		initialize : function(){ 
			this.renderSlide();
		},

		// $el - it's a cached jQuery object (el), in which you can use jQuery functions to push content. Like the Hello World in this case
		renderSlide : function(){ 
			this.$el.html( this.template({ 
				attachmentID    : '12345',
				attachmentTitle : 'This is an image title',
				attachmentSrc   : 'http://via.placeholder.com/150x150'
			}) );

			// Enable chained calls
			return this;
		}

	});

	var projectGallery = new portfolioProjectGallery();

})( jQuery );