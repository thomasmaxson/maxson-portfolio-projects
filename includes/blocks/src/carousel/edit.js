// https://github.com/stellarwp/kadence-blocks/blob/master/src/blocks/advancedgallery/edit.js
// https://github.com/Impuls-Werbeagentur/impuls-slick-slider-block/blob/master/impuls-slick-slider-block/blocks/src/slick-slider-block/edit.js


/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */

import { __ } from '@wordpress/i18n';


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

import { filter, get, pick } from 'lodash';


/**
 * Internal dependencies
 */

import CarouselImage from './edit-image';


const effectOptions = [
	{ value: 'fade', label: __( 'Fade', 'maxson' ) },
	{ value: 'scroll', label: __( 'Scroll', 'maxson' ) }
];

const slideResolutionOptions = [];

const ALLOWED_MEDIA_TYPES = [ 'image' ];

import useImageSizes from './use-image-sizes';


// export const pickRelevantMediaFiles = ( image, 'large' ) => {
// 	const imageProps = pick( image, [ 'alt', 'id', 'link', 'caption' ] );

// 	imageProps.src = get( image, [ 'sizes', 'large', 'url' ] ) || get( image, [ 'media_details', 'sizes', 'large', 'source_url' ] ) || image.url;

// 	return imageProps;
// };

export const pickRelevantMediaFiles = ( image, size ) => {
	const imageProps = Object.fromEntries(
		Object.entries( image ?? {} ).filter( ( [ key ] ) =>
			[ 'alt', 'id', 'link', 'caption' ].includes( key )
		)
	);

	imageProps.url =
		image?.sizes?.[ size ]?.url ||
		image?.media_details?.sizes?.[ size ]?.source_url ||
		image.url;

		return imageProps;
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

		const { images, autoplay, effect, pauseOnHover, showNavigation, showPagination, imageResolution, speed } = attributes;

		const [ selectedImage, setSelectedImage ] = useState( null );

		const blockProps = useBlockProps();
	
		if( images.length > 0 ) {
			imageResolutionOptions = useImageSizes(
				images[0],
				false,
				getSettings()
			);
		} // endif

		const OnMediaSelect = ( images ) => {
			const imageData = images.map( ( image ) => pickRelevantMediaFiles( image, 'project_large' ) )
			
			setAttributes( { images: imageData } );
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
				const images = filter( attributes.images, ( img, i ) => index !== i );

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


		const setImageAttributes = ( index, attrs ) => {
			const { images } = attributes;
			if ( ! images[ index ] ) {
				return;
			}
			setAttributes( { 
				images: [ 
					...images.slice( 0, index ),
					{
						...images[ index ],
						...attrs,
					},
					...images.slice( index + 1 ),
				],
			} );
		}
	

		function removeAllImages() {
	        setAttributes( {
	            images: []
	        } );
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
											value={ images.map( ( img ) => img.id ) }
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
					<PanelBody title={ __( 'Carousel Settings', 'maxson' ) }>
						<ToggleControl
							label={ __( 'Autoplay', 'maxson' ) }
							checked={ !! autoplay }
							onChange={ ( value ) => {
								setAttributes( { autoplay: value } );
							} }
						/>
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

						{ slideResolutionOptions?.length > 0 && (
							<SelectControl
								label={ __( 'Slide Resolution', 'maxson' ) }
								help={ __( 'Select the size of the source images.', 'maxson' ) }
								value={ imageResolution }
								options={ imageResolutionOptions }
								//onChange={ updateImagesSize }
								hideCancelButton={ true }
								//size="__unstable-large"
							/>
						) }
					</PanelBody>
				</InspectorControls>
				
				<div {...blockProps} >
					{ images.map( ( img, index ) => {	
						return (
							<div className="block-carousel-slide" key={ img.id || img.url }>
								<CarouselImage
									url={ img.url }
									alt={ img.alt }
									id={ img.id }
									caption={ img.caption }
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
								/>
							</div>
						);
					} ) }
				</div>
			</>
		);

};