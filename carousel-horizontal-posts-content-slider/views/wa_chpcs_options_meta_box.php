<table class="wa-chpcs_input widefat" id="wa-chpcs_slider_options">
	<tbody>	
		<?php do_action( 'wa_options_meta_box_start', $slider_id ); ?>

	
		<!-- post Order by -->
		<tr class="form-field form-required">
			<td class="label"><label for="name"><?php echo esc_html_e('Post order by', 'chpcs'); ?></label></td>
			<td><input type="text" name="slider_options[posts_order_by]"value="<?php 
if (empty($slider_options['posts_order_by'])) {
	echo'id';
} else {
	echo esc_html_e($slider_options['posts_order_by']);
}; 
?>" />
			<p class="description"><?php esc_html_e( 'e.g. ID (Possible values: id, author, _price,  title, date, category, modified)', 'chpcs' ); ?></p></td>
		</tr>

		<!-- post order -->
		<tr id="slider_post_order">
			<td class="label">
				<label>
					<?php esc_html_e( 'Post order', 'chpcs' ); ?>
				</label>
				<p class="description"></p>
			</td>
			<td>
				<select name="slider_options[post_order]">
					<option value="">none</option>
					<option value="asc" 
					<?php selected( 'asc', $slider_options['post_order'] ); ?>
					>ascending</option>
					<option value="desc" 
					<?php selected( 'desc', $slider_options['post_order'] ); ?>
					>descending</option>
					<option value="rand" 
					<?php selected( 'rand', $slider_options['post_order'] ); ?>
					>random</option>
				</select>
			</td>
		</tr>


		<!-- contents in a one order -->
				<tr id="slider_post_order">
			<td class="label">
				<label>
					<?php esc_html_e( 'Rearrange contents', 'chpcs' ); ?>
				</label>
				<p class="description"></p>
			</td>
			<td>
				<select id="wa_chpcs_contents_order" name="slider_options[contents_order][]"  multiple="multiple" required disabled>
					<option value=''>choose...</option>

					<?php 
						$contents_order = array();
						$items_order = array('category','image', 'title','date', 'excerpt', 'readmore','price','add_to_cart', 'rating');
						$default_contents = array('image','title', 'excerpt', 'readmore');
						$contents_order = $slider_options['contents_order'] ? $slider_options['contents_order']  : $default_contents;

					if (!empty($contents_order )) {

						$m_arr = array_unique(array_merge($contents_order, $items_order));

					} else {

						$m_arr =$items_order;

					}

					foreach ( $m_arr as $m_ar ) { 
						?>

					<option value="<?php echo esc_html_e($m_ar); ?>" 
						<?php 
						//selected( $m_ar, $items ) 
						if (in_array($m_ar, $contents_order )) {
							echo 'selected';
						} 
						?>
					><?php echo esc_html_e($m_ar); ?></option><?php } ?>

				</select>
				<p class="description"><?php _e( 'Please, drag and drop to sort the order. You can type to enter a custom text field to display data, this is available in the <a href="https://weaveapps.com/shop/wordpress-plugins/carousel-horizontal-posts-slider-wordpress-plugin/" target="_blank">pro version</a>', 'chpcs' ); ?></p>
			</td>
		</tr>

		<!-- Theme -->
		<tr id="slider_post_order">
			<td class="label">
				<label>
					<?php esc_html_e( 'Template', 'chpcs' ); ?>
				</label>
				<p class="description"></p>
			</td>
			<td>
				<select name="slider_options[template]" required>
							<option value="">choose...</option>
							<?php foreach ($this->list_themes() as  $value) { ?>
							<option value="<?php echo esc_html_e($value); ?>" 
								<?php selected( $value, $slider_options['template'] ); ?>
							><?php echo esc_html_e($value); ?></option>

							<?php }	?>
						
				</select><p class="description"><?php _e( 'The templates which are located in the template directry of the plugin are showing here. More theming options available in the <a target="_blank" href="https://weaveapps.com/shop/wordpress-plugins/carousel-horizontal-posts-slider-wordpress-plugin/">pro version</a>', 'chpcs' ); ?></p></td>
		</tr>


		<!-- image hover effect -->
		<tr id="image_hover_effect">
			<td class="label">
				<label>
					<?php esc_html_e( 'Image hover effects', 'chpcs' ); ?>
				</label>
				<p class="description"></p>
			</td>
			<td>
				<select name="slider_options[image_hover_effect]">
					<option value="none" 
					<?php selected( 'none', $slider_options['image_hover_effect'] ); ?>
					>none</option>
					<option value="hover_image" 
					<?php selected( 'hover_image', $slider_options['image_hover_effect'] ); ?>
					>image hover</option>
					<option value="grayscale" 
					<?php selected( 'grayscale', $slider_options['image_hover_effect'] ); ?>
					>greyscale</option>
					<option value="sepia" 
					<?php selected( 'sepia', $slider_options['image_hover_effect'] ); ?>
					>sepia</option>
					<option value="saturate" 
					<?php selected( 'saturate', $slider_options['image_hover_effect'] ); ?>
					>saturate</option>
					<option value="border" 
					<?php selected( 'border', $slider_options['image_hover_effect'] ); ?>
					>border around the image</option>
				</select>
				<p class="description"><?php esc_html_e( 'This will turn images to selected effect until user places their mouse over. Applies on basic theme.', 'chpcs' ); ?></p>
			</td>
		</tr>

		<!-- read more text -->
		<tr class="form-field form-required">
			<td class="label"><label for="name"><?php echo esc_html_e('Read more text', 'chpcs'); ?></label></td>
			<td><input type="text" name="slider_options[read_more_text]"value="
