<?php

/**
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */
$custom_class_name = isset($attributes['customClassName']) ? $attributes['customClassName'] : '';
$wrapper_attributes = !empty($custom_class_name) ? get_block_wrapper_attributes(array('class' => $custom_class_name)) : get_block_wrapper_attributes();
?>
<p <?php echo $wrapper_attributes; ?>>
	<?php esc_html_e('Banner – hello from a dynamic block!', 'my-plugin-boilerplate'); ?>
</p>