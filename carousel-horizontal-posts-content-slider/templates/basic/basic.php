<?php
	/* template name: basic */
	
	//slider layout
	$slider_gallery.= '<div class="wa_chpcs_image_carousel" id="wa_chpcs_image_carousel' . $id . '">';

	$slider_gallery.='<ul id="wa_chpcs_foo' . $id . '" style="height:' . $wa_chpcs_query_posts_item_height . 'px; overflow: hidden;">';

foreach ($myposts as $wa_post) {

		$post_title = $wa_post->post_title; //post title 
		$post_link =  get_permalink($wa_post->ID); //post link
		$post_link_target = '_self'; //post url target
		$post_content = $wa_post->post_content; //post content
		$wa_post_id=	$wa_post->ID; //post id
		$post_excerpt = $wa_post->post_excerpt;//post excerpt

		$text_type = $this->get_text_type($wa_post, $wa_chpcs_query_display_from_excerpt, $wa_post_id, $wa_chpcs_excerpt_custom_field_name );

		//woocommerce get data
		if ('product'==$qp_post_type) {
			if ( function_exists( 'get_product' ) ) {
				$_product = wc_get_product( $wa_post->ID );
			} else {

				//check if woocommerce active
				if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
					$_product = new WC_Product( $wa_post->ID );
				}

			}

		}


		$first_cat_name = $this->get_post_category_first_name($qp_post_type, $wa_post->ID);

		$slider_gallery.= '<li style="width:'.(int)$wa_chpcs_query_posts_item_width .'px; height:' . $wa_chpcs_query_posts_item_height . 'px;" id="wa_chpcs_foo_content' . $id . '" class="wa_chpcs_foo_content">';

		if ($displayimage) {

			$featured_img = '';
			$image = '';

				if ('featured_image'==$wa_chpcs_query_posts_image_type) {

					$image = $this->wa_chpcs_get_post_image($post_content, $wa_post_id, 'featured_image', 'full', $id);		
					$image_thumb = $this->wa_chpcs_get_post_image($post_content, $wa_post_id, 'featured_image', $wa_chpcs_query_image_size, $id);

					$img_id = get_post_thumbnail_id($wa_post_id);
					$alt_text = get_post_meta($img_id , '_wp_attachment_image_alt', true);

					if ($wa_chpcs_query_lazy_loading) {
						$featured_img = "<img alt='" . $post_title . "' title='" . $post_title . "' class='wa_lazy'  id='wa_chpcs_img_" . $id . "' src=" . $lazy_img . "  data-original='" . $image_thumb . "' width='" . $wa_chpcs_query_posts_image_width . "' height='" . $wa_chpcs_query_posts_image_height . "'  />";	
					} else {
						$featured_img = "<img alt='" . $post_title . "' title='" . $post_title . "'  id='wa_chpcs_img_" . $id . "' src='" . $image_thumb . "' width='" . $wa_chpcs_query_posts_image_width . "' height='" . $wa_chpcs_query_posts_image_height . "'  />";	
					}
			
				} else if (isset($wa_chpcs_query_posts_image_type)&&'first_image'==$wa_chpcs_query_posts_image_type) {

					$image = $this->wa_chpcs_get_post_image($post_content, $wa_post_id, 'first_image', 'full', $id);
				
					if ($wa_chpcs_query_lazy_loading) {

						$featured_img = "<img alt='" . $post_title . "' title='" . $post_title . "' class='wa_lazy'  id='wa_chpcs_img_" . $id . "' src=" . $lazy_img . "  data-original='" . $image . "' width='" . $wa_chpcs_query_posts_image_width . "' height='" . $wa_chpcs_query_posts_image_height . "'  />";	
			
					} else {

						$featured_img = "<img  alt='" . $post_title . "' title='" . $post_title . "'  id='wa_chpcs_img_" . $id . "' src='" . $image . "' width='" . $wa_chpcs_query_posts_image_width . "' height='" . $wa_chpcs_query_posts_image_height . "'  />";	
			
					}

				} else if ('last_image'==$wa_chpcs_query_posts_image_type) {

					$image = $this->wa_chpcs_get_post_image($post_content, $wa_post_id, 'last_image', 'full', $id);
			
					if ($wa_chpcs_query_lazy_loading) {
						$featured_img = "<img alt='" . $post_title . "' title='" . $post_title . "' class='wa_lazy'  id='wa_chpcs_img_" . $id . "' src=" . $lazy_img . "  data-original='" . $image . "' width='" . $wa_chpcs_query_posts_image_width . "' height='" . $wa_chpcs_query_posts_image_height . "'  />";	
			
					} else {

						$featured_img = "<img alt='" . $post_title . "' title='" . $post_title . "' id='wa_chpcs_img_" . $id . "' src='" . $image . "' width='" . $wa_chpcs_query_posts_image_width . "' height='" . $wa_chpcs_query_posts_image_height . "'  />";	
		
					}

				} else if ('custom_field'==$wa_chpcs_query_posts_image_type) {

					$image = $this->wa_chpcs_get_post_image($post_content, $wa_post_id, 'custom_field', 'full', $id, $wa_chpcs_featured_image_custom_field_name);		
					$image_thumb = $this->wa_chpcs_get_post_image($post_content, $wa_post_id, 'custom_field', $wa_chpcs_query_image_size, $id, $wa_chpcs_featured_image_custom_field_name);
	
					$img_id = get_post_thumbnail_id($wa_post_id);
					$alt_text = get_post_meta($img_id , '_wp_attachment_image_alt', true);

					if(!empty($image_thumb)) {
	
	
					if ($wa_chpcs_query_lazy_loading) {
						$featured_img = "<img alt='" . $post_title . "' title='" . $post_title . "' class='wa_lazy'  id='wa_chpcs_img_" . $id . "' src=" . $lazy_img . "  data-original='" . $image_thumb . "' width='" . $wa_chpcs_query_posts_image_width . "' height='" . $wa_chpcs_query_posts_image_height . "'  />";	
					} else {
						$featured_img = "<img alt='" . $post_title . "' title='" . $post_title . "'  id='wa_chpcs_img_" . $id . "' src='" . $image_thumb . "' width='" . $wa_chpcs_query_posts_image_width . "' height='" . $wa_chpcs_query_posts_image_height . "'  />";	
					}

					}
				
				} 
			
			$slider_gallery.= '<div class="wa_featured_img ">';

			//display sale text over product image
			if ('1'==$wa_chpcs_show_sale_text&&'product'==$qp_post_type) {

				if ( $_product->is_type( 'variable' ) )	{
				  
					// a variable product
					$available_variations = $_product->get_available_variations();
					$variation_id         =isset($available_variations[0])? $available_variations[0]['variation_id'] : '';

					if (!empty($variation_id)) {
						
						$variable_product1 = new WC_Product_Variation( $variation_id );
						$sales_price       = $variable_product1 ->get_sale_price();

					}

					if (!empty($sales_price)) {
						$slider_gallery .= '<span class="wa_chpcs_onsale">' . __('Sale!', 'carousel-horizontal-posts-content-slider') . '</span>';
					}
				  
				} else {

					// a simple product
					$sales_price = $_product->get_sale_price();

					if (!empty( $sales_price ) ) {
						$slider_gallery .= '<span class="wa_chpcs_onsale">' . __('Sale!', 'carousel-horizontal-posts-content-slider') . '</span>';
					}
				}

			} 	

				//display image
				if ($wa_chpcs_query_posts_lightbox) {

					$slider_gallery.= '<a href="' . $image . '" class="wa_chpcs_post_link">' . $featured_img;

					//display hover image
					if ('hover_image'==$wa_chpcs_image_hover_effect) { 

						$slider_gallery.= '<div class="wa_chpcs_overlay"></div>';

					}

					$slider_gallery.= '<div style="clear:both;"></div></a>'; 

				} else {
				
					$slider_gallery.= '<a href="' . $post_link . '" class="wa_chpcs_post_link">' . $featured_img;

					//display hover image
					if ('hover_image'==$wa_chpcs_image_hover_effect) { 

						$slider_gallery.= '<div class="wa_chpcs_overlay"></div>';

					}

					$slider_gallery.= '<div style="clear:both;"></div></a>'; 

				}

			$slider_gallery.= '</div>';

		}

		//display category
		if (!empty($first_cat_name)&&'1'==$wa_chpcs_query_show_categories) {

			$slider_gallery.= '<div class="wa_chpcs_slider_show_cats" id="wa_chpcs_slider_show_cats' . $id . '">' . $first_cat_name . '</a></div>';
		
		}
		
		/**********   Post title, Post Description, read more  **********/

		//display post title
		if ('1'==$wa_chpcs_query_posts_title) {

			$slider_gallery.= '<h4  class="wa_chpcs_slider_title" id="wa_chpcs_slider_title' . $id . '"><a style="color:' . $wa_chpcs_query_font_colour . ';" style=" text-decoration:none;" href="' . $post_link . '">' . $post_title . '</a></h4>';
		
		}

		//display rating and review section
		if ('1'==$wa_chpcs_show_rating&&'product'==$qp_post_type) {

			$slider_gallery .= '<div class="wa_chpcs_rating" id="wa_wps_rating' . $id . '">';

			$count = $_product->get_rating_count();

			$average = $_product->get_average_rating();

			if ( $count > 0 ) : 

				$slider_gallery .= '<div class="chpcs_rating" id="chpcs_rating_' . $id . '" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">';
				/* translators: %ss is replaced with "average" */
				$chpcs_rate = sprintf( __( 'Rated %s out of 5', 'woocommerce' ), $average );

				$chpcs_avg = ( ( $average / 5 ) * 100 );
				/* translators: %ss is replaced with "count" */
				$wps_review = sprintf( _n( '%s customer review', '%s customer reviews', $count, 'woocommerce' ), '<span itemprop="ratingCount" class="count">' . $count . '</span>' );

					$slider_gallery .='<div class="sr" title="' . $chpcs_rate . '">';

							$slider_gallery .='<span style="width:' . $chpcs_avg . '%">';

								$slider_gallery .='<strong itemprop="ratingValue" class="rating">';

								$slider_gallery .= esc_html( $average ); 

								$slider_gallery .='</strong>';

								$slider_gallery .= 'out of 5'; 
							
							$slider_gallery .= '</span>';

					$slider_gallery .= '</div>';//end sr
		
				$slider_gallery .= '</div>';//end wps_rating

			endif; 

			$slider_gallery .= '</div>';//end .wa_wps_rating

		}

		//display date
		if ('1'==$wa_chpcs_query_show_date) {

			$slider_gallery.= '<div class="wa_chpcs_slider_show_date" id="wa_chpcs_slider_show_date' . $id . '">' . get_the_date( '', $wa_post_id ) . '</div>';
		
		}

		//display excerpt
		if ('1'==$wa_chpcs_query_posts_display_excerpt) {
			

			$slider_gallery.= '<div style="color:' . $wa_chpcs_query_font_colour . ';" class="wa_chpcs_foo_con" id="wa_chpcsjj_foo_con' . $id . '">' . $this->wa_chpcs_clean($text_type, $word_imit) . '</div>';
		
		}

		//display read more text
		if ('1'==$wa_chpcs_query_posts_display_read_more) {

			$slider_gallery.= '<span style="color:' . $wa_chpcs_query_font_colour . ';" class="wa_chpcs_more" id="wa_chpcs_more' . $id . '"><a style="color:' . $wa_chpcs_query_font_colour . ';" href="' . $post_link . '">' . $wa_chpcs_read_more . '</a></span>';
		
		}

		//display price
		if ('1'==$wa_chpcs_show_price&&'product'==$qp_post_type) {

			$slider_gallery .= '<div class="wa_chpcs_price" id="wa_chpcs_price' . $id . '">' . $_product->get_price_html() . '</div>';

		}
		

		//display add to cart
		if ('1'==$wa_chpcs_show_add_to_cart&&'product'==$qp_post_type) {

			$slider_gallery .= '<div class="wa_chpcs_add_to_cart" id="wa_chpcs_add_to_cart' . $id . '"><a  rel="nofollow" data-product_id="' . $wa_post_id . '" data-product_sku="' . $_product->get_sku() . '" class="wa_chpcs_button add_to_cart_button product_type_simple" href="' . do_shortcode('[add_to_cart_url id="' . $wa_post_id . '"]') . '">' . __('Add to cart', 'woocommerce') . '</a></div>';
			
		}


	$slider_gallery.= '</li>';
}

	$slider_gallery.='</ul>';
	
	$slider_gallery.='<div class="wa_chpcs_clearfix"></div>';

	//show direction arrows
if ('1'==$wa_chpcs_show_controls) {

	if ('up'==$wa_chpcs_pre_direction||'down'==$wa_chpcs_pre_direction) {

		$slider_gallery.='<a class="wa_chpcs_prev_v" id="foo' . $id . '_prev" href="#"><span style="">›</span></a>';
		$slider_gallery.='<a class="wa_chpcs_next_v" id="foo' . $id . '_next" href="#"><span>‹</span></a>';
	
	} else {

		$slider_gallery.='<a class="wa_chpcs_prev" id="foo' . $id . '_prev" href="#"><span>‹</span></a>';
		$slider_gallery.='<a class="wa_chpcs_next" id="foo' . $id . '_next" href="#"><span>›</span></a>';

	}
}

	//show pagination
if ('1'==$wa_chpcs_show_paging) {

	$slider_gallery.='<div class="wa_chpcs_pagination" id="wa_chpcs_pager_' . $id . '"></div>';
}

	$slider_gallery.='</div>';