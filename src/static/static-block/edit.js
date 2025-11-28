import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	InnerBlocks,
	InspectorControls,
} from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';
import { PanelBody, TextControl } from '@wordpress/components';
import './editor.scss';

const TEMPLATE = [['create-block/banner', {}]];

export default function Edit({ clientId, attributes, setAttributes }) {
	// Get custom class from plugin settings
	const customClass = window.myPluginBoilerplateSettings?.customClass || '';
	const { customClassName } = attributes;

	// Check if toggle block already exists in inner blocks and get its isDark state
	const { hasToggleBlock, toggleIsDark } = useSelect(
		(select) => {
			const { getBlock } = select('core/block-editor');
			const block = getBlock(clientId);
			if (!block || !block.innerBlocks) {
				return { hasToggleBlock: false, toggleIsDark: false };
			}
			const toggleBlock = block.innerBlocks.find(
				(innerBlock) => innerBlock.name === 'create-block/toggle'
			);
			return {
				hasToggleBlock: !!toggleBlock,
				toggleIsDark: toggleBlock?.attributes?.isDark || false,
			};
		},
		[clientId]
	);

	// Allowed blocks - exclude toggle if one already exists
	const allowedBlocks = hasToggleBlock
		? ['create-block/banner', 'create-block/dynamic-block']
		: [
				'create-block/banner',
				'create-block/toggle',
				'create-block/dynamic-block',
			];

	const blockProps = useBlockProps({
		className:
			[customClass || '', customClassName || ''].filter(Boolean).join(' ') ||
			undefined,
	});

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Block Settings', 'my-plugin-boilerplate')}>
					<TextControl
						label={__('Custom Class', 'my-plugin-boilerplate')}
						value={customClassName}
						onChange={(value) => setAttributes({ customClassName: value })}
						help={__(
							'Add a custom CSS class to this block',
							'my-plugin-boilerplate'
						)}
					/>
				</PanelBody>
			</InspectorControls>
			<div {...blockProps}>
				<h3 className="text-2xl font-bold mb-4">
					{__('Static Block', 'my-plugin-boilerplate')}
				</h3>
				{customClass && (
					<span
						style={{
							fontSize: '0.8em',
							opacity: 0.6,
							marginLeft: '8px',
						}}
					>
						(Class: {customClass})
					</span>
				)}
				{hasToggleBlock && (
					<p
						style={{
							fontSize: '0.9em',
							padding: '8px',
							background: toggleIsDark ? '#333' : '#f0f0f0',
							color: toggleIsDark ? '#fff' : '#000',
							borderRadius: '4px',
						}}
					>
						{__('Toggle Block:', 'my-plugin-boilerplate')}{' '}
						{toggleIsDark
							? __('Dark Mode Active', 'my-plugin-boilerplate')
							: __('Light Mode Active', 'my-plugin-boilerplate')}
					</p>
				)}
				<div className="wp-block-create-block-static-block__content">
					{
						// eslint-disable-next-line prettier/prettier
						<InnerBlocks allowedBlocks={allowedBlocks} template={TEMPLATE} />
					}
				</div>
			</div>
		</>
	);
}