<?php 
if (empty($slider_options['read_more_text'])) {
	echo'Read more';
} else {
	echo esc_html_e($slider_options['read_more_text']);
}; 
?>
" />
			<p class="description"><?php esc_html_e( 'This text will be shown after the excerpt. e.g. Read more', 'chpcs' ); ?></p></td>
		</tr>

		<!-- Excerpt length -->
		<tr class="form-field form-required">
			<td class="label"><label for="name"><?php echo esc_html_e('Excerpt Length', 'chpcs'); ?></label></td>
			<td><input type="text" name="slider_options[word_limit]"value="
<?php 
if (empty($slider_options['word_limit'])) {
	echo'15';
} else {
	echo esc_html_e($slider_options['word_limit']);
}; 
?>
" onkeypress='return event.charCode >= 48 && event.charCode <= 57' />
			<p class="description"><?php esc_html_e( 'Character Limit. e.g. 10', 'chpcs' ); ?></p></td>
		</tr>

		<!-- Number of post to be shown -->
		<tr class="form-field form-required">
			<td class="label"><label for="name"><?php echo esc_html_e('Number of post to be shown in the slider', 'chpcs'); ?></label></td>
			<td><input type="text" name="slider_options[show_posts]"value="
<?php 
if (empty($slider_options['show_posts'])) {
	echo'20';
} else {
	echo esc_html_e($slider_options['show_posts']);
}; 
?>
" onkeypress='return event.charCode >= 48 && event.charCode <= 57' />
			<p class="description"><?php esc_html_e( 'e.g. 20', 'chpcs' ); ?></p></td>
		</tr>

		<!-- Number of posts to be shown in the page -->
		<tr class="form-field form-required">
			<td class="label"><label for="name"><?php echo esc_html_e('Number of items to be shown in the page', 'chpcs'); ?></label></td>
			<td><input type="text" name="slider_options[show_posts_per_page]"value="
<?php 
if (empty($slider_options['show_posts_per_page'])) {
	echo'20';
} else {
	echo esc_html_e($slider_options['show_posts_per_page']);
}; 
?>
" onkeypress='return event.charCode >= 48 && event.charCode <= 57' />
			<p class="description"><?php esc_html_e( 'e.g. 3', 'chpcs' ); ?></p></td>
		</tr>

		<!-- Number of items to be scroll -->
		<tr class="form-field form-required">
			<td class="label"><label for="name"><?php echo esc_html_e('Number of items to be scroll in one transition', 'chpcs'); ?></label></td>
			<td><input type="text" name="slider_options[items_to_be_slide]"value="
<?php 
if (empty($slider_options['items_to_be_slide'])) {
	echo'0';
} else {
	echo esc_html_e($slider_options['items_to_be_slide']);
}; 
?>
" onkeypress='return event.charCode >= 48 && event.charCode <= 57' />
			<p class="description"><?php esc_html_e( 'e.g. 0 (if zero, value will be automatically set to the size of the page.', 'chpcs' ); ?></p></td>
		</tr>

		<!-- Speed of transition -->
		<tr class="form-field form-required">
			<td class="label"><label for="name"><?php echo esc_html_e('Speed of transition', 'chpcs'); ?></label></td>
			<td><input type="text" name="slider_options[duration]"value="
<?php 
if (empty($slider_options['duration'])) {
	echo'500';
} else {
	echo esc_html_e($slider_options['duration']);
}; 
?>" onkeypress='return event.charCode >= 48 && event.charCode <= 57' />
			<p class="description"><?php esc_html_e( 'The duration of the scroll animation in milliseconds. e.g. 500', 'chpcs' ); ?></p></td>
		</tr>

		<!-- timeout of element -->
		<tr class="form-field form-required">
			<td class="label"><label for="name"><?php echo esc_html_e('Timeout between elements', 'chpcs'); ?></label></td>
			<td><input type="text" name="slider_options[timeout]"value="
<?php 
if (empty($slider_options['timeout'])) {
	echo'3000';
} else {
	echo esc_html_e($slider_options['timeout']);
}; 
?>
" onkeypress='return event.charCode >= 48 && event.charCode <= 57' />
			<p class="description"><?php esc_html_e( 'Set the time between transitions. Only applies if Auto scroll true.', 'chpcs' ); ?></p></td>
		</tr>

		<!-- Align of the image-->
		<tr id="image_type">
			<td class="label">
				<label>
					<?php esc_html_e( 'Alignment of the text', 'chpcs' ); ?>
				</label>
				<p class="description"></p>
			</td>
			<td>
				<select name="slider_options[text_align]">
					<option value="left" 
					<?php selected( 'left', $slider_options['text_align'] ); ?>
					>Left</option>
					<option value="right" 
					<?php selected( 'right', $slider_options['text_align'] ); ?>
					>Right</option>
					<option value="center" 
					<?php selected( 'center', $slider_options['text_align'] ); ?>
					>Center</option>
				</select>
			</td>
		</tr>

		<!-- Item width -->
		<tr class="form-field form-required">
			<td class="label"><label for="name"><?php echo esc_html_e('General width of items', 'chpcs'); ?></label></td>
			<td><input type="text" name="slider_options[item_width]"value="
