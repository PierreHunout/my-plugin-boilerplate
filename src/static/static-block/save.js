import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';

export default function save({ attributes }) {
	// Get custom class from plugin settings
	const customClass = window.myPluginBoilerplateSettings?.customClass || '';
	const { customClassName } = attributes;

	const blockProps = useBlockProps.save({
		className:
			[customClass || '', customClassName || ''].filter(Boolean).join(' ') ||
			undefined,
	});

	return (
		<div {...blockProps}>
			<h3>Static Block</h3>
			<div className="wp-block-create-block-static-block__content">
				<InnerBlocks.Content />
			</div>
		</div>
	);
}
