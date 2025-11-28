import { useBlockProps } from '@wordpress/block-editor';

export default function save({ attributes }) {
	const { customClassName } = attributes;

	const blockProps = useBlockProps.save({
		className: customClassName || '',
	});

	return <p {...blockProps}>{'Slider – hello from the saved content!'}</p>;
}
