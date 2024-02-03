/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */

import {
	__,
	_x,
	sprintf
} from '@wordpress/i18n';


/**
 * WordPress dependenices
 */

import {
	BlockControls,
	InspectorControls,
	MediaPlaceholder,
	MediaUpload,
	MediaUploadCheck,
	useBlockProps
} from '@wordpress/block-editor';


import {
	Button,
	PanelBody,
	SelectControl,
	TextControl,
	ToggleControl,
	ToolbarGroup,
	ToolbarItem
} from '@wordpress/components';


import {
	useState
} from '@wordpress/element';


import {
	useDispatch,
	useSelect
} from '@wordpress/data';


import {
	store as noticesStore
} from '@wordpress/notices';


/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */

import './editor.scss';


/**
 * External Dependencies
 */

import classnames from 'classnames';

import { filter, get, pick } from 'lodash';


/**
 * Internal dependencies
 */

import CarouselImage from './edit-image';


const effectOptions = [
	{ value: 'fade', label: _x( 'Fade', 'Carousel transition type', 'maxson' ) },
	{ value: 'scroll', label: _x( 'Scroll', 'Carousel transition type', 'maxson' ) }
];

const ALLOWED_MEDIA_TYPES = [ 'image' ];


const pickRelevantMediaFiles = ( image, imageSize = "project_large" ) => {
	const imageProps = Object.fromEntries(
		Object.entries( image ?? {} ).filter( ( [ key ] ) =>
			[ 'id', 'alt', 'caption', 'title' ].includes( key )
		)
	);

	imageProps.url =
		image?.sizes?.[ imageSize ]?.url ||
		image?.media_details?.sizes?.[ imageSize ]?.source_url ||
		image.url;

	const urlFull = 
		image?.imageSize?.full?.url || 
		image?.media_details?.imageSize?.full?.source_url;

	if( urlFull )
	{ 
		imageProps.urlFull = urlFull;

	} // endif

	return imageProps;
};


const getRelevantMediaFiles = async ( currentImages, imageSize = "project_large" ) => { 
	return await Promise.all( 
		currentImages.map( async ( image ) => { 
			const imageId = parseInt( image.id );
			let theImage = await wp.data.select( 'core' ).getMedia( imageId );
			//let theImage = await wp.data.select( 'core' ).getEntityRecords( 'postType', 'attachment', { include : image.id } );

			if( ! theImage ) { 
				theImage = image;
			}

			const imageProps = pick( theImage, [ 'id', 'link' ] );

			imageProps.alt = 
				get( theImage, [ 'alt_text' ] ) || 
				get( theImage, [ 'alt' ] ) || undefined;

			imageProps.caption = 
				get( theImage, [ 'caption' ] ) || 
				get( theImage, [ 'caption', 'raw' ] ) || undefined;

			imageProps.url = 
				get( theImage, [ 'media_details', 'sizes', imageSize, 'source_url' ] ) || 
				get( theImage, [ 'sizes', imageSize, 'url' ] ) || 
				theImage.source_url;

			const urlFull = 
				theImage?.imageSize?.full?.url || 
				theImage?.media_details?.imageSize?.full?.source_url;

			if( urlFull )
			{ 
				imageProps.urlFull = urlFull;

			} // endif

			const urlThumb = 
				theImage?.imageSize?.thumbnail?.url || 
				theImage?.media_details?.imageSize?.thumbnail?.source_url;

			if( urlThumb )
			{ 
				imageProps.urlThumb = urlThumb;

			} // endif

			return imageProps;
		} )
	);
};


/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return { WPElement } Element to render.
 */
