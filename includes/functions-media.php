<?php
/**
 * Plugin-specific media functions
 * 
 * @author      Thomas Maxson
 * @package     Maxson_Portfolio_Projects/includes
 * @license     GPL-2.0+
 * @version     1.0
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) )
{ 
	exit();

} // endif


// http://coderrr.com/wordpress-remove-attachment-rows-where-file-doesnt-exist/


if( ! function_exists( 'maxson_portfolio_attachment_exists' ) )
{ 
	/**
	 * Determine if attachment ID is valid
	 * 
	 * @param       string $post_id (required) The ID of the attachment to check if exists
	 * @return      string
	 */

	function maxson_portfolio_attachment_exists( $attachment_id = null )
	{ 
		return ( ! is_null( $attachment_id ) && get_permalink( $attachment_id ) ) ? true : false;
	}
} // endif


/**
 * Get the placeholder image URL
 * 
 * @return      string
 */

function maxson_portfolio_placeholder_image_src( $type = null )
{ 
	$image_src = plugins_url( "admin/images/placeholder@2x.png", MAXSON_PORTFOLIO_FILE );

	if( ! is_null( $type ) && ! empty( $type ) && 'none' != $type )
	{ 
		$placeholder = plugins_url( "admin/images/placeholder-{$type}@2x.png", MAXSON_PORTFOLIO_FILE );

		if( file_exists( $placeholder ) )
		{ 
			$image_src = $placeholder;

		} // endif
	} // endif

	return apply_filters( 'maxson_portfolio_placeholder_image_src', $image_src, $type );
}


/**
 * Get the placeholder image
 *
 * @return      string
 */

function maxson_portfolio_placeholder_image( $type = null, $size = array( 60, 60 ), $args = array() )
{ 
	$attrs      = '';
	$size_class = $size;

	if( is_array( $size_class ) )
	{ 
		$size_class = join( 'x', $size_class );

	} // endif

	$default_args = array( 
		'src'   => maxson_portfolio_placeholder_image_src( $type ), 
		'class' => "maxson-portfolio-placeholder img-responsive size-{$size_class}", 
		'alt'   => _x( 'Placeholder', 'Image placeholder alt attribute value', 'maxson' )
	);

	$args = wp_parse_args( $args, $default_args );
 
	$args = apply_filters( 'maxson_portfolio_placeholder_image_attributes', $args, $size, $type );

	$args = array_unique( array_map( 'esc_attr', $args ) );

	foreach( $args as $attr => $value )
	{ 
		$value = ( 'src' == $attr ) ? esc_url( $value ) : esc_attr( $value );

		$attrs .= sprintf( ' %1$s="%2$s"', $attr, $value );

	} // endforeach

	$image = sprintf( '<img%1$s>', $attrs );

	return apply_filters('maxson_portfolio_placeholder_image', $image, $type, $size, $args );
}


if( ! function_exists( 'maxson_portfolio_get_term_thumbnail' ) )
{ 
	/**
	 * Get term thumbnail HTML
	 * 
	 * @param       int     $term_id
	 * @param       string  $size
	 * @param       array   $args
	 * @return      string
	 */

	function maxson_portfolio_get_term_thumbnail( $term_id = null, $size = 'project_thumbnail', $args = array() )
	{ 
		if( ! is_null( $term_id ) && ! is_wp_error( $term_id ) )
		{ 
			if( is_object( $term_id ) )
			{
				$term_id = $term_id->term_id;

			} // endif

			$thumbnail_id = get_term_meta( $term_id, '_thumbnail_id', true );

			if( $thumbnail_id && maxson_portfolio_attachment_exists( $thumbnail_id ) )
			{ 
				return wp_get_attachment_image( $thumbnail_id, $size, $args );

			} else
			{ 
				return false;

			} // endif
		} else
		{ 
			return false;

		} // endif
	}
}


/**
 * Get a image size
 * 
 * @param       mixed $size (optional [thumbnail, medium, or large]) Image size to retrieve information for
 * @return      array
 */

function maxson_portfolio_get_media_sizes( $size = 'thumbnail' )
{ 
	if( is_array( $size ) )
	{ 
		$option_w = isset( $size[0] ) ? $size[0] : '300';
		$option_h = isset( $size[1] ) ? $size[1] : '300';
		$option_c = isset( $size[2] ) ? $size[2] : true;

		$size = "{$option_w}x{$option_h}";

	} elseif( in_array( $size, array( 'thumbnail', 'medium', 'large' ) ) )
	{ 
		$option_w = maxson_portfolio_get_option( "media_{$size}_width", '300' );
		$option_h = maxson_portfolio_get_option( "media_{$size}_height", '300' );
		$option_c = maxson_portfolio_get_option( "media_{$size}_crop", false );

	} else 
	{ 
		$option_w = '300';
		$option_h = '300';
		$option_c = false;

	} // endif

	$sizes = array( 
		'width'  => $option_w, 
		'height' => $option_h, 
		'crop'   => $option_c
	);

	return apply_filters( "maxson_portfolio_media_{$size}_image_size", $sizes );
}

?>