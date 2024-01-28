import { __ } from '@wordpress/i18n';

import ServerSideRender from '@wordpress/server-side-render';

import { useBlockProps } from '@wordpress/block-editor';

import {
	Disabled
} from '@wordpress/components';

import metadata from './block.json';
import './editor.scss';

export default function Edit( { attributes, setAttributes } ) {
	const blockProps = useBlockProps( {
		className: 'portfolio-project-archive-filters',
	} );

	return (
		<>
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