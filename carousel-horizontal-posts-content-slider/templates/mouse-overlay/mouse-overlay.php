<?php
/* template name: mouse-overlay */

$slider_gallery.= '<div class="wa_chpcs_image_carousel" id="wa_chpcs_image_carousel' . $id . '">';

$slider_gallery.='<ul id="wa_chpcs_foo' . $id . '" style="height:' . $wa_chpcs_query_posts_item_height . 'px; overflow: hidden;" >';

foreach ($myposts as $wa_post) {

	$slider_gallery_text_overlay_caption = '';
	$slider_gallery_text_overlay_caption_overlay_content = '';


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

		//get product category name
		$first_cat_name = $this->get_post_category_first_name($qp_post_type, $wa_post->ID);

		$slider_gallery.= '<li style="width:' . $wa_chpcs_query_posts_item_width . 'px; height:' . $wa_chpcs_query_posts_item_height . 'px;" id="wa_chpcs_foo_content' . $id . '" class="wa_chpcs_foo_content">';


	$slider_gallery.= '<div class="wa_chpcs_text_overlay_p_container"><div class="wa_chpcs_text_overlay_caption">'; 


		if ($displayimage) {

			$featured_img = '';
			$image = '';

			if ('imageOrVideo'!=$qp_post_type) {

			if ('featured_image'==$wa_chpcs_query_posts_image_type) {

				$image = $this->wa_chpcs_get_post_image($post_content, $wa_post_id, 'featured_image', 'full' , $id);		
				$image_thumb = $this->wa_chpcs_get_post_image($post_content, $wa_post_id, 'featured_image', $wa_chpcs_query_image_size, $id);

				if ($wa_chpcs_query_lazy_loading) {
					$featured_img = "<img alt='" . $post_title . "' class='wa_lazy'  id='wa_chpcs_img_" . $id . "' data-original='" . $image_thumb . "' width='" . $wa_chpcs_query_posts_image_width . "' height='" . $wa_chpcs_query_posts_image_height . "'  />";	
				} else {
					$featured_img = "<img alt='" . $post_title . "'  id='wa_chpcs_img_" . $id . "' src='" . $image_thumb . "' width='" . $wa_chpcs_query_posts_image_width . "' height='" . $wa_chpcs_query_posts_image_height . "'  />";	
				}
				
			} else if (isset($wa_chpcs_query_posts_image_type)&&'first_image'==$wa_chpcs_query_posts_image_type) {

				$image = $this->wa_chpcs_get_post_image($post_content, $wa_post_id, 'first_image', 'full', $id);
					
				if ($wa_chpcs_query_lazy_loading) {
					$featured_img = "<img alt='" . $post_title . "' class='wa_lazy'  id='wa_chpcs_img_" . $id . "' data-original='" . $image . "' width='" . $wa_chpcs_query_posts_image_width . "' height='" . $wa_chpcs_query_posts_image_height . "'  />";	
				} else {
					$featured_img = "<img alt='" . $post_title . "'   id='wa_chpcs_img_" . $id . "' src='" . $image . "' width='" . $wa_chpcs_query_posts_image_width . "' height='" . $wa_chpcs_query_posts_image_height . "'  />";	
				}

			} else if ('last_image'==$wa_chpcs_query_posts_image_type) {

				$image = $this->wa_chpcs_get_post_image($post_content, $wa_post_id, 'last_image', 'full', $id);
				if ($wa_chpcs_query_lazy_loading) {
					$featured_img = "<img alt='" . $post_title . "' class='wa_lazy'  id='wa_chpcs_img_" . $id . "' data-original='" . $image . "' width='" . $wa_chpcs_query_posts_image_width . "' height='" . $wa_chpcs_query_posts_image_height . "'  />";	
				} else {
					$featured_img = "<img alt='" . $post_title . "'   id='wa_chpcs_img_" . $id . "' src='" . $image . "' width='" . $wa_chpcs_query_posts_image_width . "' height='" . $wa_chpcs_query_posts_image_height . "'  />";	
				}

			} else if ('custom_field'==$wa_chpcs_query_posts_image_type) {

				$image = $this->wa_chpcs_get_post_image($post_content, $wa_post_id, 'custom_field', 'full', $id, $wa_chpcs_featured_image_custom_field_name);		
				$image_thumb = $this->wa_chpcs_get_post_image($post_content, $wa_post_id, 'custom_field', $wa_chpcs_query_image_size, $id, $wa_chpcs_featured_image_custom_field_name);
		
				$img_id = get_post_thumbnail_id($wa_post_id);
				$alt_text = get_post_meta($img_id , '_wp_attachment_image_alt', true);
		
		
				if ($wa_chpcs_query_lazy_loading) {
					$featured_img = "<img alt='" . $post_title . "' class='wa_lazy'  id='wa_chpcs_img_" . $id . "' src=" . $lazy_img . "  data-original='" . $image_thumb . "' width='" . $wa_chpcs_query_posts_image_width . "' height='" . $wa_chpcs_query_posts_image_height . "'  />";	
				} else {
					$featured_img = "<img alt='" . $post_title . "'  id='wa_chpcs_img_" . $id . "' src='" . $image_thumb . "' width='" . $wa_chpcs_query_posts_image_width . "' height='" . $wa_chpcs_query_posts_image_height . "'  />";	
				}
					
			}


		} else {

			$image = 	( 'video'==$wa_post['type'] ) ? $wa_post['slide_url']  : $wa_post['image']['sizes']['full'];	
			$image_thumb = $wa_post['image']['sizes']['full'];

			$img_id = get_post_thumbnail_id($wa_post['attachment']);
			$alt_text = get_post_meta($img_id , '_wp_attachment_image_alt', true);


			if(!empty($image_thumb)) {

			if ($wa_chpcs_query_lazy_loading) {

				$featured_img = "<img alt='" . $post_title . "' title='" . $post_title . "' class='wa_lazy'  id='wa_chpcs_img_" . $id . "' src=" . $lazy_img . "  data-original='" . $image_thumb . "' width='" . $wa_chpcs_query_posts_image_width . "' height='" . $wa_chpcs_query_posts_image_height . "'  />";	
				
			} else {

				$featured_img = "<img alt='" . $post_title . "' title='" . $post_title . "'  id='wa_chpcs_img_" . $id . "' src='" . $image_thumb . "' width='" . $wa_chpcs_query_posts_image_width . "' height='" . $wa_chpcs_query_posts_image_height . "'  />";	
			
			}

			}

		}


			//display image
			if ($wa_chpcs_query_posts_lightbox) {
				$slider_gallery_img= '<a href="' . $image . '">' . $featured_img . '</a>';} else {
				$slider_gallery_img= '<a href="' . $post_link . '">' . $featured_img . '</a>'; }

		}



		/**********   Post title, Post Description, read more  **********/

		//display post title
		if ('1'==$wa_chpcs_query_posts_title) {
			
			$slider_gallery_text_overlay_caption.= '<div class="wa_chpcs_text_overlay_caption_overlay_title">';
			$slider_gallery_text_overlay_caption.= '<br/><div style="color:' . $wa_chpcs_query_font_colour . ';" class="wa_chpcs_slider_title" id="wa_chpcs_slider_title' . $id . '"><a style="color:' . $wa_chpcs_query_font_colour . ';" style=" text-decoration:none;" href="' . $post_link . '">' . $post_title . '</a></div>';
			$slider_gallery_text_overlay_caption.= '</div>';
			
		}

		//display rating
					//display rating and review section
					if ('1'==$wa_chpcs_show_rating&&'product'==$qp_post_type) {

						$slider_gallery_text_overlay_caption .= '<div class="wa_chpcs_rating" id="wa_wps_rating' . $id . '">';
			
						$count = $_product->get_rating_count();
			
						$average = $_product->get_average_rating();
			
						if ( $count > 0 ) : 
			
							$slider_gallery_text_overlay_caption .= '<div class="chpcs_rating" id="chpcs_rating_' . $id . '" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">';
							/* translators: %ss is replaced with "average" */
							$chpcs_rate = sprintf( __( 'Rated %s out of 5', 'woocommerce' ), $average );
			
							$chpcs_avg = ( ( $average / 5 ) * 100 );
							/* translators: %ss is replaced with "count" */
							$wps_review = sprintf( _n( '%s customer review', '%s customer reviews', $count, 'woocommerce' ), '<span itemprop="ratingCount" class="count">' . $count . '</span>' );
			
								$slider_gallery_text_overlay_caption .='<div class="sr" title="' . $chpcs_rate . '">';
			
										$slider_gallery_text_overlay_caption .='<span style="width:' . $chpcs_avg . '%">';
			
											$slider_gallery_text_overlay_caption .='<strong itemprop="ratingValue" class="rating">';
			
											$slider_gallery_text_overlay_caption .= esc_html( $average ); 
			
											$slider_gallery_text_overlay_caption .='</strong>';
			
											$slider_gallery_text_overlay_caption .= 'out of 5'; 
										
										$slider_gallery_text_overlay_caption .= '</span>';
			
								$slider_gallery_text_overlay_caption .= '</div>';//end sr
					
							$slider_gallery_text_overlay_caption .= '</div>';//end wps_rating
			
						endif; 
			
						$slider_gallery_text_overlay_caption .= '</div>';//end .wa_wps_rating
			
					}


		//display date
		if ('1'==$wa_chpcs_query_show_date) {

			$slider_gallery_text_overlay_caption.= '<div class="wa_chpcs_slider_show_date" id="wa_chpcs_slider_show_date' . $id . '">' . get_the_date( '', $wa_post_id ) . '</a></div>';
			
		}


		//display category
		if (!empty($first_cat_name)&&'1'==$wa_chpcs_query_show_categories) {

			$slider_gallery_text_overlay_caption.= '<div class="wa_chpcs_slider_show_cats" id="wa_chpcs_slider_show_cats' . $id . '">' . $first_cat_name . '</a></div>';
				
		}



		if ('1'==$wa_chpcs_query_posts_display_excerpt) {

			$slider_gallery_text_overlay_caption_overlay_content.= '<div style="color:' . $wa_chpcs_query_font_colour . ';" class="wa_chpcs_foo_con" id="wa_chpcsjj_foo_con' . $id . '">' . $this->wa_chpcs_clean($text_type, $word_imit) . '</div>';
			
		}

		//display read more text
		if ('1'==$wa_chpcs_query_posts_display_read_more) {

			$slider_gallery_text_overlay_caption_overlay_content.= '<span style="color:' . $wa_chpcs_query_font_colour . ';" class="wa_chpcs_more" id="wa_chpcs_more' . $id . '"><a style="color:' . $wa_chpcs_query_font_colour . ';" href="' . $post_link . '">' . $wa_chpcs_read_more . '</a></span>';
			
		}


		//display price
		if ('1'==$wa_chpcs_show_price&&'product'==$qp_post_type) {

			$slider_gallery_text_overlay_caption_overlay_content .= '<div class="wa_chpcs_price" id="wa_chpcs_price' . $id . '">' . $_product->get_price_html() . '</div>';

		}
	

		//display add to cart
		if ('1'==$wa_chpcs_show_add_to_cart&&'product'==$qp_post_type) {

			$slider_gallery_text_overlay_caption_overlay_content .= '<div class="wa_chpcs_add_to_cart" id="wa_chpcs_add_to_cart' . $id . '"><a  rel="nofollow" data-product_id="' . $wa_post_id . '" data-product_sku="' . $_product->get_sku() . '" class="wa_chpcs_button add_to_cart_button product_type_simple" href="' . do_shortcode('[add_to_cart_url id="' . $wa_post_id . '"]') . '">' . __('Add to cart', 'woocommerce') . '</a></div>';
			
		}





	$slider_gallery.= $slider_gallery_img.'<div class="wa_chpcs_text_overlay_caption_overlay">'.$slider_gallery_text_overlay_caption;

	$slider_gallery.= '<div class="wa_chpcs_text_overlay_caption_overlay_content">'.$slider_gallery_text_overlay_caption_overlay_content.'</div>';

	$slider_gallery.= '</div>';
		
	$slider_gallery.= '</div></div>';


	$slider_gallery.= '</li>';

}

	$slider_gallery.='</ul>';
	$slider_gallery.='<div class="wa_chpcs_clearfix"></div>';

if ('1'==$wa_chpcs_show_controls) {

	if ('up'==$wa_chpcs_pre_direction||'down'==$wa_chpcs_pre_direction) {

		$slider_gallery.='<a class="wa_chpcs_prev_v" id="foo' . $id . '_prev" href="#"><span style="">›</span></a>';
		$slider_gallery.='<a class="wa_chpcs_next_v" id="foo' . $id . '_next" href="#"><span>‹</span></a>';
	
	} else {

			$slider_gallery.='<a class="wa_chpcs_prev" id="foo' . $id . '_prev" href="#"><span>‹</span></a>';
			$slider_gallery.='<a class="wa_chpcs_next" id="foo' . $id . '_next" href="#"><span>›</span></a>';
		
	}
}
if ('1'==$wa_chpcs_show_paging) {

	$slider_gallery.='<div class="wa_chpcs_pagination" id="wa_chpcs_pager_' . $id . '"></div>';
	
}
	$slider_gallery.='</div>';
