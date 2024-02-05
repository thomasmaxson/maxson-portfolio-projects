import { __ } from '@wordpress/i18n';

import ServerSideRender from '@wordpress/server-side-render';

import {
	InspectorControls,
	useBlockProps
} from '@wordpress/block-editor';

import {
	Disabled,
	PanelBody,
	PanelRow,
	TextControl,
	ToggleControl
} from '@wordpress/components';

import metadata from './block.json';
import './editor.scss';

export default function Edit( { attributes, setAttributes } ) {
	const blockProps = useBlockProps( {
		className: 'portfolio-project-archive-filters',
	} );

	const { filterResetShow, filterResetLabel } = attributes;

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Settings', 'maxson' ) } initialOpen={ true }>
					<PanelRow>
						<ToggleControl
							label={ __( 'Add "All" Link', 'maxson' ) }
							help={ __( 'This link allows for all items to be shown', 'maxson' ) }
							checked={ !! filterResetShow }
							onChange={ ( value ) => { 
								setAttributes( { filterResetShow: value } );
							} }
						/>
					</PanelRow>
					<PanelRow>
						{ filterResetShow ?
							<TextControl
								label={ __( 'Label', 'maxson' ) }
								value={ filterResetLabel }
								onChange={ ( value ) => { 
									setAttributes( { filterResetLabel: value } );
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