import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	InspectorControls,
	BlockControls,
} from '@wordpress/block-editor';
import {
	PanelBody,
	ToggleControl,
	TextControl,
	ToolbarGroup,
	ToolbarButton,
} from '@wordpress/components';

export default function Edit({ attributes, setAttributes }) {
	const { isDark, customClassName } = attributes;
	const blockProps = useBlockProps({
		className: [isDark ? 'dark-theme' : '', customClassName || '']
			.filter(Boolean)
			.join(' '),
	});

	return (
		<>
			<BlockControls>
				<ToolbarGroup>
					<ToolbarButton
						icon={isDark ? 'star-filled' : 'star-empty'}
						label={
							isDark
								? __(
										'Switch to Light Mode',
										'my-plugin-boilerplate'
									)
								: __(
										'Switch to Dark Mode',
										'my-plugin-boilerplate'
									)
						}
						onClick={() => setAttributes({ isDark: !isDark })}
						isPressed={isDark}
					/>
				</ToolbarGroup>
			</BlockControls>
			<InspectorControls>
				<PanelBody
					title={__('Toggle Settings', 'my-plugin-boilerplate')}
				>
					<ToggleControl
						label={__('Dark Mode', 'my-plugin-boilerplate')}
						checked={isDark}
						onChange={(value) => setAttributes({ isDark: value })}
						help={
							isDark
								? __(
										'Dark mode: enabled.',
										'my-plugin-boilerplate'
									)
								: __(
										'Dark mode: disabled.',
										'my-plugin-boilerplate'
									)
						}
					/>
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
			<div {...blockProps}>
				<p>
					{__(
						'Toggle – hello from the editor!',
						'my-plugin-boilerplate'
					)}
				</p>
				<p>
					{isDark
						? __('Current: Dark Mode', 'my-plugin-boilerplate')
						: __('Current: Light Mode', 'my-plugin-boilerplate')}
				</p>
			</div>
		</>
	);
}
