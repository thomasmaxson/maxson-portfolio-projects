import { __ } from '@wordpress/i18n';

import ServerSideRender from '@wordpress/server-side-render';

import { InspectorControls, useBlockProps } from '@wordpress/block-editor';

import {
	Disabled,
	PanelBody,
	PanelRow,
	QueryControls,
	RangeControl
} from '@wordpress/components';

import metadata from './block.json';
import './editor.scss';

export default function Edit( { attributes, setAttributes } ) {
	const blockProps = useBlockProps( {
		className: 'portfolio-project-archive-block',
	} );

	const { columns, numberOfItems, order, orderBy } = attributes;

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={ __( 'Layout', 'maxson' ) }
					initialOpen={ true }
				>
					<PanelRow>
						<RangeControl 
							label={ __( 'Desktop Column Count', 'maxson' ) }
							help={ __( 'Maximum number of columns to display on large screens', 'maxson' ) }
							value={ columns }
							min={ 1 }
							max={ 6 }
							step={ 1 }
							onChange={ ( newValue ) =>
								setAttributes( { 
									columns: Number.isNaN( newValue ) ? 1 : newValue,
								} )
							}
						/>
					</PanelRow>

				</PanelBody>
				<PanelBody
					title={ __( 'Settings', 'maxson' ) }
					initialOpen={ false }
				>
					<QueryControls
						{ ...{ numberOfItems, order, orderBy } }
						onOrderChange={ ( newValue ) =>
							setAttributes( { order: newValue } )
						}
						onOrderByChange={ ( newValue ) =>
							setAttributes( { orderBy: newValue } )
						}
						onNumberOfItemsChange={ ( newValue ) =>
							setAttributes( { numberOfItems: newValue } )
						}
					/>
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