<?php 
if (empty($slider_options['item_width'])) {
	echo'250';
} else {
	echo esc_html_e($slider_options['item_width']);
}; 
?>
" onkeypress='return event.charCode >= 48 && event.charCode <= 57' />
			<p class="description"><?php esc_html_e( 'Width of one item in the carousel(PX). e.g. 250', 'chpcs' ); ?></p></td>
		</tr>

		<!-- Item height -->
		<tr class="form-field form-required">
			<td class="label"><label for="name"><?php echo esc_html_e('General height of items', 'chpcs'); ?></label></td>
			<td><input type="text" name="slider_options[item_height]"value="
<?php 
if (empty($slider_options['item_height'])) {
	echo'320';
} else {
	echo esc_html_e($slider_options['item_height']);
}; 
?>
"  onkeypress='return event.charCode >= 48 && event.charCode <= 57'/>
			<p class="description"><?php esc_html_e('Height of one item in the carousel(PX). e.g. 320', 'chpcs' ); ?></p></td>
		</tr>


		<!-- Thumbnail image -->
		<tr id="image_type">
			<td class="label">
				<label>
					<?php esc_html_e( 'Image size', 'chpcs' ); ?>
				</label>
				<p class="description"></p>
			</td>
			<td>
				<select name="slider_options[image_size]">
					<option value="thumbnail" 
					<?php selected( 'thumbnail', $slider_options['image_size'] ); ?>
					>thumbnail</option>
					<option value="medium" 
					<?php selected( 'medium', $slider_options['image_size'] ); ?>
					>medium</option>
						<option value="large" 
					<?php selected( 'large', $slider_options['image_size'] ); ?>
					>large</option>
						<option value="full" 
					<?php selected( 'full', $slider_options['image_size'] ); ?>
					>full</option>
						<option value="other" 
					<?php selected( 'other', $slider_options['image_size'] ); ?>
					>other</option>
				</select>
				<p class="description"><?php esc_html_e( 'The default image sizes of WordPress are "thumbnail" (and its "thumb" alias), "medium", "large" and "full" (the image you uploaded). These image sizes can be configured in the WordPress Administration Media panel under Settings > Media. If you select other, the size will be automatically selected to the hight or width provided otherwise Thumbnail size will be used. only applies on featured image.', 'chpcs' ); ?></p></td>
	
			</td>
		</tr>

		<!-- Align of the image-->
		<tr id="image_type">
			<td class="label">
				<label>
					<?php esc_html_e( 'Alignment of the image', 'chpcs' ); ?>
				</label>
				<p class="description"></p>
			</td>
			<td>
				<select name="slider_options[image_align]">
					<option value="left" 
					<?php selected( 'left', $slider_options['image_align'] ); ?>
					>Left</option>
					<option value="right" 
					<?php selected( 'right', $slider_options['image_align'] ); ?>
					>Right</option>
					<option value="center" 
					<?php selected( 'center', $slider_options['image_align'] ); ?>
					>Center</option>
				</select>
			</td>
		</tr>

		<!-- Post image width -->
		<tr class="form-field form-required">
			<td class="label"><label for="name"><?php echo esc_html_e('Post image width (optional)', 'chpcs'); ?></label></td>
			<td><input type="text" name="slider_options[post_image_width]"value="
<?php 
if (empty($slider_options['post_image_width'])) {
	echo'';
} else {
	echo esc_html_e($slider_options['post_image_width']);
}; 
?>
" onkeypress='return event.charCode >= 48 && event.charCode <= 57' />
			<p class="description"><?php esc_html_e( 'Width of image in the carousel(PX). e.g. 250 If this field empty, images width will be determined to the image height. if the both height and width fields are empty, the images sizes will be changed to the size of the image automatically.', 'chpcs' ); ?></p></td>
		</tr>

		<!-- Post image height -->
		<tr class="form-field form-required">
			<td class="label"><label for="name"><?php echo esc_html_e('Post image height (optional)', 'chpcs'); ?></label></td>
			<td><input type="text" name="slider_options[post_image_height]"value="
<?php 
if (empty($slider_options['post_image_height'])) {
	echo'';
} else {
	echo esc_html_e($slider_options['post_image_height']);
}; 
?>
" onkeypress='return event.charCode >= 48 && event.charCode <= 57' />
			<p class="description"><?php esc_html_e( 'Height of image in the carousel(PX). e.g. 150. If this field empty, images height will be determined to the image width. if the both height and width fields are empty, the images sizes will be changed to the size of the image automatically.', 'chpcs' ); ?></p></td>
		</tr>

		<!-- Post image type -->
		<tr id="image_type">
			<td class="label">
				<label>
					<?php esc_html_e( 'Post image type', 'chpcs' ); ?>
				</label>
				<p class="description"></p>
			</td>
			<td>
				<select name="slider_options[image_type]">
					<option value="featured_image" 
					<?php selected( 'featured_image', $slider_options['image_type'] ); ?>
					>Featured image of post</option>
					<option value="first_image" 
					<?php selected( 'first_image', $slider_options['image_type'] ); ?>
					>First image of post</option>
					<option value="last_image" 
					<?php selected( 'last_image', $slider_options['image_type'] ); ?>
					>Last image of post</option>
					<option value="custom_field" 
					<?php selected( 'custom_field', $slider_options['image_type'] ); ?>
					>Custom Field</option>
				</select>
			</td>
		</tr>

		<!-- custom field name for featured image -->
		<tr class="form-field form-required">
			<td class="label"><label for="name"><?php echo esc_html_e('Custom field name of featured image', 'chpcs'); ?></label></td>
			<td><input type="text" name="slider_options[featured_image_custom_field_name]"value="<?php if (isset($slider_options['featured_image_custom_field_name'])&&!empty($slider_options['featured_image_custom_field_name'])) {
	echo esc_html_e($slider_options['featured_image_custom_field_name']);
}; 
?>" />
			<p class="description"><?php esc_html_e( 'Custom field name e.g. featured_image_field_name', 'chpcs' ); ?></p></td>
		</tr>

		<!-- custom field name for excerpt -->
		<tr class="form-field form-required">
			<td class="label"><label for="name"><?php echo esc_html_e('Custom field name of excerpt', 'chpcs'); ?></label></td>
			<td><input type="text" name="slider_options[excerpt_custom_field_name]"value="<?php 
