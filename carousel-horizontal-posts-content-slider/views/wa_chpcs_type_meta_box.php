<table class="wa-chpcs_input widefat" id="wa-chpcs_slider_options">
	<tbody>	
		<?php do_action( 'wa_options_meta_box_start', $slider_id ); ?>

		

		<!-- post type -->
		<tr id="slider_post_order">
			<td class="label">
				<label>
					<?php esc_html_e( 'Type of the content', 'chpcs' ); ?>
				</label>
				<p class="description"></p>
			</td>
			<td>
				<select id="wa_chpcs_query_posts_post_type" name="slider_options[post_type]" required>
					<option value=''>choose...</option>
					<option value="post" <?php selected( 'post', $slider_options['post_type'] ); ?>>Post</option>
					<option value="page" <?php selected( 'page', $slider_options['post_type'] ); ?>>Page</option>
					<option value="product" <?php selected( 'product', $slider_options['post_type'] ); ?>>Product</option>
					<option value="custom" <?php selected( 'custom', $slider_options['post_type'] ); ?> disabled>Custom Posts</option>
					<option disabled value='imageOrVideo' <?php selected( 'imageOrVideo', $slider_options['post_type'] ); ?> disabled>Image Or Video slides</option>
				</select> 
				<p class="description"><?php _e( 'Please, select a type of the content which you want to display. Image or video slides, custom post type options are available in the <a href="https://weaveapps.com/shop/wordpress-plugins/carousel-horizontal-posts-slider-wordpress-plugin/" target="_blank">pro version</a>', 'chpcs' ); ?></p>
				<span class="spinner"></span> <!-- Add this spinner class where you want it to appear--> 
			</td>
		</tr>

		<!-- taxonomy -->
		<!-- taxonomy -->
		<tr id="slider_post_order">
			<td class="label">
				<label>
					<?php esc_html_e( 'Taxonomy', 'chpcs' ); ?>
				</label>
				<p class="description"></p>
			</td>
			<td>
				<select id="wa_chpcs_query_posts_taxonomy" name="slider_options[post_taxonomy]">
					
				 <option value=''>choose...</option>
				<?php 

				$taxonomy_names = get_object_taxonomies($slider_options['post_type'] );
				if (!empty( $taxonomy_names )) {
					foreach ($taxonomy_names as $key => $value) {
						?>
				<option value="<?php echo esc_html_e($value); ?>" 
						<?php selected( $value, $slider_options['post_taxonomy'] ); ?>
					><?php echo esc_html_e($value); ?></option><?php } } ?></select>
			
					<ul>
						 <li><p class="description"><?php esc_html_e( 'If you want to display posts from categories, First select post type as post then select taxonomy as category. After that you will be able to select categories from the  categories field which will appear below. Please, leave this empty, if you want to display specific posts by posts Ids which you can fill in the post ids field in below.', 'chpcs' ); ?></p></li>
					</ul>
			
			</td>
		</tr>

		<!-- Post type -->
		<tr id="content_type" style="display: none;">
			<td class="label">
				<label>
					<?php esc_html_e( 'Display posts', 'chpcs' ); ?>
				</label>
				<p class="description"></p>
			</td>
			<td>
				<select id="wa_chpcs_query_content_type" name="slider_options[content_type]">
					<option value=''>choose...</option>
					<option value="category" 
					<?php selected( 'category', $slider_options['content_type'] ); ?>
					>Categories</option>
					<option value="newest" 
					<?php selected( 'newest', $slider_options['content_type'] ); ?>
					>Newest</option>
					<option value="tag" disabled
					<?php selected( 'tag', $slider_options['content_type'] ); ?>
					>Tags</option>
					<option value="most_viewed" disabled
					<?php selected( 'most_viewed', $slider_options['content_type'] ); ?>
					>Most viewed</option>

					<option value="related" disabled
					<?php selected( 'related', $slider_options['content_type'] ); ?>
					>Related posts (Only applies on carousels which are located in single post page.)</option>
					<option value="specific" disabled
					<?php selected( 'specific', $slider_options['content_type'] ); ?>
					>Specific posts by IDs</option>

				</select>

				<ul>
						 <li><p class="description"><?php _e( 'Tags, most Viewed, related, specific and more filtering options available in the <a href="https://weaveapps.com/shop/wordpress-plugins/carousel-horizontal-posts-slider-wordpress-plugin/" target="_blank">pro version</a>', 'chpcs' ); ?></p></li>
					</ul>
				
			</td>
		</tr>



		<!-- terms -->
		<tr id="slider_post_order" style="display: none;">
			<td class="label">
				<label>
					<?php esc_html_e( 'Categories / Terms ', 'chpcs' ); ?>
				</label>
				<p class="description"></p>
			</td>
			<td>
					<?php 
					if ('post'==$slider_options['post_type']) {

						$tax_selected = 'category';

					} else {

						$tax_selected = isset($slider_options['post_taxonomy']) ? $slider_options['post_taxonomy'] : '';
					}
					?>

					<select id="wa_chpcs_query_posts_terms" name="slider_options[post_terms][]" multiple>
					<option value=''>choose...</option>
					<?php

					 $categories = get_terms( $tax_selected , array(
							'post_type' => array($slider_options['post_type']  ),
							'fields' => 'all'

						));

					 if (!empty( $categories )) {
						 foreach ($categories as $key => $value) { 
								?>
						<option value="<?php echo isset($value->slug)&&!empty($value->slug) ?esc_html_e($value->slug) : '' ; ?>"


							 <?php 
								if (!empty( $slider_options['post_terms'] )) {

									foreach ($slider_options['post_terms'] as $contractor) {

										if ($value->slug==$contractor) {
											selected( $value->slug, $value->slug ); }
									}
								}
								?>
					 ><?php echo isset($value->name)&&!empty($value->name) ?esc_html_e($value->name) : '' ; ?></option><?php } } ?>
				</select>
				<p class="description"><?php echo esc_html_e('Please, hold down the control or command button to select multiple options.', 'chpcs'); ?></p>
			</td>
		</tr>


		<!-- tags -->
		<tr id="slider_post_order" style="display: none;">
			<td class="label">
				<label>
					<?php esc_html_e( 'Tags', 'chpcs' ); ?>
				</label>
				<p class="description"></p>
			</td>
			<td>
					<?php 
					if ('post'==$slider_options['post_type']) {

						$tax_selected = 'post_tag';

					} 
					?>

					<select id="wa_chpcs_query_posts_tags" name="slider_options[post_tags][]" multiple>
					<option value=''>choose...</option>
					<?php

					 $tags = get_terms( $tax_selected , array(
							'post_type' => array($slider_options['post_type']  ),
							'fields' => 'all'

						));

					 if (!empty( $tags )) {
						 foreach ($tags as $key => $value) { 
								?>
						<option value="<?php echo esc_html_e($value->slug); ?>"


							 <?php 
								if (!empty( $slider_options['post_tags'] )) {

									foreach ($slider_options['post_tags'] as $contractor) {

										if ($value->slug==$contractor) {
											selected( $value->slug, $value->slug ); }
									}
								}
								?>
					 ><?php echo isset($value->name)&&!empty($value->name) ?esc_html_e($value->name) : '' ; ?></option><?php } } ?>
				</select>
				<p class="description"><?php echo esc_html_e('Please, hold down the control or command button to select multiple options.', 'chpcs'); ?></p>
			</td>
		</tr>



			</tbody>
			</table>      