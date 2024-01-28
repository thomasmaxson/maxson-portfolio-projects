// // https://github.com/anna-werner/gutenberg-slider/blob/master/src
// https://github.com/pixelalbatross/slider-block/blob/main/src/slider/block.json
// https://github.com/woofx/easy-slider
// https://github.com/EssentialBlocks/parallax-slider-block

import { __, _x } from '@wordpress/i18n';

import {
	BlockControls,
	InspectorControls,
	MediaPlaceholder,
	MediaUpload,
	MediaUploadCheck,
	RichText,
	useBlockProps
} from '@wordpress/block-editor';

import './editor.scss';

import {
	BaseControl,
	Button,
	ButtonGroup,
	PanelBody,
	PanelRow,
	SelectControl,
	TextControl,
	ToggleControl,
	ToolbarGroup,
	ToolbarItem
} from '@wordpress/components';

const ALLOWED_MEDIA_TYPES = ['image'];


export default function Edit( { attributes, setAttributes } ) { 
	const blockProps = useBlockProps( {
		className: 'portfolio-project-carousel-block'
	} );

	const { 
		images, 
		links, 
		captions, 
		autoplay, 
		loop, 
		showNavigation, 
		showPagination, 
		transitionType, 
		linkType, 
		linkTarget
	} = attributes;


	const getNestedValue = ( data, index ) => {
		return data.length >= 1 ? data[ index ] : '';
	}


	const LinkChangeHandler = ( event, index ) => {
		let data = links;

		data[index] = event;

		links.splice( index, 1, event )

		setAttributes( { links: [...data] } );
	}


	const captionChangeHandler = ( event, index ) => {
		let data = captions;

		data[index] = event;

		captions.splice( index, 1, event )

		setAttributes( { captions: [...data] } );
	}


	const onSelectImages = ( event ) => { 
		setAttributes( { images: event } );
	};


	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Settings', 'maxson' ) } initialOpen={ true }>

					<PanelRow>
						<ToggleControl
							label={ __( 'Autoplay', 'maxson' ) }
							checked={ autoplay }
							onChange={ ( newValue ) => { 
								setAttributes( { autoplay: newValue } );
							} } />
					</PanelRow>
					<PanelRow>
						<ToggleControl
							label={ __( 'Loop', 'maxson' ) }
							checked={ loop }
							onChange={ ( newValue ) => { 
								setAttributes( { loop: newValue } );
							} } />
					</PanelRow>
					<PanelRow>
						<ToggleControl
							label={ __( 'Show Navigation', 'maxson' ) }
							checked={ showNavigation }
							onChange={ ( newValue ) => { 
								setAttributes( { showNavigation: newValue } );
							} } />
					</PanelRow>
					<PanelRow>
						<ToggleControl
							label={ __( 'Show Pagination', 'maxson' ) }
							checked={ showPagination }
							onChange={ ( newValue ) => { 
								setAttributes( { showPagination: newValue } );
							} } />
					</PanelRow>
					<PanelRow>
						<BaseControl label={ __( 'Transition', 'maxson' ) }>
						<ButtonGroup style={ { width: "100%"} }>
							<Button 
								isSecondary
								isPrimary={ transitionType == 'fade' }
								onClick={ () => 
									setAttributes( { transitionType: 'fade' } )
								}>{ __( 'Fade', 'Transition type label', 'maxson' ) }</Button>
							<Button
								isSecondary
								isPrimary={ transitionType == 'slide' }   onClick={ () => 
									setAttributes( { transitionType: 'slide' } )
								}>{ _x( 'Slide', 'Transition type label', 'maxson' ) }</Button>
						</ButtonGroup>
						</BaseControl>
					</PanelRow>
					<PanelRow>
						<SelectControl
							label={ __( 'Link Slide To', 'maxson' ) }
							value={ linkType }
							onChange={ ( value ) => { 
								setAttributes( { linkType: value } );
							} }
							options={ [ 
								{ 
									value: 'url', 
									label: __( 'Custom URL', 'maxson' )
								}, { 
									value: 'attachment', 
									label: __( 'Attachment Page', 'maxson' )
								}, { 
									value: 'media', 
									label: __( 'Media File', 'maxson' )
								}, { 
									value: 'none', 
									label: __( 'None', 'maxson' )
								}
							] }
						/>
					</PanelRow>
					{ linkType !== 'none' ?
						<PanelRow>
							<ToggleControl
								label={ __( 'Open link in new tab', 'maxson' ) }
								checked={ linkTarget }
								onChange={ ( newValue ) => { 
									setAttributes( { linkTarget: newValue } );
								} } />
						</PanelRow>
					 	: ''
					}
				</PanelBody>
			</InspectorControls>



			{ !! images.length ? (
				<>
					<BlockControls>
						<ToolbarGroup>
							<ToolbarItem>
								{ () => (
									<MediaUploadCheck>
										<MediaUpload
											onSelect={ onSelectImages }
											accept="image/*"
											allowedTypes={ ALLOWED_MEDIA_TYPES }
											value={ images.map( ( img ) => 
												img.id
											) }
											render={ ( { open } ) => (
												<Button
													onClick={ open }
													label={ __( 'Edit Carousel', 'maxson' ) }
													isSmall
													icon="edit"
												/>
											) }
											multiple={ true }
											gallery={ true }
										/>
									</MediaUploadCheck>
								)}
							</ToolbarItem>
						</ToolbarGroup>
					</BlockControls>

					<div { ...blockProps }>
					{ 
						images.map( ( item, index ) => ( 
							<div className="carousel-slide" key={ index }>
								<figure className="carousel-slide--figure">
									<img src={ item.url } alt={ item.alt }  className="carousel-slide--image" />
									<figcaption className="carousel-slide--details">
										<RichText 
											placeholder={ __( 'Slide Caption', 'maxson' ) }
											value={ getNestedValue( captions, index ) } 
											onChange={ ( event ) => 
												captionChangeHandler( event, index )
											}
										/>
									</figcaption>
								</figure>
								{ linkType === 'url' ?
									<div className="carousel-slide--extras">
										<TextControl 
											label={ __( 'Custom Link', 'maxson' ) }
											placeholder={ __( 'Enter URL (starting with http:// or https://)', 'maxson' ) }
											value={ getNestedValue( links, index ) } 
											onChange={ ( event ) => 
												LinkChangeHandler( event, index )
											}
										/>
									</div> : ''
								}
									
							</div>
						) )
					}
					</div>
				</>
			) : ( 
				<MediaPlaceholder
					icon="slides"
					labels={ { 
						title: __( 'Carousel', 'maxson' )
					} }
					onSelect={ onSelectImages }
					accept="image/*"
					allowedTypes={ ALLOWED_MEDIA_TYPES }
					multiple={ true }
					gallery={ true }
				/>

			) }
		</>
	);
}