if (isset($slider_options['excerpt_custom_field_name'])&&!empty($slider_options['excerpt_custom_field_name'])) {
	echo esc_html_e($slider_options['excerpt_custom_field_name']);
}; 
?>" />
			<p class="description"><?php esc_html_e( 'Custom field name e.g. excerpt_field_name', 'chpcs' ); ?></p></td>
		</tr>

	

		<!-- Slider easing effect -->
		<tr id="display_image">
			<td class="label">
				<label>
					<?php esc_html_e( 'Slider transition effect', 'chpcs' ); ?>
				</label>
				<p class="description"></p>
			</td>
			<td>
				<select name="slider_options[easing_effect]">
					<option value="linear" 
					<?php selected( 'linear', $slider_options['easing_effect'] ); ?>
					>linear</option>
					<option value="swing" 
					<?php selected( 'swing', $slider_options['easing_effect'] ); ?>
					>swing</option>
					<option value="quadratic" 
					<?php selected( 'quadratic', $slider_options['easing_effect'] ); ?>
					>quadratic</option>
					<option value="cubic" 
					<?php selected( 'cubic', $slider_options['easing_effect'] ); ?>
					>cubic</option>
					<option value="elastic" 
					<?php selected( 'elastic', $slider_options['easing_effect'] ); ?>
					>elastic</option>
				</select>
			</td>
		</tr>

		<!-- Transition effect -->
		<tr id="display_image">
			<td class="label">
				<label>
					<?php esc_html_e( 'Easing effects', 'chpcs' ); ?>
				</label>
				<p class="description"></p>
			</td>
			<td>
				<select name="slider_options[fx]">
					<option value="scroll" 
					<?php selected( 'scroll', $slider_options['fx'] ); ?>
					>scroll</option>
					<option value="directscroll" 
					<?php selected( 'directscroll', $slider_options['fx'] ); ?>
					>directscroll</option>
					<option value="fade" 
					<?php selected( 'fade', $slider_options['fx'] ); ?>
					>fade</option>
					<option value="crossfade" 
					<?php selected( 'crossfade', $slider_options['fx'] ); ?>
					>crossfade</option>
					<option value="cover" 
					<?php selected( 'cover', $slider_options['fx'] ); ?>
					>cover</option>
					<option value="cover-fade" 
					<?php selected( 'cover-fade', $slider_options['fx'] ); ?>
					>cover-fade</option>
					<option value="uncover" 
					<?php selected( 'uncover', $slider_options['fx'] ); ?>
					>uncover</option>
					<option value="uncover-fade" 
					<?php selected( 'uncover-fade', $slider_options['fx'] ); ?>
					>uncover-fade</option>
					<option value="none" 
					<?php selected( 'none', $slider_options['fx'] ); ?>
					>none</option>
				</select>
			</td>
		</tr>

		<!-- Direction to scroll -->
		<tr id="display_image">
			<td class="label">
				<label>
					<?php esc_html_e( 'Direction to scroll the carousel', 'chpcs' ); ?>
				</label>
				<p class="description"></p>
			</td>
			<td>
				<select name="slider_options[direction]">
					<option value="right" 
					<?php selected( 'right', $slider_options['direction'] ); ?>
					>right</option>
					<option value="left" 
					<?php selected( 'left', $slider_options['direction'] ); ?>
					>left</option>
					<option value="up" 
					<?php selected( 'up', $slider_options['direction'] ); ?>
					>up</option>
					<option value="down" 
					<?php selected( 'down', $slider_options['direction'] ); ?>
					>down</option>
				</select>
				<p class="description"><?php esc_html_e( 'Please, select right or left to display slider horizontally up or down to display vertically.', 'chpcs' ); ?></p>
			</td>
		</tr>

		<!-- Align items -->
		<tr id="display_image">
			<td class="label">
				<label>
					<?php esc_html_e( 'Align the items in Slider', 'chpcs' ); ?>
				</label>
				<p class="description"></p>
			</td>
			<td>
				<select name="slider_options[align_items]">
					<option value="center" 
					<?php selected( 'center', $slider_options['align_items'] ); ?>
					>center</option>
					<option value="left" 
					<?php selected( 'left', $slider_options['align_items'] ); ?>
					>left</option>
					<option value="right" 
					<?php selected( 'right', $slider_options['align_items'] ); ?>
					>right</option>
				</select>
			</td>
		</tr>

		<!-- Font colour -->
		<tr class="form-field form-required">
			<td class="label"><label for="name"><?php echo esc_html_e('Font colour', 'chpcs'); ?></label></td>
			<td><input type="text" id ="wa_chpcs_font_colour" name="slider_options[font_colour]"value="<?php 
if (empty($slider_options['font_colour'])) {
	echo'#000';
} else {
	echo esc_html_e($slider_options['font_colour']);
}; 
?>" />
			<p class="description"><?php esc_html_e( 'Font colour', 'chpcs' ); ?></p></td>
		</tr>

		<!-- Control colour -->
		<tr class="form-field form-required">
			<td class="label"><label for="name"><?php echo esc_html_e('Direction arrows colour', 'chpcs'); ?></label></td>
			<td><input type="text" id ="wa_chpcs_control_colour" name="slider_options[control_colour]"value="
