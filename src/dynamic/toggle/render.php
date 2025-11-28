<?php
// Generates a unique id for aria-controls.
$unique_id = wp_unique_id('p-');

// Get saved isDark value from block attributes
$is_dark = isset($attributes['isDark']) ? $attributes['isDark'] : false;
$custom_class_name = isset($attributes['customClassName']) ? $attributes['customClassName'] : '';

// Build wrapper classes
$classes = [];
if ($is_dark) {
	$classes[] = 'dark-theme';
}
if (!empty($custom_class_name)) {
	$classes[] = $custom_class_name;
}

// Adds the global state.
wp_interactivity_state(
	'create-block',
	[
		'isDark'    => $is_dark,
		'darkText'  => esc_html__('Switch to Light', 'my-plugin-boilerplate'),
		'lightText' => esc_html__('Switch to Dark', 'my-plugin-boilerplate'),
		'themeText'	=> $is_dark ? esc_html__('Switch to Light', 'my-plugin-boilerplate') : esc_html__('Switch to Dark', 'my-plugin-boilerplate')
	]
);
?>

<div
	<?php echo get_block_wrapper_attributes(['class' => implode(' ', $classes)]); ?>
	data-wp-interactive="create-block"
	<?php echo wp_interactivity_data_wp_context(['isOpen' => false]); ?>
	data-wp-watch="callbacks.logIsOpen"
	data-wp-class--dark-theme="state.isDark">
	<button
		data-wp-on--click="actions.toggleTheme"
		data-wp-text="state.themeText"></button>

	<button
		data-wp-on--click="actions.toggleOpen"
		data-wp-bind--aria-expanded="context.isOpen"
		aria-controls="<?php echo esc_attr($unique_id); ?>">
		<?php esc_html_e('Toggle', 'my-plugin-boilerplate'); ?>
	</button>

	<p
		id="<?php echo esc_attr($unique_id); ?>"
		data-wp-bind--hidden="!context.isOpen">
		<?php
		esc_html_e('Toggle - hello from an interactive block!', 'my-plugin-boilerplate');
		?>
	</p>
</div>