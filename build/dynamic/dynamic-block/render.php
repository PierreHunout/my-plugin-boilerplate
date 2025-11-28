<?php

// Get all published pages
$pages = get_pages(
	[
		'sort_column' => 'post_title',
		'sort_order'  => 'ASC',
		'post_status' => 'publish'
	]
);

// Get custom class from plugin settings
$settings = get_option('my_plugin_boilerplate_settings', []);
$custom_class = isset($settings['features']['custom_class']) ? $settings['features']['custom_class'] : '';

// Get customClassName from block attributes
$custom_class_name = isset($attributes['customClassName']) ? $attributes['customClassName'] : '';

// Combine classes
$classes = array_filter(array($custom_class, $custom_class_name));

// Add custom class to block wrapper attributes
$wrapper_attributes = get_block_wrapper_attributes(
	[
		'class' => implode(' ', $classes)
	]
);
?>
<div <?php echo $wrapper_attributes; ?>>
	<h3><?php esc_html_e('Dynamic Block Container', 'my-plugin-boilerplate'); ?></h3>

	<div class="wp-block-create-block-dynamic-block__pages">
		<h4><?php esc_html_e('Pages List', 'my-plugin-boilerplate'); ?></h4>
		<?php if (empty($pages)) : ?>
			<p><?php esc_html_e('No pages found.', 'my-plugin-boilerplate'); ?></p>
		<?php else : ?>
			<ul>
				<?php foreach ($pages as $page) : ?>
					<li>
						<a href="<?php echo esc_url(get_permalink($page->ID)); ?>">
							<?php echo esc_html($page->post_title); ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>

	<div class="wp-block-create-block-dynamic-block__content">
		<h4><?php esc_html_e('Nested Blocks', 'my-plugin-boilerplate'); ?></h4>
		<?php echo $content; ?>
	</div>
</div>