<?php 
if (empty($slider_options['control_colour'])) {
	echo'#000';
} else {
	echo esc_html_e($slider_options['control_colour']);
}; 
?>" />
			<p class="description"><?php esc_html_e( 'Next and Prev controls colour', 'chpcs' ); ?></p></td>
		</tr>

		<!-- Control background colour -->
		<tr class="form-field form-required">
			<td class="label"><label for="name"><?php echo esc_html_e('Direction arrows background colour', 'chpcs'); ?></label></td>
			<td><input type="text" id ="wa_chpcs_control_bg_colour" name="slider_options[control_bg_colour]"value="<?php 
if (empty($slider_options['control_bg_colour'])) {
	echo'#fff';
} else {
	echo esc_html_e($slider_options['control_bg_colour']);
}; 
?>" />
			<p class="description"><?php esc_html_e( 'Next and Prev controls background colour', 'chpcs' ); ?></p></td>
		</tr>

		<!-- Control colour -->
		<tr class="form-field form-required">
			<td class="label"><label for="name"><?php echo esc_html_e('Direction arrows hover colour', 'chpcs'); ?></label></td>
			<td><input type="text" id ="wa_chpcs_control_hover_colour" name="slider_options[arrows_hover_colour]"value="<?php 
if (empty($slider_options['arrows_hover_colour'])) {
	echo'#000';
} else {
	echo esc_html_e($slider_options['arrows_hover_colour']);
}; 
?>" />
			<p class="description"><?php esc_html_e( 'Next and Prev controls hover colour', 'chpcs' ); ?></p></td>
		</tr>

		<!-- Image hover colour -->
		<tr class="form-field form-required">
			<td class="label"><label for="name"><?php echo esc_html_e('Image hover colour', 'wps'); ?></label></td>
			<td><input type="text" id ="wa_chpcs_image_hover_colour" name="slider_options[hover_image_bg]"value="<?php 
if (empty($slider_options['hover_image_bg'])) {
	echo'rgba(40,168,211,.85)';
} else {
	echo esc_html_e($slider_options['hover_image_bg']);
}; 
?>" />
			<p class="description"><?php esc_html_e( 'Image hover effect background colour', 'wps' ); ?></p></td>
		</tr>

		<!-- Next pre length -->
		<tr class="form-field form-required">
			<td class="label"><label for="name"><?php echo esc_html_e('Size of direction arrows', 'chpcs'); ?></label></td>
			<td><input type="text" name="slider_options[size_arrows]"value="
<?php 
if (empty($slider_options['size_arrows'])) {
	echo'18';
} else {
	echo esc_html_e($slider_options['size_arrows']);
}; 
?>" onkeypress='return event.charCode >= 48 && event.charCode <= 57' />
			<p class="description"><?php esc_html_e( 'e.g. 18', 'chpcs' ); ?></p></td>
		</tr>

		<!-- Font size -->
		<tr class="form-field form-required">
			<td class="label"><label for="name"><?php echo esc_html_e('Title font size', 'chpcs'); ?></label></td>
			<td><input type="text" name="slider_options[title_font_size]"value="
<?php 
if (empty($slider_options['title_font_size'])) {
	echo'18';
} else {
	echo esc_html($slider_options['title_font_size']);
}; 
?>" onkeypress='return event.charCode >= 48 && event.charCode <= 57' />
			<p class="description"><?php esc_html_e( 'e.g. 18', 'chpcs' ); ?></p></td>
		</tr>

		<!-- general font size -->
		<tr class="form-field form-required">
			<td class="label"><label for="name"><?php echo esc_html_e('General font size', 'chpcs'); ?></label></td>
			<td><input type="text" name="slider_options[font_size]"value="<?php 
if (empty($slider_options['font_size'])) {
	echo'18';
} else {
	echo esc_html($slider_options['font_size']);
}; 
?>" onkeypress='return event.charCode >= 48 && event.charCode <= 57' />
			<p class="description"><?php esc_html_e( 'e.g. 18', 'chpcs' ); ?></p></td>
		</tr>




		<!-- Direction to scroll -->
		<tr id="display_image">
			<td class="label">
				<label>
					<?php esc_html_e( 'Typography', 'chpcs' ); ?>
				</label>
				<p class="description"></p>
			</td>
			<td>
				<select name="slider_options[font_family]" disabled>
				<option value="">default</option>

				<?php
				
				
				$plugins_path = plugins_url() . '/carousel-horizontal-posts-content-slider/views/webfonts.json';

				$request = file_get_contents( $plugins_path );

				$fonts = json_decode( $request );

				if(isset($fonts )) {
				foreach ( $fonts->items as $font ) { 

					
					?>
					
				
					<option value="<?php echo esc_html_e(str_replace(' ', '+', $font->family)); ?>" 	<?php selected(  str_replace(' ', '+', $font->family), $slider_options['font_family'] ); ?>><?php echo esc_html_e($font->family); ?></option>
					<?php } }?>
				</select>
				<p class="description"><?php _e( 'Please, select a font family if not default ones will be used. Over 800 Google fonts available in the <a target="_blank" href="https://weaveapps.com/shop/wordpress-plugins/carousel-horizontal-posts-slider-wordpress-plugin/">pro version</a>', 'chpcs' ); ?></p>
			</td>
		</tr>



		<!-- default image -->
		<tr class="form-field form-required">
			<td class="label"><label for="name"><?php echo esc_html_e('Default image', 'chpcs'); ?></label></td>
			<td><input type="text" name="slider_options[default_image]"value="<?php 
