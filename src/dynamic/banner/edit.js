import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import './editor.scss';

export default function Edit({ attributes, setAttributes }) {
	const { customClassName } = attributes;
	const blockProps = useBlockProps({
		className: customClassName || '',
	});

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={__('Block Settings', 'my-plugin-boilerplate')}
				>
					<TextControl
						label={__('Custom Class', 'my-plugin-boilerplate')}
						value={customClassName}
						onChange={(value) =>
							setAttributes({ customClassName: value })
						}
						help={__(
							'Add a custom CSS class to this block',
							'my-plugin-boilerplate'
						)}
					/>
				</PanelBody>
			</InspectorControls>
			<p {...blockProps}>
				{__('Banner – hello from the editor!', 'my-plugin-boilerplate')}
			</p>
		</>
	);
}
