/**
 * External Dependencies
 */

import classnames from 'classnames';


/**
 * WordPress Dependencies
 */

import {
	__,
	_x
} from '@wordpress/i18n';


import {
	Button
} from '@wordpress/components';


import {
	isBlobURL
} from '@wordpress/blob';


export default function CarouselImage( 
{ 
    url, 
    alt, 
    id, 
    isSelected, 
    onSelect, 
    onRemove, 
    onMoveForward, 
	onMoveBackward, 
	isFirstItem, 
	isLastItem, 
	imageCount
} ) {
    const onImageClick = () => {
        if ( ! isSelected ) {
            onSelect();
        }
    }

    const className = classnames( {
        'is-selected': isSelected,
        'is-transient': isBlobURL( url )
    } );


    return (
        <figure className={ className }>
            { isSelected && 
                <>
					{ imageCount > 1 &&
	                    <div className="block-carousel-slide--action-reorder">
	    					<Button
	    						label={ isFirstItem ? undefined : _x( 'Move Image Backward', 'Carousel reorder help text', 'maxson' ) }
	    						icon="arrow-left"
	    						className="block-carousel-slide--action-move-backward"
								onClick={ isFirstItem ? undefined : onMoveBackward }
								isSmall
	    						aria-disabled={ isFirstItem }
	    						disabled= { ! isSelected }
	    					/>
	    					<Button
	    						label={ isLastItem ? undefined : _x( 'Move Image Forward', 'Carousel reorder help text', 'maxson' ) }
	    						className="block-carousel-slide--action-move-forward"
	    						onClick={ isLastItem ? undefined : onMoveForward }
								icon="arrow-right"
	    						isSmall
	    						aria-disabled={ isLastItem }
	    						disabled={ ! isSelected }
	    					/>
	    				</div>
					}

					<div className="block-carousel-slide--action-remove">
						<Button
							onClick={ onRemove }
							className="blocks-gallery-item__remove"
							label={ __( 'Remove Image' ) }
							isSmall
							isDestructive
							icon="no-alt"
						/>
					</div>
                </>
            }
            <img
                src={ url }
                alt={ alt }
				className="block-carousel-slide--image"
                tabIndex="0"
                data-id={ id }
                onClick={ onImageClick }
                onKeyDown={ onImageClick }
            />
        </figure>
    );
}