if (empty($slider_options['default_image'])) {
	echo esc_html_e(plugins_url() . '/carousel-horizontal-posts-content-slider/assets/images/default-image.jpg');
} else {
	echo esc_html_e($slider_options['default_image']);
}; 
?>"  />
			<p class="description"><?php esc_html_e( 'This image will be shown for posts which does not have images', 'chpcs' ); ?></p></td>
		</tr>

		<!-- loading image -->
		<tr class="form-field form-required">
			<td class="label"><label for="name"><?php echo esc_html_e('loader image', 'chpcs'); ?></label></td>
			<td><input type="text" name="slider_options[lazy_load_image]"value="
<?php 
if (empty($slider_options['lazy_load_image'])) {
	echo esc_html_e(plugins_url() . '/carousel-horizontal-posts-content-slider/assets/images/default-image.jpg');
} else {
	echo esc_html_e($slider_options['lazy_load_image']);
}; 
?>"  />
			<p class="description"><?php esc_html_e( 'This image will be used for lazy loading', 'chpcs' ); ?></p></td>
		</tr>

		<!-- hover image -->
		<tr class="form-field form-required">
			<td class="label"><label for="name"><?php echo esc_html_e('Image hover', 'wps'); ?></label></td>
			<td><input type="text" name="slider_options[hover_image_url]"value="
<?php 
if (empty($slider_options['hover_image_url'])) {
	echo esc_html_e(plugins_url() . '/carousel-horizontal-posts-content-slider/assets/images/hover.png');
} else {
	echo esc_html_e($slider_options['hover_image_url']);
}; 
?>"  />
			<p class="description"><?php esc_html_e( 'This image will be used for image hover effect.', 'wps' ); ?></p></td>
		</tr>

		<!-- start date -->
		<tr class="form-field form-required">
			<td class="label"><label for="name"><?php echo esc_html_e('Start date', 'chpcs'); ?></label></td>
			<td><input id="start_date"  type="text" name="slider_options[start_date]"value="
<?php 
if (empty($slider_options['start_date'])) {
	echo'';
} else {
	echo esc_html_e($slider_options['start_date']);
}; 
?>
"  disabled/>
			<p class="description"><?php _e( 'Please, leave empty to always show this carousel. You can schedule slider in the <a target="_blank" href="https://weaveapps.com/shop/wordpress-plugins/carousel-horizontal-posts-slider-wordpress-plugin/">pro version</a>', 'chpcs' ); ?></p></td>
		</tr>

		<!-- end date -->
		<tr class="form-field form-required">
			<td class="label"><label for="name"><?php echo esc_html_e('End date', 'chpcs'); ?></label></td>
			<td><input id="end_date"  type="text" name="slider_options[end_date]"value="
<?php 
if (empty($slider_options['end_date'])) {
	echo'';
} else {
	echo esc_html_e($slider_options['end_date']);
}; 
?>"  disabled/>
			<p class="description"><?php _e( 'Please, leave empty to always show this carousel. You can schedule slider in the <a target="_blank" href="https://weaveapps.com/shop/wordpress-plugins/carousel-horizontal-posts-slider-wordpress-plugin/">pro version</a>', 'chpcs' ); ?></p></td>
		</tr>


		<tr id="slider_controls">
			
			<td class="label">
				<label>
					<?php esc_html_e( 'Display options', 'chpcs' ); ?>
				</label>
				<p class="description">
					<?php esc_html_e( 'Enable or Disable different navigation and control options' , 'chpcs' ); ?>
				</p>
			</td>
			<td>

				<p>
					<label>
						<input type="checkbox" name="slider_options[show_title]" value="true"
						<?php 
						if (isset($slider_options['show_title'])) {
							checked( true, $slider_options['show_title'] ); } 
						?>
						 
						/><?php esc_html_e( 'Show post title' , 'chpcs' ); ?>
					</label>
				</p>

				<p>
					<label>
						<input type="checkbox" name="slider_options[show_image]" value="true"
						<?php 
						if (isset($slider_options['show_image'])) {
							checked( true, $slider_options['show_image'] ); } 
						?>
						 
						/><?php esc_html_e( 'Show image' , 'chpcs' ); ?>
					</label>
				</p>

				<p>
					<label>
						<input type="checkbox" name="slider_options[show_excerpt]" value="true"
						<?php 
						if (isset($slider_options['show_excerpt'])) {
							checked( true, $slider_options['show_excerpt'] ); } 
						?>
						 
						/><?php esc_html_e( 'Show excerpt' , 'chpcs' ); ?>
					</label>
				</p>


				<p>
					<label>
						<input type="checkbox" name="slider_options[show_read_more_text]" value="true"
<?php
if (isset($slider_options['show_read_more_text'])) {
	checked( true, $slider_options['show_read_more_text'] ); } 
?>
	
/><?php esc_html_e( 'Show read more text' , 'chpcs' ); ?>
					</label>

				</p>

				<p>
					<label>
						<input type="checkbox" name="slider_options[number_of_rows]" value="true" disabled
<?php
if (isset($slider_options['number_of_rows'])) {
	checked( true, $slider_options['number_of_rows'] ); } 
?>
	