export default function Edit( 
	{ 
		attributes, 
		isSelected, 
		noticeUI, 
		setAttributes
	} ) {

		const { 
			images, 
			autoplay, 
			pauseOnAction, 
			pauseOnHover, 
			speed, 
			effect, 
			showNavigation, 
			showPagination, 
			imageResolution, imageCrop } = attributes;

		let [ selectedImage, setSelectedImage ] = useState( null );

		const { createSuccessNotice, createErrorNotice } = useDispatch( noticesStore );


		const blockProps = useBlockProps( { 
			"className" : classnames( { 
				'is-cropped' : imageCrop
			} )
		} );


		const imageResolutionOptions = wp.data.select( 'core/block-editor' ).getSettings().imageSizes.map( ( { name, slug } ) => ( { 
			value: slug, 
			label: name
		} ) );


		const onResolutionChange = async ( imageSize ) => {
			setAttributes( { imageResolution: imageSize } );

			try {
				const imageData = await getRelevantMediaFiles( images, imageSize );
				const imageSizeData = imageResolutionOptions.find( ( size ) => size.value === imageSize );

				setAttributes( { images: imageData } );

				createSuccessNotice( 
					sprintf( __( 'All carousel image sizes updated to: %s', 'maxson' ), imageSizeData.label ),
					{
						id: 'maxson-portfolio-carousel-attributes-imageSize',
						type: 'snackbar'
					}
				)
			} catch ( error )
			{ 
				console.error( error );

			};
		}


		const OnMediaSelect = ( images ) => {
			const imageData = images.map( ( image ) => pickRelevantMediaFiles( image, imageResolution ) )

			setAttributes( { 
				ids: images.map( ( image ) => { 
					return image.id 
				} ),
				images: imageData
			} );
		}


		const OnImageSelect = ( index ) => {
			return () => {
				if ( selectedImage !== index ) {
					selectedImage = index
				}
			};
		}


		const OnImageRemove = ( index ) => {
			return () => {
				const images = filter( attributes.images, ( image, i ) => index !== i );

				selectedImage = null;

				setAttributes( { images: images } );
			};
		}


		const onImageMove = ( oldIndex, newIndex ) => { 
			let newImages = [ ...images ];

			newImages.splice( newIndex, 1, images[ oldIndex ] );
			newImages.splice( oldIndex, 1, images[ newIndex ] );

			setSelectedImage( newIndex );

			setAttributes( { images: newImages } );
		}


		const onImageMoveForward = ( oldIndex ) => {
			if ( oldIndex === images.length - 1 ) {
				return;
			}

			onImageMove( oldIndex, oldIndex + 1 );
		}


		const onImageMoveBackward = ( oldIndex ) => {
			if ( oldIndex === 0 ) {
				return;
			}

			onImageMove( oldIndex, oldIndex - 1 );
		}
	

		function removeAllImages() {
	        setAttributes( {
	            images: []
	        } );

			createSuccessNotice( 
				__( 'All carousel images removed', 'maxson' ),
				{
					id: 'maxson-portfolio-carousel-images-removed',
					type: 'snackbar'
				}
			);
	    }


		if ( images.length === 0 ) {
			return (
				<div { ...blockProps }>
					<>
						<MediaPlaceholder
							icon="format-gallery"
							labels={ {
								title: __( 'Carousel', 'maxson' )
							} }
							onSelect={ OnMediaSelect }
							accept="image/*"
							allowedTypes={ ALLOWED_MEDIA_TYPES }
							multiple
							notices={ noticeUI }
						/>
					</>
				</div>
			);
		}

		return (
			<>
				{ noticeUI }

				<BlockControls>
					{ !! images.length && (
						<ToolbarGroup>
							<ToolbarItem>
								{ () => (
									<MediaUploadCheck>
										<MediaUpload
											onSelect={ OnMediaSelect }
											allowedTypes={ ALLOWED_MEDIA_TYPES }
											multiple
											gallery
											value={ images.map( ( image ) => image.id ) }
											render={ ( { open } ) => (
												<>
													<Button
														onClick={ open }
														className="components-toolbar__control"
														label={ __( 'Add/Edit Carousel Images', 'maxson' ) }
														isSmall
														icon="edit"
													/>
													<Button
														onClick={ removeAllImages }
														className="components-toolbar__control"
														label={ __( 'Clear Carousel Images' ) }
														icon="trash"
													/>
												</>
											) }
										/>
									</MediaUploadCheck>
								)}
							</ToolbarItem>
						</ToolbarGroup>
					) }
				</BlockControls>


				<InspectorControls>
					<PanelBody title={ __( 'Carousel Settings', 'maxson' ) } initialOpen={ true }>
						<ToggleControl
							label={ __( 'Autoplay', 'maxson' ) }
							checked={ !! autoplay }
							onChange={ ( value ) => {
								setAttributes( { autoplay: value } );
							} }
						/>
						{ autoplay ?
							<ToggleControl
								label={ __( 'Pause on action', 'maxson' ) }
								checked={ !! pauseOnAction }
								onChange={ ( value ) => {
									setAttributes( { pauseOnAction: value } );
								} }
							/>
						 	: ''
						}
						{ autoplay ?
							<ToggleControl
								label={ __( 'Pause on hover', 'maxson' ) }
								checked={ !! pauseOnHover }
								onChange={ ( value ) => {
									setAttributes( { pauseOnHover: value } );
								} }
							/>
						 	: ''
						}
						<TextControl
							label={ __( 'Speed', 'maxson' ) }
							type='number'
							min='100'
							max='500'
							value={ speed }
							onChange={ ( value ) => {
								setAttributes( { speed: value } );
							} }
						/>
						<SelectControl
							label={ __( 'Effect', 'maxson' ) }
							value={ effect }
							onChange={ ( value ) => {
								setAttributes( { effect: value } );
							} }
							options={ effectOptions }
						/>
					</PanelBody>

					<PanelBody title={ __( 'Carousel Controls', 'maxson' ) } initialOpen={ false }>
						<ToggleControl
							label={ __( 'Show Navigation', 'maxson' ) }
							checked={ !! showNavigation }
							onChange={ ( value ) => {
								setAttributes( { showNavigation: value } );
							} }
						/>
						<ToggleControl
							label={ __( 'Show Pagination', 'maxson' ) }
							checked={ !! showPagination }
							onChange={ ( value ) => {
								setAttributes( { showPagination: value } );
							} }
						/>
					</PanelBody>

					{ imageResolutionOptions?.length > 0 && (
						<PanelBody title={ __( 'Image Settings', 'maxson' ) } initialOpen={ false }>
							<SelectControl
								label={ __( 'Image Resolution', 'maxson' ) }
								help={ __( 'Select the size of the source images.', 'maxson' ) }
								value={ imageResolution }
								options={ imageResolutionOptions }
								onChange={ ( value ) => { 
									onResolutionChange( value );
								} }
							/>

							<ToggleControl
								label={ __( 'Crop Image', 'maxson' ) }
								help={ ( imageCrop ) => { 
									return !! imageCrop
									? __( 'Images are cropped to align.', 'maxson' )
									: __( 'Images are not cropped.', 'maxson' );
								} }
								checked={ !! imageCrop }
								onChange={ ( value ) => {
									setAttributes( { imageCrop: value } );
								} }
							/>
						</PanelBody>
					) }
				</InspectorControls>
				
				<div {...blockProps}>
					{ images.map( ( image, index ) => {	
						return (
							<div className="block-carousel-slide" key={ image.id || image.url }>
								<CarouselImage
									url={ image.url }
									alt={ image.alt }
									id={ image.id }
									caption={ image.caption }
									onSelect={ OnImageSelect( index ) }
									onRemove={ OnImageRemove( index ) }
									isSelected={ isSelected }
									setAttributes={ ( attrs ) => 
										setImageAttributes( index, attrs )
									}
									onMoveBackward={ () => { 
										onImageMoveBackward( index ) }
									}
									onMoveForward={ () => { 
										onImageMoveForward( index ) }
									}
									isFirstItem={ index === 0 }
									isLastItem={ ( index + 1 ) === images.length }
									imageCount={ images.length || 0 }
								/>
							</div>
						);
					} ) }
				</div>
			</>
		);

};