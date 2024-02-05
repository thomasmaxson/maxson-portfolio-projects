/**
 * WordPress dependenices
 */

import { useBlockProps } from '@wordpress/block-editor';


/**
 * The save function defines the way in which the different attributes should
 * be combined into the final markup, which is then serialized by the block
 * editor into `post_content`.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#save
 *
 * @return {WPElement} Element to render.
 */

export default function save( { attributes } ) {

	const { images, autoplay, pauseOnAction, pauseOnHover, speed, effect, showNavigation, showPagination, imageCrop } = attributes;

	let blockProps = useBlockProps.save( { 
		className: imageCrop ? 'is-cropped' : '',
		'data-autoplay': autoplay,
		'data-speed': speed,
		'data-effect': effect,
		'data-arrows': showNavigation,
		'data-dots': showPagination
	} )

	if( autoplay && pauseOnAction )
	{ 
		blockProps = { ...blockProps, 'data-pauseOnAction': pauseOnHover }
	}

	if( autoplay && pauseOnHover )
	{ 
		blockProps = { ...blockProps, 'data-pauseOnHover': pauseOnHover }
	}


	return (
		<ul { ...blockProps }>
			{ images.map( ( image ) => { 
				const img = <img 
					src={ image.url } 
					className={ image.id ? `wp-image-${ image.id }` : null } 
					alt={ image.alt } 
					title={ image.title } 
					data-url-full={ image.urFull } 
					data-url={ image.url } 
					data-id={ image.id } 
					data-link={ image.link } 
				/>;

				return (
					<li key={ image.id || image.url } className="carousel-slide">
						<figure>
							{ img }
							{ image.caption && image.caption.length > 0 && (
								<figcaption>{ image.caption }</figcaption>
							) }
						</figure>
					</li>
				);
			} ) }
		</ul>
	);
}