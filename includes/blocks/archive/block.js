;( function()
{ 
	// https://gist.github.com/ahmadawais/1b61fdaaf292e8d6590b17491a4007bc


	// The __() for internationalization.
//	var __ = wp.i18n.__;
	const { __ } = wp.i18n;

	// The wp.element.createElement() function to create elements.
//	var component = wp.element.createElement;
	const { Component } = wp.element;

	// The registerBlockType() to register blocks.
//	var registerBlockType = wp.blocks.registerBlockType;
	const { registerBlockType } = wp.blocks;


	/**
	 * Register Basic Block.
	 *
	 * Registers a new block provided a unique name and an object defining its
	 * behavior. Once registered, the block is made available as an option to any
	 * editor interface where blocks are implemented.
	 *
	 * @param  {string}   name     Block name.
	 * @param  {Object}   settings Block settings.
	 * @return {?WPBlock}          The block, if it has been successfully
	 *                             registered; otherwise `undefined`.
	 */
	registerBlockType( 'portfolioprojects/archive', { 
		title       : __( 'Portfolio Archive' ), 
		description : __( 'Display a grid of portfolio projects.' ), 
		icon        : 'grid-view', 
		category    : 'portfolioprojects', 
		keywords    : [ 
			__( 'portfolio' ),
			__( 'projects' ),
			__( 'archive' ),
			__( 'maxson' ),
		],

		// The "edit" property must be a valid function.
		edit: function( props )
		{ 
			const attributes    = props.attributes;
			const setAttributes = props.setAttributes;


		},

		// The "save" property must be specified and must be a valid function.
		save: function( props ) {
			return el(
				'p', // Tag type.
				{ className: props.className }, // The class="wp-block-gb-01-basic" : The class name is generated using the block's name prefixed with wp-block-, replacing the / namespace separator with a single -.
				'Hello World! — from the frontend (01 Basic Block).' // Content inside the tag.
			);
		},
	} );
})();
