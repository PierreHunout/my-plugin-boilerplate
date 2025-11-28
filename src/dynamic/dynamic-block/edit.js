import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';
import { PanelBody, TextControl } from '@wordpress/components';
import './editor.scss';

export default function Edit({ attributes, setAttributes }) {
	// Get custom class from plugin settings
	const customClass = window.myPluginBoilerplateSettings?.customClass || '';
	const { customClassName } = attributes;

	const blockProps = useBlockProps({
		className:
			[customClass || '', customClassName || ''].filter(Boolean).join(' ') ||
			undefined,
	});

	const pages = useSelect((select) => {
		return select('core').getEntityRecords('postType', 'page', {
			per_page: -1,
			status: 'publish',
			orderby: 'title',
			order: 'asc',
		});
	}, []);

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
				<h3>{__('Dynamic Block Container', 'my-plugin-boilerplate')}</h3>

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

				<div className="wp-block-create-block-dynamic-block__pages">
					<h4>{__('Pages List', 'my-plugin-boilerplate')}</h4>
					{
						// eslint-disable-next-line prettier/prettier
						!pages && <p>{__('Loading pages…', 'my-plugin-boilerplate')}</p>
					}
					{pages && pages.length === 0 && (
						<p>{__('No pages found.', 'my-plugin-boilerplate')}</p>
					)}
					{pages && pages.length > 0 && (
						<ul>
							{pages.map((page) => (
								<li key={page.id}>
									<a href={page.link}>{page.title.rendered}</a>
								</li>
							))}
						</ul>
					)}
				</div>
			</div>
		</>
	);
}
