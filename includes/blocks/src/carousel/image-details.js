import {
	useSelect
} from '@wordpress/data';


/**
 * ImageProps
 *
 * This component is used to display an image from the media library.
 * It's meant as a JS companion to the PHP function `wp_get_attachment_image()`
 *
 * @link https://www.briancoords.com/getting-wordpress-media-library-images-in-javascript/
 * 
 * @param {object} props
 * @param {number} props.imageId The ID of the image to display
 * @param {string} props.size The size of the image to display
 */

export default function ImageProps( { imageId, size = 'project_large' } ) {

	const { image } = useSelect( ( select ) => ( { 
		image: select( 'core' ).getMedia( imageId )
	} ) );

	const imageAttributes = () => { 
		let attributes = { 
			src: image.source_url,
			alt: image.alt_text,
			caption: image.caption,
			title: image.title,
			className: `attachment-${size} size-${size}`,
			width: image.media_details.width,
			height: image.media_details.height
		};

		if( image.media_details && image.media_details.sizes && image.media_details.sizes[size] )
		{ 
			attributes.src = image.media_details.sizes[size].source_url;
			attributes.width = image.media_details.sizes[size].width;
			attributes.height = image.media_details.sizes[size].height;

		} // endif

		const urlFull = 
			image?.imageSize?.full?.url || 
			image?.media_details?.imageSize?.full?.source_url;

		if( urlFull )
		{ 
			attributes.urlFull = urlFull;

		} // endif

		return attributes;
	};

	return imageAttributes;
}