/><?php esc_html_e( 'Enable grid view' , 'chpcs' ); ?>
					</label>
					<p class="description"><?php _e( 'If this is checked, this will enable two row carousel or display posts in two rows. This is available in the <a target="_blank" href="https://weaveapps.com/shop/wordpress-plugins/carousel-horizontal-posts-slider-wordpress-plugin/">pro version</a>' , 'chpcs' ); ?></p>
				
				</p>

				<p>
					<label>
						<input type="checkbox" name="slider_options[show_cats]" value="true" 
						<?php 
						if (isset($slider_options['show_cats'])) {
							checked( true, $slider_options['show_cats'] ); } 
						?>
						 
						/><?php esc_html_e( 'Show category name' , 'chpcs' ); ?>
					</label>
					<p class="description"><?php esc_html_e( 'This will show the name of category a post belongs to on top of the item.' , 'chpcs' ); ?></p>
				</p>


				<p>
					<label>
						<input type="checkbox" name="slider_options[show_date]" value="true"
						<?php 
						if (isset($slider_options['show_date'])) {
							checked( true, $slider_options['show_date'] ); } 
						?>
						 
						/><?php esc_html_e( 'Show date' , 'chpcs' ); ?>
					</label>
					<p class="description"><?php esc_html_e( 'This will show the date of a post belongs to on bottom of the title.' , 'chpcs' ); ?></p>
				</p>


				<p>
					<label>
						<input type="checkbox" name="slider_options[show_custom_feild]" value="true" disabled
						<?php 
						if (isset($slider_options['show_custom_feild'])) {
							checked( true, $slider_options['show_custom_feild'] ); } 
						?>
						 
						/><?php _e( 'If this is checked, this will show custom fields data. This option available in the <a target="_blank" href="https://weaveapps.com/shop/wordpress-plugins/carousel-horizontal-posts-slider-wordpress-plugin/">pro version</a>' , 'chpcs' ); ?>
					</label>
					<p class="description"><?php esc_html_e( 'This will show the data from the custom fields define in the rearrange contents.' , 'chpcs' ); ?></p>
				</p>


				<p>
					<label>
						<input type="checkbox" name="slider_options[show_custom_feilds]" value="true"
						<?php 
						if (isset($slider_options['show_custom_feilds'])) {
							checked( true, $slider_options['show_custom_feilds'] ); } 
						?>
						 
						/><?php esc_html_e( 'Show custom feilds' , 'chpcs' ); ?>
					</label>
				</p>

				<p>
					<label>
						<input type="checkbox" name="slider_options[excerpt_type]" value="true"
						<?php 
						if (isset($slider_options['excerpt_type'])) {
							checked( true, $slider_options['excerpt_type'] ); } 
						?>
						 
						/><?php esc_html_e( 'Pick text in excerpt field' , 'chpcs' ); ?>
					</label>
					<p class="description"><?php esc_html_e( 'If checked, text will be picked from excerpt field instead of post content area.' , 'chpcs' ); ?></p>
			
				</p>

				<p>
					<label>
						<input type="checkbox" name="slider_options[responsive]" value="true"
						<?php 
						if (isset($slider_options['responsive'])) {
							checked( true, $slider_options['responsive'] ); } 
						?>
						 
						/><?php esc_html_e( 'Change general width of items to fill the carousel' , 'chpcs' ); ?>
					</label>
						<p class="description"><?php esc_html_e( 'If uncheck, items will be centered to the page and width will not be changed. (only applies on horizontal carousels)' , 'chpcs' ); ?></p>
				</p>

				<p>
					<label>
						<input type="checkbox" name="slider_options[lightbox]" value="true" disabled
						<?php 
						if (isset($slider_options['lightbox'])) {
							checked( true, $slider_options['lightbox'] ); } 
						?>
						 
						/><?php esc_html_e( 'Lightbox' , 'chpcs' ); ?>
					</label>
					<p class="description"><?php _e( 'Uses Magnific popup for displaying images in lightbox. This option available in the <a target="_blank" href="https://weaveapps.com/shop/wordpress-plugins/carousel-horizontal-posts-slider-wordpress-plugin/">pro version</a>' , 'chpcs' ); ?></p>

				</p>

				<p>
					<label>
						<input type="checkbox" name="slider_options[lazy_loading]" value="true" disabled
						<?php 
						if (isset($slider_options['lazy_loading'])) {
							checked( true, $slider_options['lazy_loading'] ); } 
						?>
						 
						/><?php esc_html_e( 'Lazy loading' , 'chpcs' ); ?>
					</label>
					<p class="description"><?php _e( 'If checked, images outside of viewport wont be loaded before user scrolls to them. Uses lazyload plugin. This option available in the <a target="_blank" href="https://weaveapps.com/shop/wordpress-plugins/carousel-horizontal-posts-slider-wordpress-plugin/">pro version</a>' , 'chpcs' ); ?></p>
				</p>

				<p>
					<label>
						<input type="checkbox" name="slider_options[auto_scroll]" value="true"
						<?php 
						if (isset($slider_options['auto_scroll'])) {
							checked( true, $slider_options['auto_scroll'] ); } 
						?>
						 
						/><?php esc_html_e( 'Auto scroll slider' , 'chpcs' ); ?>
					</label>
				</p>


				<p>
					<label>
						<input type="checkbox" name="slider_options[touch_swipe]" value="true"
						<?php 
						if (isset($slider_options['touch_swipe'])) {
							checked( true, $slider_options['touch_swipe'] ); } 
						?>
						 
						/><?php esc_html_e( 'Touch Swipe' , 'chpcs' ); ?>
					</label>
					<p class="description"><?php esc_html_e( 'A carousel scrolled by swiping (or dragging on non-touch-devices). Uses touchSwipe plugin.' , 'chpcs' ); ?></p>
				</p>

				<p>
					<label>
						<input type="checkbox" name="slider_options[show_controls]" value="true"
						<?php 
						if (isset($slider_options['show_controls'])) {
							checked( true, $slider_options['show_controls'] ); } 
						?>
						 
						/><?php esc_html_e( 'Show direction arrows' , 'chpcs' ); ?>
					</label>
				</p>

				<p>
					<label>
						<input type="checkbox" name="slider_options[animate_controls]" value="true"
						<?php 
						if (isset($slider_options['animate_controls'])) {
							checked( true, $slider_options['animate_controls'] ); } 
						?>
						 
						/><?php esc_html_e( 'Show direction arrows only when mouse hovers over it. (only applies on horizontal carousels).' , 'chpcs' ); ?>
					</label>
				</p>

				<p>
					<label>
						<input type="checkbox" name="slider_options[show_paging]" value="true"
						<?php 
						if (isset($slider_options['show_paging'])) {
							checked( true, $slider_options['show_paging'] ); } 
						?>
						 
						/><?php esc_html_e( 'Show pagination ' , 'chpcs' ); ?>
					</label>
				</p>


				<p>
					<label>
						<input type="checkbox" name="slider_options[css_transitions]" value="true"
						<?php 
						if (isset($slider_options['css_transitions'])) {
							checked( true, $slider_options['css_transitions'] ); } 
						?>
						 
						/><?php esc_html_e( 'CSS3 Transtitions' , 'chpcs' ); ?>
					</label>
					<p class="description"><?php esc_html_e( 'Transition effect will be used CSS3 or hardware acceleration. Uses jquery.transit plugin.' , 'chpcs' ); ?></p>
				</p>

				<p>
					<label>
						<input type="checkbox" name="slider_options[circular]" value="true"
						<?php 
						if (isset($slider_options['circular'])) {
							checked( true, $slider_options['circular'] ); } 
						?>
						 
						/><?php esc_html_e('Circular' , 'chpcs' ); ?>
					</label>
					<p class="description"><?php esc_html_e( 'Determines whether the carousel should be circular.' , 'chpcs' ); ?></p>
				</p>

				<p>
					<label>
						<input type="checkbox" name="slider_options[infinite]" value="true"
						<?php 
						if (isset($slider_options['infinite'])) {
							checked( true, $slider_options['infinite'] ); } 
						?>
						 
						/><?php esc_html_e( 'Infinite' , 'chpcs' ); ?>
					</label>
					<p class="description"><?php esc_html_e('Determines whether the carousel should be infinite.' , 'chpcs' ); ?></p>
				</p>

				<p>
					<label>
						<input type="checkbox" name="slider_options[pause_on_hover]" value="true"
						<?php 
						if (isset($slider_options['pause_on_hover'])) {
							checked( true, $slider_options['pause_on_hover'] ); } 
						?>
						 
						/><?php esc_html_e( 'Make carousel pause when mouse hovers over it.' , 'chpcs' ); ?>
					</label>
				</p>

				<!-- woocommerce product options -->


				<p>
					<label>
						<input type="checkbox" id="wa_chpcs_show_rating"  name="slider_options[show_rating]" value="true"
						<?php 
						if (isset($slider_options['show_rating'])) {
							checked( true, $slider_options['show_rating'] ); } 
						?>
						 
						/><?php esc_html_e( 'Show ratings' , 'chpcs' ); ?>
					</label>
				</p>


								<p>
					<label>
						<input type="checkbox" id="wa_chpcs_show_price" name="slider_options[show_price]" value="true"
						<?php 
						if (isset($slider_options['show_price'])) {
							checked( true, $slider_options['show_price'] ); } 
						?>
						 
						/><?php esc_html_e( 'Show Price ' , 'carousel-horizontal-posts-content-slider' ); ?>
					</label>
				</p>

				<p>
					<label>
						<input type="checkbox" id="wa_chpcs_show_sale_text" name="slider_options[show_sale_text_over_image]" value="true"
						<?php 
						if (isset($slider_options['show_sale_text_over_image'])) {
							checked( true, $slider_options['show_sale_text_over_image'] ); } 
						?>
						 
						/><?php esc_html_e( 'Show post sale text over top of post image' , 'carousel-horizontal-posts-content-slider' ); ?>
					</label>
				</p>


				<p>
					<label>
						<input type="checkbox" id="wa_chpcs_show_add_to_cart"  name="slider_options[show_add_to_cart]" value="true"
						<?php 
						if (isset($slider_options['show_add_to_cart'])) {
							checked( true, $slider_options['show_add_to_cart'] ); } 
						?>
						 
						/><?php esc_html_e( 'Show Add to Cart' , 'carousel-horizontal-posts-content-slider' ); ?>
					</label>
				</p>

			</td>
		</tr>

		<!-- custom style -->
		<tr class="form-field form-required">
			<td class="label"><label for="name"><?php echo esc_html_e('Custom css', 'chpcs'); ?></label></td>
			<td><textarea name="slider_options[custom_css]" placeholder=".wa_chpcs_slider_title { color: #ccc !important; }">
			<?php 
			if (empty($slider_options['custom_css'])) {
				echo'';
			} else {
				echo esc_html_e($slider_options['custom_css']);
			}; 
			?>
			</textarea>
			<p class="description"><?php esc_html_e( 'custom styles or override existing styles to meet your requirements.', 'chpcs' ); ?></p></td>
		</tr>

	<?php do_action( 'wa_options_meta_box_end', $slider_id ); ?>
	</tbody>
</table>
