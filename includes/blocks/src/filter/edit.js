import {
	__,
	_x
} from '@wordpress/i18n';


import ServerSideRender from '@wordpress/server-side-render';


import {
	InspectorControls,
	useBlockProps
} from '@wordpress/block-editor';


import {
	Disabled,
	PanelBody,
	PanelRow,
	SelectControl,
	TextControl,
	ToggleControl
} from '@wordpress/components';


import {
	useSelect
} from '@wordpress/data';


import metadata from './block.json';
import './editor.scss';


export default function Edit( { attributes, setAttributes } ) {
	const blockProps = useBlockProps( {
		className: 'portfolio-project-archive-filters',
	} );


	const { taxonomy, variation, resetShow, resetLabel } = attributes;


	const postType = useSelect( select => { 
	    const { getCurrentPostType } = select( 'core/editor' );
	    return getCurrentPostType();
	}, [] );


	const taxonomies = useSelect( select => {
	    const { getTaxonomies } = select( 'core' );
	    return getTaxonomies( { type: postType } );
	}, [ postType ] );


	const taxonomyOptions = taxonomies?.map( taxonomy => ( {
	    label: taxonomy.name, 
	    value: taxonomy.slug
	} ) );


	const variationOptions = [
		{ value: 'list', label: _x( 'List', 'Carousel transition type', 'maxson' ) },
		{ value: 'select', label: _x( 'Dropdown', 'Carousel transition type', 'maxson' ) }
	];


	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Settings', 'maxson' ) } initialOpen={ true }>
					<PanelRow>
						<SelectControl
							label={ __( 'Taxonomy', 'maxson' ) }
							value={ taxonomy }
							onChange={ ( value ) => { 
								setAttributes( { taxonomy: value } );
							} }
							options={ taxonomyOptions }
						/>
					</PanelRow>
					<PanelRow>
						<SelectControl
							label={ __( 'List Style', 'maxson' ) }
							value={ variation }
							onChange={ ( value ) => {
								setAttributes( { variation: value } );
							} }
							options={ variationOptions }
						/>
					</PanelRow>
					<PanelRow>
						<ToggleControl
							label={ __( 'Add "All" Link', 'maxson' ) }
							help={ __( 'This link allows for all items to be shown', 'maxson' ) }
							checked={ !! resetShow }
							onChange={ ( value ) => { 
								setAttributes( { resetShow: value } );
							} }
						/>
					</PanelRow>
					<PanelRow>
						{ resetShow ?
							<TextControl
								label={ __( 'Label', 'maxson' ) }
								value={ resetLabel }
								onChange={ ( value ) => { 
									setAttributes( { resetLabel: value } );
								} }
							/>
						 	: ''
						}
					</PanelRow>
				</PanelBody>
			</InspectorControls>


			<div { ...blockProps }>
				<Disabled>
					<ServerSideRender
						block={ metadata.name }
						skipBlockSupportAttributes
						attributes={ attributes }
					/>
				</Disabled>
			</div>
		</>
	);
}