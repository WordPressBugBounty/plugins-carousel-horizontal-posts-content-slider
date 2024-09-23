<?php 
/* 
*	Plugin Name: Carousel horizontal posts content slider
*	Description: A simple posts content slider, product, images, videos, related posts, custom post type carousel plugin for WordPress.
*	Version: 3.3.1
*	Author: subhansanjaya
*	Author URI: http://www.weaveapps.com
*/
if (! defined( 'ABSPATH' )) {
	exit; // Exit if accessed directly
}

class Carousel_Horizontal_Posts_Content_Slider {

	//default settings
	private $defaults = array(
		'settings' => array(
			'jquery' => false,
			'transit' => true,
			'lazyload' => true,
			'caroufredsel' => true,
			'touchswipe' => true,
			'loading_place' => 'footer',
			'deactivation_delete' => false
		),
		'version' => '3.3'
	);

	private $options = array();
	private $tabs = array();

	public function __construct() {

		//activation and deactivation hooks
		register_activation_hook(__FILE__, array(&$this, 'wa_chpcs_multisite_activation') );
		register_deactivation_hook(__FILE__, array(&$this, 'wa_chpcs_multisite_deactivation'));

		//define plugin path
		define( 'WA_CHPCS_SLIDER_PLUGIN_PATH', plugin_dir_path(__FILE__) );

		// redirect to settings page after plugin activation
		add_action( 'activated_plugin', array(&$this, 'wa_chpcs_activation_redirect') );

		//define theme directory
		define( 'WA_CHPCS_SLIDER_PLUGIN_TEMPLATE_DIRECTORY_NAME', 'templates' );
		define( 'WA_CHPCS_PLUGIN_TEMPLATE_DIRECTORY', WA_CHPCS_SLIDER_PLUGIN_PATH . WA_CHPCS_SLIDER_PLUGIN_TEMPLATE_DIRECTORY_NAME . DIRECTORY_SEPARATOR );

		//define view directory
		define( 'WA_CHPCS_SLIDER_PLUGIN_TEMPLATE_DIRECTORY_NAME_VIEW', 'views' );
		define( 'WA_CHPCS_PLUGIN_VIEW_DIRECTORY', WA_CHPCS_SLIDER_PLUGIN_PATH . WA_CHPCS_SLIDER_PLUGIN_TEMPLATE_DIRECTORY_NAME_VIEW . DIRECTORY_SEPARATOR );
	
		add_action('admin_init', array(&$this, 'register_settings'));

		//register post type
		add_action('init', array(&$this, 'wa_chpcs_init'));

		// metaboxes 
		add_action( 'add_meta_boxes', array( $this, 'wa_chpcs_add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'wa_chpcs_save_metabox_data' ) );

		//update messages and help text
		add_action('post_updated_messages', array(&$this, 'wa_chpcs_updated_messages'));
	
		//load defaults
		add_action('plugins_loaded', array(&$this, 'load_defaults'));

		//register shortcode button to the TinyMCE toolbar
		add_action('init', array(&$this, 'wa_chpcs_shortcode_button_init'));

		//update plugin version
		update_option('wa_chpcs_version', $this->defaults['version'], '', 'no');

		//set settings
		$array                     = get_option('wa_chpcs_settings');
		$array                     = false === $array ? array() : $array;
		$this->options['settings'] = array_merge($this->defaults['settings'], $array );
		
		add_action('wp_enqueue_scripts', array(&$this, 'wa_chpcs_load_scripts'));
		add_shortcode( 'carousel-horizontal-posts-content-slider', array(&$this, 'wa_chpcs_shortcode') );

		if (is_admin()) {
			add_action( 'admin_menu', array(&$this, 'wa_chpcs_pre_add_to_menu' ) );
		}

		add_action('admin_enqueue_scripts', array(&$this, 'admin_include_scripts'));

		//add text domain for localization
		add_action('plugins_loaded', array(&$this, 'wa_chpcs_load_textdomain'));

		// create widget
		include_once('includes/class-wa-chpcs-widget.php');
		$wachpcs_widget = new WA_CHPCS_Widget();

		//add settings link
		add_filter('plugin_action_links', array(&$this, 'wa_chpcs_settings_link'), 2, 2);

		//add ajax on admin to display related select post types
		add_action( 'admin_footer', array(&$this, 'wa_chpcs_related_select'));

		add_action('wp_ajax_nopriv_wa_chpcs_action', array(&$this, 'wa_chpcs_action_callback'));
		add_action('wp_ajax_wa_chpcs_action', array(&$this, 'wa_chpcs_action_callback'));

		//remove publish box
		add_action( 'admin_menu', array(&$this, 'wa_chpcs_remove_publish_box'));

		add_action('admin_print_scripts', array(&$this, 'wa_chpcs_disable_autosave'));

		//register plugin block
		add_filter('wa_chpcs_get_plugin_blocks', array(&$this,'wa_chpcs_register_plugin_block'));

		//enqueue block editor assets
		add_action( 'enqueue_block_editor_assets', array(&$this,'wa_chpcs_enqueue_block_editor_assets' ));

		//register editor assets
		add_filter('wa_chpcs_get_block_editor_assets', array(&$this,'wa_chpcs_register_block_editor_assets'));

		//add custom column
		add_filter( 'manage_edit-wa_chpcs_columns', array(&$this,'wa_chpcs_edit_wa_chpcs_columns' )) ;

		//add content to coumns
		add_action( 'manage_wa_chpcs_posts_custom_column', array(&$this,'wa_chpcs_manage_wa_chpcs_columns'), 10, 2 );

		// display admin notices
		add_action( 'admin_notices', array(&$this,'wa_chpcs_display_admin_notice' ));
		add_action( 'wp_ajax_wa-chpcs-never-show-review-notice', array(&$this, 'wa_chpcs_dismiss_review_notice' ));

	}

	// display admin notice
	public function wa_chpcs_display_admin_notice() {

		if ( ! current_user_can( 'manage_options' ) ) {
				return;
		}
	
		$review = get_option( 'wa_chpcs_review_notice_dismiss' );
		$time   = time();
		$load   = false;
	
		if ( ! $review ) {
			$review = array(
				'time'      => $time,
				'dismissed' => false,
			);
	
			add_option( 'wa_chpcs_review_notice_dismiss', $review );
	
		} else {
	
			// Check if it has been dismissed or not
			if ( ( isset( $review['dismissed'] ) && ! $review['dismissed'] ) && ( isset( $review['time'] ) && ( ( $review['time'] + ( DAY_IN_SECONDS * 3 ) ) <= $time ) ) ) {
				$load = true;
			}
		}
	
		// if we cannot load return early
		if ( ! $load ) {	
			return;
		}
		?>
			
			<div id="wa-chpcs-review-notice" class="wa-chpcs-review-notice">
				<div class="wa-chpcs-plugin-icon">
					<img src="<?php echo esc_html(plugins_url() . '/carousel-horizontal-posts-content-slider/assets/images/icon-256x256.png'); ?>" alt="Carousel Horizontal Posts Content Slider">
				</div>
				<div class="wa-chpcs-notice-text">
					<h3>Enjoying <strong>Carousel Horizontal Posts Content Slider</strong>?</h3>
					<p>We hope you've enjoyed using <strong>Carousel Horizontal Posts Content Slider</strong> by Weave Apps. Would you please show us some love by rating us in our <a href="https://weaveapps.com/shop/wordpress-plugins/carousel-horizontal-posts-slider-wordpress-plugin/#reviews" target="_blank"><strong>Web Site</strong></a>?
					Just a minute to rate it. Thank you!</p>
	
					<p class="wa-chpcs-review-actions">
						<a href="https://wordpress.org/plugins/carousel-horizontal-posts-content-slider/#reviews" target="_blank" class="notice-dismissed rate-wp-carousel"><span class="dashicons dashicons-external"></span>Sure, I'd love to!</a>

						<a href="#" class="notice-dismissed never-show-again"><span class="dashicons dashicons-star-filled"></span>I've already left a review. :)
						</a>

						<a href="#" class="notice-dismissed remind-me-later"><span class="dashicons dashicons-clock"></span>Nope, maybe later
						</a>
	
				<?php wp_nonce_field('wa_chpcs_action', 'wa__field'); ?>
						<a href="#" class="notice-dismissed never-show-again"><span class="dashicons dashicons-dismiss"></span>Never show again</a>
					</p>
				</div>
			</div>
	
			<script type='text/javascript'>
	
	jQuery(document).ready(function() {
		jQuery(document).on('click', '.notice-dismissed', function( event ) {

						if ( jQuery(this).hasClass('rate-chpcs') ) {
							var notice_dismissed_value = "1";
						}
						if ( jQuery(this).hasClass('remind-me-later') ) {
							var notice_dismissed_value =  "2";
							event.preventDefault();
						}
						if ( jQuery(this).hasClass('never-show-again') ) {

							var notice_dismissed_value =  "3";
							event.preventDefault();
						}
	
						var nonce =  jQuery("#wa_chpcs_field").attr('value');
	
						jQuery.post( ajaxurl, {
							action: 'wa-chpcs-never-show-review-notice',
							'nonce' : nonce,
							notice_dismissed_data : notice_dismissed_value
						});
	
						jQuery('#wa-chpcs-review-notice').hide();
					});
				});
	
			</script>
			<?php
	}
	
	// dismiss review notice
	public function wa_chpcs_dismiss_review_notice() {
		if ( ! $review ) {
			$review = array();
		}
	
		if (isset($_POST['notice_dismissed_data'])&&isset($_REQUEST['wa_chpcs_field'])&&wp_verify_nonce(sanitize_text_field($_REQUEST['wa_chpcs_field']), 'wa-chpcs-never-show-review-notice')) {
	
			$notice_dismissed_data = sanitize_text_field($_POST['notice_dismissed_data']);
		}
	
		switch ( $notice_dismissed_data) {
			case '1':
				$review['time']      = time();
				$review['dismissed'] = false;
				break;
			case '2':
				$review['time']      = time();
				$review['dismissed'] = false;
				break;
			case '3':
				$review['time']      = time();
				$review['dismissed'] = true;
				break;
		}
		update_option( 'wa_chpcs_review_notice_dismiss', $review );
		die;
	}

	//add custom column
	public function wa_chpcs_edit_wa_chpcs_columns( $columns ) {

		$columns = array(
			'cb' => '&lt;input type="checkbox" />',
			'title' => esc_html( 'Title' ),
			'shortcode' => esc_html( 'Shortcode' ),
			'phpcode' => esc_html( 'PHP code' ),
			'date' => esc_html( 'Date' )
		);
	
		return $columns;
	}

	//adding contents to custom columns
	public function wa_chpcs_manage_wa_chpcs_columns( $column, $post_id ) {
		global $post;
	
		switch ( $column ) {
	
			case 'shortcode':
				$shortcode = "[carousel-horizontal-posts-content-slider id='" . $post_id . "']";
	
				if ( empty( $shortcode ) ) {
					echo esc_html( 'Unknown' );
	
				} else {
					echo esc_html($shortcode);
				}
	
				break;

			case 'phpcode':

				$shortcode = "<?php echo do_shortcode('[carousel-horizontal-posts-content-slider id=\"{$post_id}\"]') ?>";
	
				if ( empty( $shortcode ) ) {
					echo esc_html( 'Unknown' );
	
				} else {
					echo esc_html($shortcode);
				}
	
				break;
	
			default:
				break;
		}
	}

	// gutenberg
	public function wa_chpcs_enqueue_block_editor_assets() {

		$blocks = apply_filters('wa_chpcs_get_plugin_blocks', array());
		// Get the last version from all plugins.
		$assets = apply_filters('wa_chpcs_get_block_editor_assets', array());
		// Not performing unregister or unenqueue as in old versions all are with prefixes.
		wp_enqueue_script('chpcs-block', $assets['js_path'], array( 'wp-blocks', 'wp-element','wp-editor' ), $assets['version']);
		wp_localize_script('chpcs-block', 'chpcs_obj_translate', array(
		  'nothing_selected' => esc_html('Nothing selected.', 'chpcs'),
		  'empty_item' => esc_html('- Select -', 'chpcs'),
		  'blocks' => json_encode($blocks)
		));
		wp_enqueue_style('chpcs-block', $assets['css_path'], array( 'wp-edit-blocks' ), $assets['version']);

	}

	public function wa_chpcs_register_plugin_block( $blocks) {

		$iconUrl = plugins_url() . '/carousel-horizontal-posts-content-slider/assets/images/icon.png';
		$iconSvg = plugins_url() . '/carousel-horizontal-posts-content-slider/assets/images/icon.png';

		$plugin_name = 'Carousel-Horizontal-Posts-Content-Slider';
		$data = $this->get_shortcode_data();
		$blocks['chpcs/slider-block'] = array(
		  'title' => 'Carousel-Horizontal-Posts-Content-Slider',
			/* translators: %s is replaced with "plugin name" */
		  'titleSelect' => sprintf(esc_html('Select %s', 'chpcs'), $plugin_name),
		  'iconUrl' => $iconUrl,
		  'iconSvg' => array('width' => 20, 'height' => 20, 'src' => $iconSvg),
		  'isPopup' => false,
		  'data' => $data,
		);
		return $blocks;
	}

	// register block editor assets
	public function wa_chpcs_register_block_editor_assets( $assets) {

		$version = '3.3';
		$js_path = plugins_url( 'assets/js/block.js', __FILE__ );
		$css_path = plugins_url( 'assets/css/block.css', __FILE__ );
		if (!isset($assets['version']) || version_compare($assets['version'], $version) === -1) {
			$assets['version'] = $version;
			$assets['js_path'] = $js_path;
			$assets['css_path'] = $css_path;
		}
		return $assets;

	}

	// get shortcode data
	public function get_shortcode_data() {

		$default_args = array( 
		  'post_type' => 'wa_chpcs',
		  'numberposts' => -1,
		  'posts_per_page' => -1,
		  'post_status' => 'publish',
		  'fields' => array('ID', 'post_title'),
		);

		$datatoBePassed = get_posts($default_args);

		$data = array();
		$data['shortcode_prefix'] = 'carousel-horizontal-posts-content-slider';
		$data['inputs'][] = array(
		'type' => 'select',
		'id' => 'wdps_id',
		'name' => 'wdps_id',
		'shortcode_attibute_name' => 'id',
		'options'  => $datatoBePassed,
		);
		return json_encode($data);

	}

	// redirect to plugin's page after activation
	public function wa_chpcs_activation_redirect( $plugin ) {
		if (  plugin_basename( __FILE__ ) == $plugin ) {
			exit(esc_html( wp_redirect( esc_html(admin_url( 'edit.php?post_type=wa_chpcs' ) )) ));
		}
	}

	// multisite activation
	public function wa_chpcs_multisite_activation( $networkwide) {
		if (is_multisite() && $networkwide) {
			global $wpdb;

			$activated_blogs = array();
			$current_blog_id = $wpdb->blogid;
			$blogs_ids = $wpdb->get_col($wpdb->prepare('SELECT blog_id FROM ' . $wpdb->blogs, ''));

			foreach ($blogs_ids as $blog_id) {
				switch_to_blog($blog_id);
				$this->activate_single();
				$activated_blogs[] = (int) $blog_id;
			}

			switch_to_blog($current_blog_id);
			update_site_option('wa_chpcs_activated_blogs', $activated_blogs, array());
		} else {
			$this->activate_single();
		}
	}

	public function activate_single() {

		$this->wa_chpcs_add_sample_data();

		
		add_option('wa_chpcs_settings', $this->defaults['settings'], '', 'no');
		add_option('wa_chpcs_version', $this->defaults['version'], '', 'no');
	}


	// add existing data
	public function wa_chpcs_add_sample_data() {

		$existing_data = array();
		$existing_data = get_option('wa_chpcs_settings') ? get_option('wa_chpcs_settings') : '';
	
		if (empty($existing_data)) {
			return;
		}
	
		// create slider if not already exisit
		$default_img = plugins_url() . '/carousel-horizontal-posts-content-slider/assets/images/default-image.jpg'; // default image
		$loading_img = plugins_url() . '/carousel-horizontal-posts-content-slider/assets/images/loader.gif'; // loading image
		$hover_img = plugins_url() . '/carousel-horizontal-posts-content-slider/assets/images/hover.png'; // loading image
	
		$slider_options = array(
			'post_type' => 'post',
			'content_type' => 'newest',
			'post_taxonomy' => '',
			'post_terms' => '',
			'post_ids' => '',
			'posts_order_by' => 'id',
			'post_order' => 'asc',
			'template' => 'basic',
			'image_hover_effect' => 'none',
			'read_more_text' => 'Read more',
			'featured_image_custom_field_name' => '',
			'excerpt_custom_field_name' => '',
			'word_limit' => '10',
			'show_posts' => '20',
			'show_posts_per_page' => '4',
			'items_to_be_slide' => '0',
			'duration' => '500',
			'item_width' => '200',
			'item_height' => '350',
			'post_image_width' => '200',
			'post_image_height' => '',
			'image_type' => 'featured_image',
			'easing_effect' => 'linear',
			'fx' => 'scroll',
			'align_items' => 'center',
			'font_colour' => '#000',
			'control_colour' => '#fff',
			'control_bg_colour' => '#000',
			'arrows_hover_colour' => '#ccc',
			'size_arrows' => '18',
			'title_font_size' => '14',
			'font_size' => '12',
			'default_image' => $default_img,
			'lazy_load_image' => $loading_img,
			'show_title' => true,
			'show_image' => true,
			'show_excerpt' => true,
			'title_top_of_image' => true,
			'show_read_more_text' => true,
			'excerpt_type' => false,
			'responsive' => false,
			'lightbox' => false,
			'lazy_loading' => false,
			'auto_scroll' => true,
			'draggable' => true,
			'circular' => false,
			'infinite' => true,
			'touch_swipe' => true,
			'direction' => 'right',
			'show_controls' => true,
			'animate_controls' => true,
			'show_paging' => true,
			'css_transitions' => true,
			'pause_on_hover' => true,
			'timeout' => '3000',
			'start_date' => '',
			'end_date' => '',
			'hover_image_bg' => 'rgba(40,168,211,.85)',
			'hover_image_url' => $hover_img,
			'text_align' => 'left',
			'image_size' => 'other',
			'image_align' => 'left',
			'featured_image_custom_field_name' => '',
			'excerpt_custom_field_name' => '',
		);
	
	
			$post_title = sanitize_title( 'Existing Slider' );
			 
			$new_post = array(
				'post_title' => $post_title,
				'post_content' => '',
				'post_status' => 'publish',
			//	'post_date' => date('Y-m-d H:i:s'),
				'post_author' => '',
				'post_type' => 'wa_chpcs',
				'post_category' => array(0)
			);
	
			require_once ABSPATH . '/wp-admin/includes/post.php';
			if (  function_exists( 'post_exists' ) ) {
	

				if(!post_exists( $post_title )) {

				$post_id =  wp_insert_post( $new_post );

				if ($post_id) {
					
					
					update_post_meta( $post_id, 'options', $slider_options );
				}

			}
	
			}
			 
	}
	
	//deactivation hook
	public function wa_chpcs_multisite_deactivation( $networkwide) {
		if (is_multisite() && $networkwide) {
			global $wpdb;

			$activated_blogs = array();
			$current_blog_id = $wpdb->blogid;
			$blogs_ids       = $wpdb->get_col($wpdb->prepare('SELECT blog_id FROM ' . $wpdb->blogs, ''));

			foreach ($blogs_ids as $blog_id) {
				switch_to_blog($blog_id);
				$this->deactivate_single(true);

				if (in_array((int) $blog_id, $activated_blogs, true)) {
					unset($activated_blogs[array_search($blog_id, $activated_blogs)]);
				}
			}

			switch_to_blog($current_blog_id);
			update_site_option('wa_chpcs_activated_blogs', $activated_blogs);
		} else {
			$this->deactivate_single();
		}
	}

	// deactivate single
	public function deactivate_single( $multi = false) {
		if ( true===$multi ) {
			$options = get_option('wa_chpcs_settings');
			$check = $options['deactivation_delete'];
		} else {
			$check = $this->options['settings']['deactivation_delete'];
		}

		if ( true===$check ) {

			$mycustomposts = get_posts( array( 'post_type' => 'wa_chpcs'));

			foreach ( $mycustomposts as $mypost ) {
				// delete each post
				wp_delete_post( $mypost->ID, true);
			}

			delete_option('wa_chpcs_settings');
			delete_option('wa_chpcs_version');
		}
	}

	//	settings link in plugin management screen
	public function wa_chpcs_settings_link( $actions, $file) {

		if (false !== strpos($file, 'carousel-horizontal-posts-content-slider')) {
		$actions['settings'] = '<a href="edit.php?post_type=wa_chpcs&page=wa_chpcs">Settings | <a href="https://weaveapps.com/shop/wordpress-plugins/carousel-horizontal-posts-slider-wordpress-plugin/" style="color:#04C018;font-weight:bold;" target="_blank">' . "Go Pro" . '</a>';
		}
		return $actions; 

	}

	public function wa_chpcs_related_select() {	
		?>
	<script type="text/javascript" >
	jQuery(document).ready(function($) {

		var posts_taxonomy = $("#wa_chpcs_query_posts_taxonomy option:selected").attr('value');
		var posts_terms = $("#wa_chpcs_query_posts_terms option:selected").attr('value'); 
		var posts_tags = $("#wa_chpcs_query_posts_tags option:selected").attr('value'); 
		var post_type = $("#wa_chpcs_query_posts_post_type option:selected").attr('value'); 
		var content_type = $("#wa_chpcs_query_content_type option:selected").attr('value'); 

		if('product'!=post_type) { 
			$("#wa_chpcs_show_add_to_cart").closest('p').hide(); 
			$("#wa_chpcs_show_sale_text").closest('p').hide();  
			$("#wa_chpcs_show_price").closest('p').hide(); 
			$("#wa_chpcs_show_rating").closest('p').hide(); 
		} 

		if('post'!=post_type) { 


			$("#wa_chpcs_query_content_type").closest('tr').hide(); //hide product type

		} else {

			jQuery("#wa_chpcs_query_posts_taxonomy").closest('tr').hide(); 

		}


		if('post'==post_type) {
			$("#wa_chpcs_query_content_type").closest('tr').show();
		}

		if(content_type&&'category'!=content_type) { 

			$("#wa_chpcs_query_posts_taxonomy").closest('tr').hide(); 
			$("#wa_chpcs_query_posts_terms").closest('tr').hide(); 
			$("#wa_chpcs_query_posts_terms").removeAttr('required'); 

		}

		if('post'==post_type&&'category'==content_type) {

			$("select#wa_chpcs_query_posts_terms").removeAttr("disabled");
			$("#wa_chpcs_query_posts_terms").closest('tr').show(); 
			$("#wa_chpcs_query_posts_terms").attr("required","required");

		}

		if('product'==post_type&&posts_taxonomy) {

$("select#wa_chpcs_query_posts_terms").removeAttr("disabled");
$("#wa_chpcs_query_posts_terms").closest('tr').show(); 
$("#wa_chpcs_query_posts_terms").attr("required","required");

}

		if(content_type&&'tag'!=content_type) { 
 
			$("#wa_chpcs_query_posts_tags").closest('tr').hide(); 
			$("#wa_chpcs_query_posts_tags").removeAttr('required'); 

		}

		if('post'==post_type&&'tag'==content_type) {

			$("select#wa_chpcs_query_posts_tags").removeAttr("disabled");
			$("#wa_chpcs_query_posts_tags").closest('tr').show(); 
			$("#wa_chpcs_query_posts_tags").attr("required","required");

		}

		//disabled taxonomy field
		if(!posts_taxonomy) {

			$("select#wa_chpcs_query_posts_taxonomy").attr("disabled","disabled");
			$("#wa_chpcs_query_posts_taxonomy").closest('tr').hide(); 
		}

		//disabled terms field
		if(!posts_terms) {
			
			$("select#wa_chpcs_query_posts_terms").attr("disabled","disabled");
			$("#wa_chpcs_query_posts_terms").closest('tr').hide(); 

		}

		//disabled tags field
		if(!posts_tags) {

			$("select#wa_chpcs_query_posts_tags").attr("disabled","disabled");
			$("#wa_chpcs_query_posts_tags").closest('tr').hide(); 

		}

		//select terms based on product type
		$("select#wa_chpcs_query_content_type").change(function() {

			var post_type = jQuery("select#wa_chpcs_query_posts_post_type option:selected").attr('value');
			var content_type = $("#wa_chpcs_query_content_type option:selected").attr('value');
			var tax = "category";
			var nonce =  jQuery("#wa_wps_field").attr('value');

			var data = {
				'action': 'wa_chpcs_action',
				'post_type': post_type,
				'tax': tax,
				'content_type': content_type,
				'nonce' : nonce
			};

			$.post(ajaxurl, data, function(response) {
				 $("select#wa_chpcs_query_posts_terms").removeAttr("disabled");
				 $("select#wa_chpcs_query_posts_terms").html(response);
			});

			$.post(ajaxurl, data, function(response) {
				 $("select#wa_chpcs_query_posts_tags").removeAttr("disabled");
				 $("select#wa_chpcs_query_posts_tags").html(response);
			});
		
		});


		$("select#wa_chpcs_query_content_type").change(function()	{

		var content_type = $("#wa_chpcs_query_content_type option:selected").attr('value');
		var post_type = $("#wa_chpcs_query_posts_post_type option:selected").attr('value');
		

		if('post'==post_type&&'category'!=content_type) {

			$("#wa_chpcs_query_posts_terms").removeAttr('required'); 
			$("#wa_chpcs_query_posts_taxonomy").closest('tr').hide(); 
			$("#wa_chpcs_query_posts_terms").closest('tr').hide(); 
			$("#wa_chpcs_query_posts_tags").removeAttr('required'); 
			$("#wa_chpcs_query_posts_tags").closest('tr').hide(); 
			
		} else {

			$("#wa_chpcs_query_posts_taxonomy").closest('tr').hide(); 
			$("#wa_chpcs_query_posts_terms").closest('tr').show(); 
			$("#wa_chpcs_query_posts_terms").attr("required","required");
			$("#wa_chpcs_query_posts_tags").removeAttr('required'); 
			$("#wa_chpcs_query_posts_tags").closest('tr').hide(); 

		}

		if('post'==post_type&&'tag'==content_type) {
			$("#wa_chpcs_query_posts_tags").closest('tr').show(); 
			$("#wa_chpcs_query_posts_tags").attr("required","required");
		} else {

			$("#wa_chpcs_query_posts_tags").closest('tr').hide(); 
			$("#wa_chpcs_query_posts_tags").removeAttr('required'); 
		}


	});

		//select taxonomies based on post type
		$("select#wa_chpcs_query_posts_post_type").change(function() {

		$("#wa_chpcs_query_posts_terms").attr("required","required");

		$("select#wa_chpcs_query_posts_terms").attr("disabled","disabled");

		$("select#wa_chpcs_query_posts_taxonomy").attr("disabled","disabled");

		$("#wa_chpcs_query_posts_terms").closest('tr').hide(); 

		$("#wa_chpcs_query_posts_tags").closest('tr').hide(); 

		$("#wa_chpcs_query_posts_taxonomy").closest('tr').hide();

		$(".spinner").addClass("is-active"); // add the spinner is-active class before the Ajax posting 

		var post_type = jQuery("select#wa_chpcs_query_posts_post_type option:selected").attr('value');
		var nonce =  jQuery("#wa_wps_field").attr('value');

		if('product'==post_type){


			$("#wa_chpcs_show_add_to_cart").closest('p').show(); 
			$("#wa_chpcs_show_sale_text").closest('p').show();  
			$("#wa_chpcs_show_price").closest('p').show(); 
			$("#wa_chpcs_show_rating").closest('p').show(); 

		} else {

			$("#wa_chpcs_show_add_to_cart").closest('p').hide(); 
			$("#wa_chpcs_show_sale_text").closest('p').hide();  
			$("#wa_chpcs_show_price").closest('p').hide(); 
			$("#wa_chpcs_show_rating").closest('p').hide(); 

			
		}


			var data = {
				'action': 'wa_chpcs_action',
				'post_type': post_type,
				'nonce' : nonce
			};

			$.post(ajaxurl, data, function(response) {

				if("null"==response){

					$("#wa_chpcs_query_posts_taxonomy").closest('tr').hide(); 
					$("#wa_chpcs_query_posts_terms").closest('tr').hide(); 
					$("#wa_chpcs_query_posts_terms").removeAttr('required'); 
					$("#wa_chpcs_query_posts_taxonomy").removeAttr('required'); 

				} else {

						if('post'!=post_type) { 

						$("#wa_chpcs_query_posts_taxonomy").closest('tr').show(); 
						$("select#wa_chpcs_query_posts_taxonomy").removeAttr("disabled");
						$("select#wa_chpcs_query_posts_taxonomy").html(response);
						$("select#wa_chpcs_query_posts_terms").attr("disabled","disabled");
						$("#wa_chpcs_query_posts_terms").closest('tr').hide(); 
						$("#wa_chpcs_query_posts_tags").closest('tr').hide(); 

					}

				}

				$(".spinner").removeClass("is-active"); // add the spinner is-active class before the Ajax posting 

			});
		});

		//select terms based on post types and taxonomy
		$("select#wa_chpcs_query_posts_taxonomy").change(function(){

			var post_type = jQuery("select#wa_chpcs_query_posts_post_type option:selected").attr('value');
			var tax = jQuery("select#wa_chpcs_query_posts_taxonomy option:selected").attr('value');
			var nonce =  jQuery("#wa_wps_field").attr('value');
			$(".spinner").addClass("is-active"); // add the spinner is-active class before the Ajax posting 

			var data = {
				'action': 'wa_chpcs_action',
				'post_type': post_type,
				'tax': tax,
				'nonce' : nonce
			};

			$.post(ajaxurl, data, function(response) {
				
				 $("#wa_chpcs_query_posts_terms").attr("required","required");
				 $("select#wa_chpcs_query_posts_terms").removeAttr("disabled");
				 $("#wa_chpcs_query_posts_terms").closest('tr').show(); 
				 $("select#wa_chpcs_query_posts_terms").html(response);

				 $(".spinner").removeClass("is-active"); // add the spinner is-active class before the Ajax posting 

			});
		});

	$("select#wa_chpcs_query_posts_post_type").change(function(){

		var post_type = $("#wa_chpcs_query_posts_post_type option:selected").attr('value'); 

		var content_type = $("#wa_chpcs_query_content_type option:selected").attr('value'); 

		if('post'==post_type) { 

			$("#wa_chpcs_query_posts_taxonomy").closest('tr').hide(); 

			$("#wa_chpcs_query_content_type").attr("required","required");
			$("#wa_chpcs_query_content_type").closest('tr').show(); //hide product type

				if('category'!=content_type) {

					$("#wa_chpcs_query_posts_taxonomy").closest('tr').hide(); 
					$("#wa_chpcs_query_posts_terms").closest('tr').hide(); 
					$("#wa_chpcs_query_posts_terms").removeAttr('required'); 

				}

			} else {

				$("#wa_chpcs_query_content_type").closest('tr').hide(); //hide product type

			}

		});

	});
	</script> 

		<?php
	
	}

	//ajax action call back
	public function wa_chpcs_action_callback() {

			if(isset($_POST['post_type'])&&isset($_POST['content_type'])&&$_POST['content_type']=="tag") { 
		
			echo $this->showTags($_POST['post_type'],$_POST['content_type']);

		} else if(isset($_POST['post_type'])&&isset($_POST['tax'])) { 
		
			echo $this->showTerms($_POST['post_type'],$_POST['tax']);

		} else if(isset($_POST['post_type'])) { 

			echo $this->showTax($_POST['post_type']);

		} else {

			echo $type;
		}

		die(); // this is required to terminate immediately and return a proper response

	}

	//	show all taxonomies for given post type
	//	show all taxonomies for given post type
	public function showTax( $post_type) {
	 
		$type = '<option value="">choose...</option>';
			  $taxonomy_names = get_object_taxonomies( $post_type );

		if (empty($taxonomy_names)) {
			$type = 'null';
			return  $type;
			die(); }

		foreach ($taxonomy_names as $key => $value) {
			$type .= '<option value="' . $value . '" >' . $value . '</option>';
		}
	
		return  $type;

	}

	//	show terms to post type and tax
	public function showTerms( $post_type, $tax) {

		$type = '<option value="">choose...</option>';
		$categories = get_terms($tax, array('post_type' => array($post_type),'fields' => 'all'));

		foreach ($categories as $key => $value) {
			$type .= '<option value="' . $value->slug . '">' . $value->name . '</option>';
		}

		return  $type;

	}

	//show tags to post type and tax
	public function showTags( $post_type, $tax) {

		if ('post'==$post_type) {

			$tax = 'post_tag';
		} 

		$type = '<option value="">choose...</option>';
		$tags = get_terms($tax, array('post_type' => array($post_type),'fields' => 'all'));

		foreach ($tags as $key => $value) {
			$type .= '<option value="' . $value->slug . '">' . $value->name . '</option>';
		}

		return  $type;

	}

	//	template function
	public function wa_chpcs( $atts) {

		$arr = array();
		$arr['id']=$atts;
		echo esc_html_e(wa_chpcs_shortcode($arr));
	}

	// load text domain for localization
	public function wa_chpcs_load_textdomain() {

		load_plugin_textdomain('carousel-horizontal-posts-content-slider', false, dirname(plugin_basename(__FILE__)) . '/languages/');

	}

	//	load front e8nd scripts
	public function wa_chpcs_load_scripts( $jquery_true) {

		wp_register_style('wa_chpcs_css_file', plugins_url('/assets/css/custom-style.css', __FILE__), array(), '3.3');
		wp_enqueue_style('wa_chpcs_css_file');

		if (true===$this->options['settings']['jquery'] ) {

			wp_register_script('wa_chpcs_jquery', plugins_url('/assets/js/caroufredsel/jquery-1.8.2.min.js', __FILE__), array('jquery'), '3.3', ( 'header'===$this->options['settings']['loading_place']  ? false : true ));
			wp_enqueue_script('wa_chpcs_jquery'); 

		}

		if ( true===$this->options['settings']['transit'] ) {

			wp_register_script('wa_chpcs_transit', plugins_url('/assets/js/caroufredsel/jquery.transit.min.js', __FILE__), array('jquery'), '3.3', ( 'header'===$this->options['settings']['loading_place'] ? false : true ));
			wp_enqueue_script('wa_chpcs_transit');

		}

		if ( true===$this->options['settings']['lazyload'] ) {

			wp_register_script('wa_chpcs_lazyload', plugins_url('/assets/js/caroufredsel/jquery.lazyload.min.js', __FILE__), array('jquery'), '3.3', ( 'header'===$this->options['settings']['loading_place']  ? false : true ));
			wp_enqueue_script('wa_chpcs_lazyload'); 

		}


		if ( true===$this->options['settings']['caroufredsel'] ) {

			wp_register_script('wa_chpcs_caroufredsel_script', plugins_url('/assets/js/caroufredsel/jquery.carouFredSel-6.2.1-packed.js', __FILE__), array('jquery'), '3.3', ( 'header'===$this->options['settings']['loading_place']  ? false : true ));
			wp_enqueue_script('wa_chpcs_caroufredsel_script');

		}

		if ( true===$this->options['settings']['touchswipe'] ) {

			wp_register_script('wa_chpcs_touch_script', plugins_url('/assets/js/caroufredsel/jquery.touchSwipe.min.js', __FILE__), array('jquery'), '3.3', ( 'header'===$this->options['settings']['loading_place']  ? false : true ));
			wp_enqueue_script('wa_chpcs_touch_script'); 

		}

	}

	//	include admin scripts
	public function admin_include_scripts() {

		wp_register_style('wa_chpcs_admin_css', plugins_url('assets/css/admin.css', __FILE__), array(), '3.3');
		wp_enqueue_style('wa_chpcs_admin_css');

		//	add spectrum colour picker
		wp_register_style('wa-chpcs-admin-spectrum', plugins_url('assets/css/spectrum/spectrum.css', __FILE__), array(), '3.3');
		wp_enqueue_style('wa-chpcs-admin-spectrum');

		wp_register_script('wa-chpcs-admin-spectrum-js', plugins_url('assets/js/spectrum/spectrum.js', __FILE__), array(), '3.3');
		wp_enqueue_script('wa-chpcs-admin-spectrum-js');

		wp_register_style('wa-chpcs-date-picker', plugins_url('assets/css/jquery-ui.min.css', __FILE__), array(), '3.3');
		wp_enqueue_style('wa-chpcs-date-picker');

		//add date picker
		wp_enqueue_script(	'jquery-ui-datepicker');

		// add jQuery UI sortable
		wp_enqueue_script( 'jquery-ui-sortable');


		wp_register_script('wa-chpcs-admin-script', plugins_url('assets/js/admin-script.js', __FILE__), array(), '3.3');
		wp_enqueue_script('wa-chpcs-admin-script');

		//add select2 
		wp_register_style('wa-chpcs-admin-select2', plugins_url('assets/css/select2/select2.min.css', __FILE__), array(), '3.3');
		wp_enqueue_style('wa-chpcs-admin-select2');

		wp_register_script('wa-chpcs-admin-select2-js', plugins_url('assets/js/select2/select2.min.js', __FILE__), array(), '3.3');
		wp_enqueue_script('wa-chpcs-admin-select2-js');

		//sortable2
		wp_register_style('wa-chpcs-admin-select2-sortable', plugins_url('assets/css/select2/select2.sortable.css', __FILE__), array(), '3.3');
		wp_enqueue_style('wa-chpcs-admin-select2-sortable');

		wp_register_script('wa-chpcs-admin-select2-sortable', plugins_url('assets/js/select2/select2.sortable.min.js', __FILE__), array(), '3.3');
		wp_enqueue_script('wa-chpcs-admin-select2-sortable');

	}

	//	get excerpt
	public function wa_chpcs_clean( $excerpt, $substr) {

		$string = $excerpt;
		$string = strip_shortcodes(wp_trim_words( $string, (int) $substr ));
		return $string;

	}

	//	get post thumbnail
	public	function wa_chpcs_get_post_image( $post_content, $post_image_id, $img_type, $img_size, $slider_id, $field_name = 'custom_field') {

		if ('featured_image'==$img_type) {
			if (has_post_thumbnail( $post_image_id ) ) : 
				$img_arr = wp_get_attachment_image_src( get_post_thumbnail_id( $post_image_id ), $img_size );
				$first_img = $img_arr[0];
				endif; 
		} else if ('first_image'==$img_type) {
			$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post_content, $matches);
			$first_img = isset($matches[1][0])?$matches[1][0]:'';
		} else if ('last_image'==$img_type) {
			$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post_content, $matches);
			$first_img = isset($matches[1][count($matches[0])-1])?$matches[1][count($matches[0])-1]:''; 
		} else if ('custom_field'==$img_type) {

			$imgurl = get_field($field_name, $post_image_id)['ID'];
			$img_arr = wp_get_attachment_image_src( $imgurl , $img_size );
			$first_img = $img_arr[0];

		}
		if (empty($first_img)) {
			$options = get_post_meta( $slider_id, 'options', true ); //	options settings

			if (!empty($options['default_image'])) {

				 $first_img = $options['default_image'];

			} else {
				 $first_img = plugins_url() . '/carousel-horizontal-posts-content-slider/assets/images/default-image.jpg';

			}

		}
		  return $first_img;
	}

	//	add admin menu
	public function wa_chpcs_pre_add_to_menu() {
		
		add_submenu_page( 'edit.php?post_type=wa_chpcs', 'Settings', 'Settings', 'manage_options', 'wa_chpcs', array(&$this, 'options_page') );

	}

	//	set lazy load image
	public function get_lazy_load_image( $image) {

		if (!empty($image)) {

			 $lazy_load_image_url = $image;

		} else {
			 $lazy_load_image_url = plugins_url() . '/carousel-horizontal-posts-content-slider/assets/images/loader.gif';

		}

			return $lazy_load_image_url;
	}

	//	schedule sliders
	public function get_status_of_schedule( $start_date, $end_date) {

		$status = '';

		$startDate = strtotime($start_date);
		$endDate = strtotime($end_date);
		$currentDate = strtotime(gmdate('m/d/Y'));
		
		if (empty($start_date)&&empty($end_date)) {

			$status = 1;

		}

		if (( $currentDate >= $startDate ) && ( $currentDate <= $endDate )) {

			$status = 2;

		} else if (( $currentDate <= $startDate ) && ( $currentDate >= $endDate )) {

			$status = 3;

		}

		if (1==$status||2==$status) {

			return true;

		}

	}


	public function wa_chpcs_get_page_id_by_title($title)
	{
		$page = get_page_by_title($title, OBJECT, 'wa_chpcs');
		return $page->ID;
	}
	

	//display slider
	public function wa_chpcs_shortcode( $atts) {

		global $chpcs, $wpdb, $post;

		if ( ! is_array( $atts ) ) {

			if($this->wa_chpcs_get_page_id_by_title('existing-slider')) {

				$atts['id'] =  $this->wa_chpcs_get_page_id_by_title('existing-slider');

			}else {

				return '';
			}


			
		}

		$id = (int)$atts['id'];
		$options = get_post_meta( apply_filters( 'translate_object_id', $id, get_post_type( $id ), true ), 'options', true ); //options settings
		$slides = get_post_meta( apply_filters( 'translate_object_id', $id, get_post_type( $id ), true ), 'slides', true ); //options settings

		if (empty($options)) {
			return false; }

		$wa_chpcs_auto = isset($options['auto_scroll']) ? 'true' : 'false';
		$wa_chpcs_timeout = isset($options['timeout']) ? $options['timeout'] :'3000';//time out
		$wa_chpcs_show_controls = isset($options['show_controls']) ? $options['show_controls'] :'';
		$wa_chpcs_show_paging = isset($options['show_paging']) ? $options['show_paging'] : ''; //display paging
		$wa_chpcs_query_posts_image_type = isset($options['image_type']) ? $options['image_type'] :''; //display image type
		$wa_chpcs_query_posts_item_width = isset($options['item_width']) ? (int)$options['item_width'] : ''; //item width
		$wa_chpcs_query_posts_item_height = isset($options['item_height']) ? (int)$options['item_height'] : ''; //item height
		$wa_chpcs_query_posts_fx = isset($options['fx']) ? $options['fx'] : ''; // transition effects type
		$c_min_items = isset($options['show_posts_per_page']) ? $options['show_posts_per_page'] :'4'; // min items 
		$c_items = isset($options['items_to_be_slide']) ? $options['items_to_be_slide'] :'0'; //no of items per page
		$c_easing = $options['easing_effect']; //easing effect
		$c_duration = isset($options['duration']) ? $options['duration'] :'500';//duration
		$qp_showposts = isset($options['show_posts']) ? $options['show_posts'] :'20'; //no of posts to display
		$qp_orderby= isset($options['posts_order_by']) ? $options['posts_order_by'] :'id'; //order by
		$qp_order= isset($options['post_order']) ? $options['post_order'] :'asc';
		; //order
		$qp_category= isset($options['post_ids']) ? $options['post_ids'] : ''; // post type
		$qp_post_type= isset($options['post_type']) ? $options['post_type'] :'';	//post type
		$content_type= isset($options['content_type']) ? $options['content_type'] :'';	//post type
		$wa_chpcs_pre_direction = isset($options['direction']) ? $options['direction'] :'';	//posts direction
		$slider_template = isset($options['template']) ? $options['template'] : '';	//slider template
		$chpcs_pre_align = isset($options['align_items']) ? $options['align_items'] : '';	//align
		$wa_chpcs_circular = isset($options['circular']) ? 'true' : 'false';	//circular
		$wa_chpcs_infinite = isset($options['infinite']) ? 'true' : 'false';	//infinite
		$taxonomy= isset($options['post_taxonomy']) ? $options['post_taxonomy'] : '';	//taxonomy
		$terms= isset($options['post_terms']) ? $options['post_terms'] : '';	//terems
		$tags= isset($options['post_tags']) ? $options['post_tags'] : '';	//tags
		$wa_chpcs_query_font_colour =  isset($options['font_colour']) ? $options['font_colour'] : '';	//font colour
		$control_colour = isset($options['control_colour']) ? $options['control_colour'] : ''; //direction arrows colour
		$control_bg_colour = isset($options['control_bg_colour']) ? $options['control_bg_colour'] : '' ; //direction arrows background colour
		$arrows_hover_colour = isset($options['arrows_hover_colour']) ? $options['arrows_hover_colour'] : '' ; //direction arrows hover colour
		$size_arrows = isset($options['size_arrows']) ? $options['size_arrows'] : '' ;
		$title_font_size = isset($options['title_font_size']) ? $options['title_font_size'] : ''; //title font size
	
		$font_size = isset($options['font_size']) ? $options['font_size'] : ''; //general font size
		$custom_css = isset($options['custom_css']) ? $options['custom_css'] : ''; //custom styles
		$wa_chpcs_query_lazy_loading = isset($options['lazy_loading']) ? $options['lazy_loading'] : '' ;	//lazy loading enable
		$wa_chpcs_query_posts_lightbox = isset($options['lightbox']) ? $options['lightbox'] : '' ;	//lightbox
		$wa_chpcs_query_animate_controls = isset($options['animate_controls']) ? $options['animate_controls'] : '' ;//animate
		$wa_chpcs_query_css_transitions = isset($options['css_transitions']) ? $options['css_transitions'] : '' ;//css3 transitions
		$wa_chpcs_query_pause_on_hover = isset($options['pause_on_hover']) ? $options['pause_on_hover'] : '' ; //pause on hover
		$wa_chpcs_image_hover_effect = isset($options['image_hover_effect']) ? $options['image_hover_effect'] : '' ;	//image hover
		$lazy_img = $this->get_lazy_load_image($options['lazy_load_image']); //lazy load image
		$wa_chpcs_query_start_date = isset($options['start_date']) ? $options['start_date'] : '' ;//start date
		$wa_chpcs_query_end_date = isset($options['end_date']) ? $options['end_date'] : '' ;	//end date

		//data required for the template files
		$wa_chpcs_query_posts_display_excerpt = isset($options['show_excerpt']) ? $options['show_excerpt'] : '' ; //display excerpt type boolean
		$wa_chpcs_query_posts_display_read_more = isset($options['show_read_more_text']) ? $options['show_read_more_text'] : '' ;//display read more type boolean
		$wa_chpcs_query_posts_title =  isset($options['show_title']) ? $options['show_title'] : '' ;//display title type boolean
		$wa_chpcs_query_posts_image_height= isset($options['post_image_height']) ? (int)$options['post_image_height'] : 'auto' ; //thumbnail height string
		$wa_chpcs_query_posts_image_height = ($wa_chpcs_query_posts_image_height==0) ? 'auto' :$wa_chpcs_query_posts_image_height ;
		
		$wa_chpcs_query_posts_image_width =  isset($options['post_image_width']) ? $options['post_image_width'] : '' ; //thumbnail width string
		$wa_chpcs_read_more = isset($options['read_more_text']) ? $options['read_more_text'] : '' ; //read more text string
		$displayimage =   isset($options['show_image']) ? $options['show_image'] : '' ;//display image type boolean
		$word_imit = isset($options['word_limit']) ? $options['word_limit'] : '10' ;//word limit integer
		$wa_chpcs_query_display_from_excerpt =   isset($options['excerpt_type']) ? $options['excerpt_type'] : '' ;//display text in excerpt field
		$wa_chpcs_query_show_categories =  isset($options['show_cats']) ? $options['show_cats'] : '' ;//show categories
		$wa_chpcs_query_show_date =  isset($options['show_date']) ? $options['show_date'] : '' ;//show date
		$wa_chpcs_query_show_custom =  isset($options['show_custom_feild']) ? $options['show_custom_feild'] : '' ;//show date
		$wa_chpcs_query_image_size = isset($options['image_size']) ? $options['image_size'] : 'thumbnail' ; //image size
		$wa_chpcs_text_align = isset($options['text_align']) ? $options['text_align'] : 'left';	//text align
		$wa_chpcs_image_size = isset($options['image_size']) ? $options['image_size'] : 'left';	//image align
		$wa_chpcs_featured_image_custom_field_name = isset($options['featured_image_custom_field_name']) ? $options['featured_image_custom_field_name'] : '' ; // image custom field name
		$wa_chpcs_excerpt_custom_field_name = isset($options['excerpt_custom_field_name']) ? $options['excerpt_custom_field_name'] : '' ; // excerpt custom field name
		$wa_chpcs_contents_order = isset($options['contents_order']) ? $options['contents_order'] : array('image', 'title', 'excerpt', 'readmore');	//contents order in an item
		
		$wa_chpcs_show_custom_feilds = isset($options['show_custom_feilds']) ? $options['show_custom_feilds'] : ''; //display custom feilds data
		$wa_chpcs_show_add_to_cart = isset($options['show_add_to_cart']) ? $options['show_add_to_cart'] : ''; //display add to cart
		$wa_chpcs_show_price = isset($options['show_price']) ? $options['show_price'] : ''; //display price
		$wa_chpcs_show_rating = isset($options['show_rating']) ? $options['show_rating'] : ''; //display ratings
		$wa_chpcs_show_sale_text = isset($options['show_sale_text']) ? $options['show_sale_text'] : ''; //display sale text over image
		$number_of_rows = isset($options['number_of_rows']) ? '2' : ''; //number of rows

		// slides
		$wa_chpcs_slides_image = isset($options['featured_image_custom_field_name']) ? $options['featured_image_custom_field_name'] : '' ; // image custom field name
		
		//schedule sliders
		$status = $this->get_status_of_schedule($wa_chpcs_query_start_date, $wa_chpcs_query_end_date);

		if (false==$status) {
			return false;}
		
		$slider_gallery = '';
		$slider_gallery1 = '';


		$slider_gallery .= '<style>';

		if ('hover_image'==$wa_chpcs_image_hover_effect) { 

			$slider_gallery .= '.wa_chpcs_post_link {

			position: relative;

			display: block;
			
		}

		 .wa_featured_img .wa_chpcs_post_link .wa_chpcs_overlay,
		 .wa_featured_vid .wa_chpcs_post_link .wa_chpcs_overlay
		 {

			position: absolute;

			top: 0;

			left: 0;

			width: 100%;

			height: 100%;

			background: url(' . $options['hover_image_url'] . ') 50% 50% no-repeat;

			background-color: ' . $options['hover_image_bg'] . ';

			opacity: 0;
		}

		.wa_featured_img .wa_chpcs_post_link:hover .wa_chpcs_overlay,
		.wa_featured_vid .wa_chpcs_post_link:hover .wa_chpcs_overlay
		{

			opacity: 1;

			-moz-opacity: 1;

			filter: alpha(opacity=1);

		}';

		} 

		if (!empty($custom_css)) {
			$slider_gallery .=  $custom_css;  } 

		$slider_gallery .= '#wa_chpcs_slider_title' . $id . ' { 

			color: ' . $options['font_colour'] . ';

			font-size: ' . $options['title_font_size'] . 'px;

			margin: auto;
		}



		#wa_chpcs_image_carousel' . $id . ' {

			color: ' . $options['font_colour'] . ';

			font-size: ' . $options['font_size'] . 'px;';

		if ('up'==$wa_chpcs_pre_direction||'down'==$wa_chpcs_pre_direction) { 

			$slider_gallery .=  'width: ' . $wa_chpcs_query_posts_item_width . 'px;';

		} 

		$slider_gallery .= '}

		#wa_chpcs_image_carousel' . $id . ' .wa_chpcs_text_overlay_caption:hover::before {

   			 background-color: ' . $options['hover_image_bg'] . '!important;
		}

		#wa_chpcs_image_carousel' . $id . ' .wa_chpcs_overlay_caption::before {

			background-color: ' . $options['hover_image_bg'] . '!important;
		}

		#wa_chpcs_image_carousel' . $id . ' .wa_chpcs_prev, #wa_chpcs_image_carousel' . $id . ' .wa_chpcs_next,#wa_chpcs_image_carousel' . $id . ' .wa_chpcs_prev_v, #wa_chpcs_image_carousel' . $id . ' .wa_chpcs_next_v  {

			background: ' . $options['control_bg_colour'] . ';

			color: ' . $options['control_colour'] . ';

			font-size: ' . $options['size_arrows'] . 'px !important;

			line-height: ' . ( $options['size_arrows']+7 ) . 'px;

			width: ' . ( $options['size_arrows']+10 ) . 'px;

			height: ' . ( $options['size_arrows']+10 ) . 'px;

			margin-top: -' . $options['size_arrows'] . 'px;
			
			text-decoration: none;';

			

		if (1==$wa_chpcs_query_animate_controls) {
			if ('left'==$wa_chpcs_pre_direction||'right'==$wa_chpcs_pre_direction) { 

				$slider_gallery .= 'opacity: 0;';

			} }

		$slider_gallery .= '}

		#wa_chpcs_image_carousel' . $id . ' .wa_chpcs_prev:hover, #wa_chpcs_image_carousel' . $id . ' .wa_chpcs_next:hover {

			color: ' . $options['arrows_hover_colour'] . ';

		}

		#wa_chpcs_pager_' . $id . ' a {

			background: ' . $options['arrows_hover_colour'] . ';

		}';

		$slider_gallery .= '#wa_chpcs_image_carousel' . $id . ' li img {';
	

		if ('grayscale'==$wa_chpcs_image_hover_effect||'saturate'==$wa_chpcs_image_hover_effect||'sepia'==$wa_chpcs_image_hover_effect) { 

			$slider_gallery .= 'filter: url("data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\'><filter id=\'grayscale\'><feColorMatrix type=\'matrix\' values=\'0.3333 0.3333 0.3333 0 0 0.3333 0.3333 0.3333 0 0 0.3333 0.3333 0.3333 0 0 0 0 0 1 0\'/></filter></svg>#grayscale"); /* Firefox 3.5+ */
			
			filter: gray; /* IE6-9 */

			-webkit-filter: ' . $wa_chpcs_image_hover_effect . '(100%); /* Chrome 19+ & Safari 6+ */';

		} 

		$slider_gallery .= '}';

		$slider_gallery .= '#wa_chpcs_image_carousel' . $id . ' li .wa_featured_img, #wa_chpcs_image_carousel' . $id . ' li .wa_featured_vid {

			text-align: ' . $options['image_align'] . ';

		}

		#wa_chpcs_image_carousel' . $id . ' li  {

			text-align: ' . $options['text_align'] . ';

		}';

		$slider_gallery .=  '#wa_chpcs_image_carousel' . $id . ' li img:hover {';

		if (!empty($wa_chpcs_image_hover_effect)) { 

			if ('border'==$wa_chpcs_image_hover_effect) { 

				$slider_gallery .= 'border : solid 1px ' . $options['control_bg_colour'] . ';';

			} else if ('grayscale'==$wa_chpcs_image_hover_effect||'saturate'==$wa_chpcs_image_hover_effect||'sepia'==$wa_chpcs_image_hover_effect) { 

				$slider_gallery .=  'filter: none;
			-webkit-filter: ' . $wa_chpcs_image_hover_effect . '(0%);';

			} } 
		
		$slider_gallery .= '</style>';
		
		if ('up'==$wa_chpcs_pre_direction||'down'==$wa_chpcs_pre_direction) {

			$chpcs_pre_responsive = '0';

		} else {
			$chpcs_pre_responsive = isset($options['responsive']) ? $options['responsive'] : '';
		}

		$data_to_be_passed = array(
			'id' => $id,
			'chpcs_pre_responsive' => intval($chpcs_pre_responsive),
			'wa_chpcs_pre_direction' => $wa_chpcs_pre_direction,
			'chpcs_pre_align' => $chpcs_pre_align,
			'wa_chpcs_auto' => $wa_chpcs_auto,
			'wa_chpcs_timeout' => $wa_chpcs_timeout,
			'c_items' => $c_items,
			'wa_chpcs_query_posts_fx' => $wa_chpcs_query_posts_fx,
			'c_easing' => $c_easing,
			'c_duration' => $c_duration,
			'wa_chpcs_query_pause_on_hover' => $wa_chpcs_query_pause_on_hover,
			'wa_chpcs_infinite' => $wa_chpcs_infinite,
			'wa_chpcs_circular' => $wa_chpcs_circular,
			'wa_chpcs_query_lazy_loading' => $wa_chpcs_query_lazy_loading,
			'wa_chpcs_query_posts_item_width' => $wa_chpcs_query_posts_item_width,
			'c_min_items' => $c_min_items,
			'wa_chpcs_query_css_transitions' => $wa_chpcs_query_css_transitions,
			'wa_chpcs_query_posts_lightbox' => $wa_chpcs_query_posts_lightbox,
			'wa_chpcs_query_animate_controls' => $wa_chpcs_query_animate_controls,
		);

		// add custom js

		$data_json_str = json_encode($data_to_be_passed);
		$slider_gallery .= '<script>';



		
		$slider_gallery .= 'jQuery(document).ready(function($) {

			var wa_vars = ' . $data_json_str . ";



    //lazy loading
    if (wa_vars.wa_chpcs_query_lazy_loading) {
        function loadImage() {
            jQuery('img.wa_lazy').lazyload({
                container: jQuery('#wa_chpcs_image_carousel' + wa_vars.id)
            });
        }

    }

    $('#wa_chpcs_foo' + wa_vars.id).carouFredSel({
            responsive: (parseInt(wa_vars.chpcs_pre_responsive) == 1) ? true : false,
            direction: wa_vars.wa_chpcs_pre_direction,
            align: wa_vars.chpcs_pre_align,
            width: (parseInt(wa_vars.chpcs_pre_responsive) != 1) ? '100%' : '',
            auto: {
                play: (wa_vars.wa_chpcs_auto=='true') ? true: false,
                timeoutDuration: wa_vars.wa_chpcs_timeout
            },
            scroll: {
                items: (wa_vars.c_items && wa_vars.c_items != 0) ? wa_vars.c_items : '',
                fx: wa_vars.wa_chpcs_query_posts_fx,
                easing: wa_vars.c_easing,
                duration: parseInt(wa_vars.c_duration),
                pauseOnHover: (wa_vars.wa_chpcs_query_pause_on_hover == 1) ? true : false,
            },
            infinite: (wa_vars.wa_chpcs_infinite=='true') ? true: false,
            circular: (wa_vars.wa_chpcs_circular=='true') ? true: false,
            onCreate: function(data) {
                if (wa_vars.wa_chpcs_query_lazy_loading) {
                    loadImage();
                }
            },
            prev: {
                onAfter: function(data) {
                    if (wa_vars.wa_chpcs_query_lazy_loading) {
                        loadImage();
                    }
                },

                button: '#foo' + wa_vars.id + '_prev'
            },
            next: {
                onAfter: function(data) {
                    if (wa_vars.wa_chpcs_query_lazy_loading) {
                        loadImage();
                    }
                },
                button: '#foo' + wa_vars.id + '_next'
            },
            items: {
                width: (parseInt(wa_vars.chpcs_pre_responsive)== 1) ? wa_vars.wa_chpcs_query_posts_item_width : '',
                visible: (parseInt(wa_vars.chpcs_pre_responsive) == 0 && wa_vars.wa_chpcs_pre_direction == 'up' || wa_vars.wa_chpcs_pre_direction == 'down') ? wa_vars.c_min_items : '',

                visible: {
                    min: (parseInt(wa_vars.chpcs_pre_responsive) == 1) ? 1 : '',
                    max: (parseInt(wa_vars.chpcs_pre_responsive) == 1) ? parseInt(wa_vars.c_min_items) : '',
                }
            },
            pagination: {
                container: '#wa_chpcs_pager_' + wa_vars.id
            }
        }

        , {
            transition: (wa_vars.wa_chpcs_query_css_transitions == 1) ? true : false
        }


    );
    
    //touch swipe
    if (wa_vars.wa_chpcs_pre_direction == 'up' || wa_vars.wa_chpcs_pre_direction == 'down') {

        $('#wa_chpcs_foo' + wa_vars.id).swipe({
            excludedElements: 'button, input, select, textarea, .noSwipe',
            swipeUp
            : function() {
                $('#wa_chpcs_foo' + wa_vars.id).trigger('next', 'auto');
            },
            swipeDown
            : function() {
                $('#wa_chpcs_foo' + wa_vars.id).trigger('prev', 'auto');
                console.log('swipeRight');
            },
            tap: function(event, target) {
                $(target).closest('.wa_chpcs_slider_title').find('a').click();
            }
        })

    } else {
        $('#wa_chpcs_foo' + wa_vars.id).swipe({
            excludedElements: 'button, input, select, textarea, .noSwipe',
            swipeLeft: function() {
                $('#wa_chpcs_foo' + wa_vars.id).trigger('next', 'auto');
            },
            swipeRight: function() {
                $('#wa_chpcs_foo' + wa_vars.id).trigger('prev', 'auto');
                console.log('swipeRight');
            },
            tap: function(event, target) {
                $(target).closest('.wa_chpcs_slider_title').find('a').click();
            }
        })
    }

    //animation for next and prev
    if (wa_vars.wa_chpcs_query_animate_controls == 1) {
        if (wa_vars.wa_chpcs_pre_direction == 'left' || wa_vars.wa_chpcs_pre_direction == 'right') {
			jQuery(function( $ ) {

				$('#wa_chpcs_image_carousel' + wa_vars.id)
                .hover(function() {
                    $('#wa_chpcs_image_carousel' + wa_vars.id + ' .wa_chpcs_prev').animate({
                        'left': '1.2%',
                        'opacity': 1
                    }), 300;
                    $('#wa_chpcs_image_carousel' + wa_vars.id + ' .wa_chpcs_next').animate({
                        'right': '1.2%',
                        'opacity': 1
                    }), 300;
                }, function() {
                    $('#wa_chpcs_image_carousel' + wa_vars.id + ' .wa_chpcs_prev').animate({
                        'left': 0,
                        'opacity': 0
                    }), 'fast';
                    $('#wa_chpcs_image_carousel' + wa_vars.id + ' .wa_chpcs_next').animate({
                        'right': 0,
                        'opacity': 0
                    }), 'fast';
				});
				
			});

        }
    }

});";

		$slider_gallery .= '</script>';
	

		if ('rand'==$qp_order) {  

			$qp_orderby='rand'; 
		}

		$post_ids=explode(',', $qp_category);

		$args = array( 'numberposts' => $qp_showposts, 'suppress_filters' => false,  'post__in' => $post_ids,'post_status' => 'publish', 'order'=> $qp_order,  'orderby' => $qp_orderby,  'post_type' => $qp_post_type);
		
		$args_custom_post_type_only =  array( 'posts_per_page' => $qp_showposts, 'suppress_filters' => false, 'post_status' => 'publish', 'product_cat' => '', 'order'=> $qp_order, 'orderby' => $qp_orderby, 'post_type' => $qp_post_type);	
		
		$args_custom = array(
			'posts_per_page' => $qp_showposts,
			'post_type' => $qp_post_type,
			'order'=> $qp_order, 
			'orderby' => $qp_orderby,
			'suppress_filters' => false,
			'post_status'  => 'publish',
			'tax_query' => array(
						array(
							'taxonomy' => $taxonomy,
							'field' => 'slug',
							'terms' => $terms
						)
					)
			);

		if ('post'==$qp_post_type) {

			if ('newest'==$content_type) {

				$args = array(  
				'post_type' => $qp_post_type,  
				'orderby' =>'date','order' => 'DESC',
				'suppress_filters' => false,
				'posts_per_page' => $qp_showposts,
				'post_status'  => 'publish',
				'stock' => 1
				);  
	  
				$myposts_custom = get_posts( $args );

			} else if ('category'==$content_type) {
			
				$arr = array();
					
				$args_custom = array(
				'posts_per_page' => $qp_showposts,
				'post_type' => $qp_post_type,
				'order'=> $qp_order, 
				'suppress_filters' => false,
				'orderby' => $qp_orderby,
				'post_status'  => 'publish',
				'tax_query' => array(
				array(
				'taxonomy' => 'category',
				'field' => 'slug',
				'terms' => $terms)));

				$temp_arr	=	get_posts( $args_custom );
				$arr = array_merge($arr, $temp_arr);

				$myposts_custom = $arr;

			} 

		} else if ($qp_post_type&&$taxonomy&&$terms) {

			$myposts_custom = get_posts( $args_custom );

		} else if ($qp_post_type&&!$taxonomy&&!$qp_category) {

			$myposts_custom = get_posts($args_custom_post_type_only);

		}

		$myposts = array();

		if (isset($myposts_posts)&&isset($myposts_custom)) {

			$myposts = array_merge($myposts_posts, $myposts_custom );

		} else if (isset($myposts_posts)) {

			$myposts = $myposts_posts;

		} else if (isset($myposts_custom)) {

			$myposts = $myposts_custom;

		}

		 if (!isset($myposts)||empty($myposts)) { 

			return false;
		}

		//include theme
		include $this->wa_chpcs_file_path($slider_template);

		wp_reset_postdata(); 
		
		return $slider_gallery;
			
	}

	// view path for the theme files
	public function wa_chpcs_file_path( $view_name, $is_php = true ) {

		$temp_path = get_stylesheet_directory() . '/carousel-horizontal-posts-content-slider/templates/';

		if (file_exists($temp_path)) {

			if ( strpos( $view_name, '.php' ) === false && $is_php ) {
				return $temp_path . '/' . $view_name . '/' . $view_name . '.php';
			}
			return $temp_path . $view_name;

		} else {

			if ( strpos( $view_name, '.php' ) === false && $is_php ) {
				return WA_CHPCS_PLUGIN_TEMPLATE_DIRECTORY . '/' . $view_name . '/' . $view_name . '.php';
			}
			return WA_CHPCS_PLUGIN_TEMPLATE_DIRECTORY . $view_name;
		}

	}

	//get related posts
	public function wa_get_related_posts( $post_id, $related_count, $args = array() ) {
		$args = wp_parse_args( (array) $args, array(
			'orderby' => 'rand',
			'return'  => 'query', 
		) );
	 
		$related_args = array(
			'post_type'      => get_post_type( $post_id ),
			'posts_per_page' => $related_count,
			'post_status'    => 'publish',
			'post__not_in'   => array( $post_id ),
			'orderby'        => $args['orderby'],
			'suppress_filters' => false,
			'tax_query'      => array()
		);
	 
		$post       = get_post( $post_id );
		$taxonomies = get_object_taxonomies( $post, 'names' );
	 
		foreach ( $taxonomies as $taxonomy ) {
			$terms = get_the_terms( $post_id, $taxonomy );
			if ( empty( $terms ) ) {
				continue;
			}
			$term_list = wp_list_pluck( $terms, 'slug' );
			$related_args['tax_query'][] = array(
				'taxonomy' => $taxonomy,
				'field'    => 'slug',
				'terms'    => $term_list
			);
		}
	 
		if ( count( $related_args['tax_query'] ) > 1 ) {
			$related_args['tax_query']['relation'] = 'OR';
		}
	 
		if (  'query'==$args['return']  ) {
			return $related_args ;
		} else {
			return $related_args;
		}
	}

	//remove default auto save
	public function wa_chpcs_disable_autosave() {

		global $post;
		if (isset($post->ID)&&get_post_type($post->ID) == 'wa_chpcs') {
			wp_dequeue_script('autosave');
		}
	}

	//remove default publish box of the custom post type 
	public function wa_chpcs_remove_publish_box() {

		remove_meta_box( 'submitdiv', 'wa_chpcs', 'side' );
	
	}

	//add metaboxes to the page
	public function wa_chpcs_add_meta_boxes() {


		add_meta_box('wa_chpcs_type_meta_box', esc_html( 'Type of the slider', 'chpcs' ), array( $this, 'wa_chpcs_type_meta_box' ), 'wa_chpcs', 'normal', 'high');
		add_meta_box('wa_chpcs_custom_publish_meta_box', esc_html( 'Save', 'chpcs' ), array( $this, 'wa_chpcs_custom_publish_meta_box' ), 'wa_chpcs', 'side');
		add_meta_box('wa_chpcs_shortcode_meta_box', esc_html( 'Shortcode', 'chpcs' ), array( $this, 'wa_chpcs_shortcode_meta_box' ), 'wa_chpcs', 'side');
		add_meta_box('wa_chpcs_banner_meta_box', esc_html( 'Useful Links', 'chpcs' ), array( $this, 'wa_chpcs_banner_meta_box' ), 'wa_chpcs', 'side');
		add_meta_box('wa_chpcs_options_metabox', esc_html( 'Options', 'chpcs' ), array( $this, 'wa_chpcs_options_meta_box' ), 'wa_chpcs');

	}


	// public function disable_drag_metabox() {
	// 	wp_deregister_script('postbox');
	// }

	public function wa_chpcs_type_meta_box( $post) {

		$slider_id = $post->ID;

		$slider_options = get_post_meta( $slider_id, 'options', true );

		if ( ! $slider_options ) {
			$slider_options = self::default_options();
		}

		include $this->wa_chpcs_view_path( __FUNCTION__ );
	}




	//custom publish meta box
	public function wa_chpcs_custom_publish_meta_box( $post ) {

		$slider_id = $post->ID;
		$post_status = get_post_status( $slider_id );
		$delete_link = get_delete_post_link( $slider_id );
		$nonce = wp_create_nonce( 'ssp_slider_nonce' );
		include $this->wa_chpcs_view_path( __FUNCTION__ );
	}


	//publish meta box
	public function wa_rs_custom_publish_meta_box( $post ) {

		$slider_id = $post->ID;
		$post_status = get_post_status( $slider_id );
		$delete_link = get_delete_post_link( $slider_id );
		$nonce = wp_create_nonce( 'ssp_slider_nonce' );
		include $this->wa_chpcs_view_path( __FUNCTION__ );
	}

	//banner box
	public function wa_chpcs_banner_meta_box( $post ) {

		include $this->wa_chpcs_view_path( __FUNCTION__ );
	}

	//short code meta box
	public function wa_chpcs_shortcode_meta_box( $post ) {
		$slider_id = $post->ID;
		if ( get_post_status( $slider_id ) !== 'publish' ) {

			echo esc_html( 'Please, fill the required fields. Then click on the Create Slider button to get the slider shortcode.', 'chpcs' );
			return;
		}
		$slider_title = get_the_title( $slider_id );
		$shortcode = sprintf( "[%s id='%s']", 'carousel-horizontal-posts-content-slider', $slider_id, $slider_title );
		$template_code = sprintf( "<?php echo do_shortcode('[%s id=%s]');?>", 'carousel-horizontal-posts-content-slider', $slider_id, $slider_title );
		include $this->wa_chpcs_view_path( __FUNCTION__ );
	}

	//set options meta box
	public function wa_chpcs_options_meta_box( $post ) {
		$slider_id = $post->ID;

		$slider_options = get_post_meta( $slider_id, 'options', true );

		if ( ! $slider_options ) {
			$slider_options = self::default_options();
		}

		include $this->wa_chpcs_view_path( __FUNCTION__ );
	}


	//view path for the template files
	public function wa_chpcs_view_path( $view_name, $is_php = true ) {

		if ( strpos( $view_name, '.php' ) === false && $is_php ) {
			return WA_CHPCS_PLUGIN_VIEW_DIRECTORY . $view_name . '.php';
		}
		
		return WA_CHPCS_PLUGIN_VIEW_DIRECTORY . $view_name;
	}

	//register setting for admin page 
	public function register_settings() {

		register_setting('wa_chpcs_settings', 'wa_chpcs_settings', array(&$this, 'validate_options'));
		//general settings
		add_settings_section('wa_chpcs_settings', esc_html('', 'chpcs'), '', 'wa_chpcs_settings');
		add_settings_field('wa_chpcs_loading_place', esc_html('Loading place:', 'chpcs'), array(&$this, 'wa_chpcs_loading_place'), 'wa_chpcs_settings', 'wa_chpcs_settings');
		add_settings_field('wa_chpcs_jquery', esc_html('Load jQuery:', 'chpcs'), array(&$this, 'wa_chpcs_jquery'), 'wa_chpcs_settings', 'wa_chpcs_settings');
		add_settings_field('wa_chpcs_transit', esc_html('Load transit:', 'chpcs'), array(&$this, 'wa_chpcs_transit'), 'wa_chpcs_settings', 'wa_chpcs_settings');
		add_settings_field('wa_chpcs_caroufredsel', esc_html('CarouFredsel:', 'chpcs'), array(&$this, 'wa_chpcs_caroufredsel'), 'wa_chpcs_settings', 'wa_chpcs_settings');
		add_settings_field('wa_chpcs_lazyload', esc_html('Lazyload:', 'chpcs'), array(&$this, 'wa_chpcs_lazyload'), 'wa_chpcs_settings', 'wa_chpcs_settings');
		add_settings_field('wa_chpcs_touch_swipe', esc_html('TouchSwipe:', 'chpcs'), array(&$this, 'wa_chpcs_touch_swipe'), 'wa_chpcs_settings', 'wa_chpcs_settings');
		add_settings_field('wa_chpcs_deactivation_delete', esc_html('Deactivation:', 'chpcs'), array(&$this, 'wa_chpcs_deactivation_delete'), 'wa_chpcs_settings', 'wa_chpcs_settings');

	}



	//loading place
	public function wa_chpcs_loading_place() {
		echo '
		<div id="wa_chpcs_loading_place" class="wplikebtns">';

		foreach ($this->loading_places as $val => $trans) {
			$val = esc_attr($val);

			echo '
			<input id="rll-loading-place-' . esc_attr($val) . '" type="radio" name="wa_chpcs_settings[loading_place]" value="' . esc_attr($val) . '" ' . checked($val, $this->options['settings']['loading_place'], false) . ' />
			<label for="rll-loading-place-' . esc_attr($val) . '">' . esc_html($trans) . '</label>';
		}

		echo '
			<p class="description">' . esc_html('Select where all the scripts should be placed.', 'chpcs') . '</p>
		</div>';
	}

	//delete on deactivation
	public function wa_chpcs_deactivation_delete() {
		echo '
		<div id="rll_deactivation_delete" class="wplikebtns">';

		foreach ($this->choices as $val => $trans) {
			echo '
			<input id="rll-deactivation-delete-' . esc_attr($val) . '" type="radio" name="wa_chpcs_settings[deactivation_delete]" value="' . esc_attr($val) . '" ' . checked(( 'yes'===$val  ? true : false ), $this->options['settings']['deactivation_delete'], false) . ' />
			<label for="rll-deactivation-delete-' . esc_attr($val) . '">' . esc_html($trans) . '</label>';
		}

		echo '
			<p class="description">' . esc_html('Delete settings on plugin deactivation.', 'chpcs') . '</p>
		</div>';
	}

	//enable jquery
	public function wa_chpcs_jquery() {
		echo '
		<div id="wa_chpcs_jquery" class="wplikebtns">';

		foreach ($this->choices as $val => $trans) {
			$val = esc_attr($val);

			echo '
			<input id="jquery-' . esc_attr($val) . '" type="radio" name="wa_chpcs_settings[jquery]" value="' . esc_attr($val) . '" ' . checked(( 'yes'===$val  ? true : false ), $this->options['settings']['jquery'], false) . ' />
			<label for="jquery-' . esc_attr($val) . '">' . esc_html($trans) . '</label>';
		}

		echo '
			<p class="description">' . esc_html('Enable this option, if you dont have jQuery on your website.', 'chpcs') . '</p>
		</div>';
	}

	//load transit
	public function wa_chpcs_transit() {
		echo '
		<div id="wa_chpcs_transit" class="wplikebtns">';

		foreach ($this->choices as $val => $trans) {
			$val = esc_attr($val);

			echo '
			<input id="transit-' . esc_attr($val) . '" type="radio" name="wa_chpcs_settings[transit]" value="' . esc_attr($val) . '" ' . checked(( 'yes'===$val  ? true : false ), $this->options['settings']['transit'], false) . ' />
			<label for="transit-' . esc_attr($val) . '">' . esc_html($trans) . '</label>';
		}

		echo '
			<p class="description">' . esc_html('Disable this option, if this script has already loaded on your web site.', 'chpcs') . '</p>
		</div>';
	}

	//load caroufredsel
	public function wa_chpcs_caroufredsel() {
		echo '
		<div id="wa_chpcs_caroufredsel" class="wplikebtns">';

		foreach ($this->choices as $val => $trans) {
			$val = esc_attr($val);

			echo '
			<input id="caroufredsel-' . esc_html($val) . '" type="radio" name="wa_chpcs_settings[caroufredsel]" value="' . esc_attr($val) . '" ' . checked(( 'yes'===$val  ? true : false ), $this->options['settings']['caroufredsel'], false) . ' />
			<label for="caroufredsel' . esc_html($val) . '">' . esc_html($trans) . '</label>';
		}

		echo '
			<p class="description">' . esc_html('Disable this option, if this script has already loaded on your web site.', 'chpcs') . '</p>
		</div>';
	}

	//load lazy load
	public function wa_chpcs_lazyload() {

		echo '
		<div id="wa_chpcs_lazyload" class="wplikebtns">';

		foreach ($this->choices as $val => $trans) {
			$val = esc_attr($val);

			echo '
			<input id="lazyload-' . esc_html($val) . '" type="radio" name="wa_chpcs_settings[lazyload]" value="' . esc_attr($val) . '" ' . checked(( 'yes'===$val  ? true : false ), $this->options['settings']['lazyload'], false) . ' />
			<label for="lazyload-' . esc_html($val) . '">' . esc_html($trans) . '</label>';
		}

		echo '
			<p class="description">' . esc_html('Disable this option, if this script has already loaded on your web site.', 'chpcs') . '</p>
		</div>';
	}


	//touch swipe
	public function wa_chpcs_touch_swipe() {

		echo '
		<div id="wa_chpcs_touch_swipe" class="wplikebtns">';

		foreach ($this->choices as $val => $trans) {
			$val = esc_attr($val);

			echo '
			<input id="touchswipe-' . esc_html($val) . '" type="radio" name="wa_chpcs_settings[touchswipe]" value="' . esc_attr($val) . '" ' . checked(( 'yes'===$val  ? true : false ), $this->options['settings']['touchswipe'], false) . ' />
			<label for="touchswipe-' . esc_html($val) . '">' . esc_html($trans) . '</label>';
		}

		echo '
			<p class="description">' . esc_html('Disable this option, if this script has already loaded on your web site.', 'chpcs') . '</p>
		</div>';

	}

	//get all post types
	public function get_post_types() {

		$post_types = get_post_types( '', 'names' ); 

		return $post_types;

	}

	//list of directories
	public function list_themes() {

		$temp_path = get_stylesheet_directory() . '/carousel-horizontal-posts-content-slider/templates/';

		if (file_exists($temp_path)) {

			$dir = new DirectoryIterator($temp_path);

		} else {

			$dir = new DirectoryIterator(WA_CHPCS_PLUGIN_TEMPLATE_DIRECTORY);
		}

		foreach ($dir as $fileinfo) {
			if ($fileinfo->isDir() && !$fileinfo->isDot()) {
				$list_of_themes[] = $fileinfo->getFilename();
			}
		}
		return $list_of_themes;

	}

	//get categories
	public function get_post_category_first_name( $qp_post_type, $post_id) {

		$first_cat_name = ' ';
				//get product category name
		if ('product'==$qp_post_type) {

			$args = array( 'taxonomy' => 'product_cat',);
			$terms = wp_get_post_terms($post_id, 'product_cat', $args);

			$first_cat_name = $terms[0]->name;

		} else {

			$category = get_the_category($post_id);
			$first_cat_name = !empty($category) ? $category[0]->cat_name : '';

		}

		return $first_cat_name;
	}

	//get post category id
	public function get_post_category_id( $qp_post_type, $post_id) {

		$first_cat_name = ' ';
				//get product category name
		if ('product'==$qp_post_type) {

			$args = array( 'taxonomy' => 'product_cat',);
			$terms = wp_get_post_terms($post_id, 'product_cat', $args);

			$first_cat_name = $terms[0]->term_id;

		} else {

			$category = get_the_category($post_id);
			$first_cat_name = !empty($category) ? $category[0]->term_id : '';

		}

		return $first_cat_name;
	}

	//get text type to display
	public function get_text_type( $wa_post, $wa_chpcs_query_display_from_excerpt, $post_id, $field_name) {

		$text_type = '';

		if (isset($field_name) && !empty($field_name)) {

			$text_type =  get_field($field_name, $post_id);

		} else if (1==$wa_chpcs_query_display_from_excerpt) {

			$text_type = $wa_post->post_excerpt;
		} else {
			

			$text_type = $wa_post->post_content;

		}

		return $text_type;

	}

	//options page
	public function options_page() {

		$tab_key = ( isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'general-settings' );

		echo '<div class="wrap">
			<h2>' . esc_html('Carousel Horizontal Posts Content Slider', 'chpcs') . '</h2>
			<h2 class="nav-tab-wrapper">';

		foreach ($this->tabs as $key => $name) {
			echo '
			<a class="nav-tab ' . ( $tab_key == $key ? 'nav-tab-active' : '' ) . '" href="' . admin_url('admin.php?page=carousel-horizontal-posts-content-slider&tab=' . $key) . '">' . esc_html($name['name']) . '</a>';
		}

		echo '
			</h2>
			<div class="wa-chpcs-settings">
				<form action="options.php" method="post">';

		wp_nonce_field('update-options');
		settings_fields($this->tabs[$tab_key]['key']);
		do_settings_sections($this->tabs[$tab_key]['key']);

		echo '<p class="submit">';
		submit_button('', 'primary', $this->tabs[$tab_key]['submit'], false);
		echo ' ';
		echo esc_html(submit_button(esc_html('Reset to defaults', 'chpcs'), 'secondary', $this->tabs[$tab_key]['reset'], false));
		echo '</p></form></div><div class="clear"></div></div>';
	}

	//load defaults
	public function load_defaults() {
		
		$this->choices = array(
			'yes' => esc_html('Enable', 'chpcs'),
			'no' => esc_html('Disable', 'chpcs')
		);

		$this->loading_places = array(
			'header' => __('Header', 'chpcs'),
			'footer' => esc_html('Footer', 'chpcs')
		);

		$this->tabs = array(
			'general-settings' => array(
				'name' => __('General Settings', 'chpcs'),
				'key' => 'wa_chpcs_settings',
				'submit' => 'save_chpcs_settings',
				'reset' => 'reset_chpcs_settings',
			)
		);
	}

	//default options
	public static function default_options() {

		$default_img = plugins_url() . '/carousel-horizontal-posts-content-slider/assets/images/default-image.jpg'; // default image
		$loading_img = plugins_url() . '/carousel-horizontal-posts-content-slider/assets/images/loader.gif'; // loading image
		$hover_img = plugins_url() . '/carousel-horizontal-posts-content-slider/assets/images/hover.png'; // loading image

		$default_options = array(
			'post_type' => '',
			'content_type' => '',
			'post_taxonomy' => '',
			'post_terms' => '',
			'post_ids' => '',
			'posts_order_by' => 'id',
			'post_order' => 'asc',
			'template' => 'basic',
			'image_hover_effect' => 'none',
			'read_more_text' => 'Read more',
			'featured_image_custom_field_name' => '',
			'excerpt_custom_field_name' => '',
			'word_limit' => '10',
			'show_posts' => '20',
			'show_posts_per_page' => '4',
			'items_to_be_slide' => '0',
			'duration' => '500',
			'item_width' => '200',
			'item_height' => '350',
			'post_image_width' => '200',
			'post_image_height' => '',
			'image_type' => '',
			'easing_effect' => 'linear',
			'fx' => 'scroll',
			'align_items' => 'center',
			'font_colour' => '#000',
			'control_colour' => '#fff',
			'control_bg_colour' => '#000',
			'arrows_hover_colour' => '#ccc',
			'size_arrows' => '18',
			'title_font_size' => '14',
			'font_size' => '12',
			'default_image' => $default_img,
			'lazy_load_image' => $loading_img,
			'show_title' => true,
			'show_image' => true,
			'show_excerpt' => true,
			'title_top_of_image' => true,
			'show_read_more_text' => true,
			'excerpt_type' => false,
			'responsive' => false,
			'lightbox' => false,
			'lazy_loading' => false,
			'auto_scroll' => true,
			'draggable' => true,
			'circular' => false,
			'infinite' => true,
			'touch_swipe' => true,
			'direction' => 'right',
			'show_controls' => true,
			'animate_controls' => true,
			'show_paging' => true,
			'css_transitions' => true,
			'pause_on_hover' => true,
			'timeout' => '3000',
			'start_date' => '',
			'end_date' => '',
			'hover_image_bg' => 'rgba(40,168,211,.85)',
			'hover_image_url' => $hover_img,
			'text_align' => 'left',
			'image_size' => 'other',
			'image_align' => 'left',
			'show_custom_feilds' => true,
			'show_add_to_cart' => true,
			'show_price' => true,
			'show_rating' => true,
			'show_sale_text' => true,
			'number_of_rows' => false,
			'font_family'=>' '
		);

		return apply_filters( 'wa_chpcs_default_options', $default_options );

	}

	//validate options and register settings
	public function validate_options( $input) {

		if (isset($_POST['save_chpcs_settings'])) {

			// loading place
			$input['loading_place'] = ( isset($input['loading_place'], $this->loading_places[$input['loading_place']]) ? $input['loading_place'] : $this->defaults['settings']['loading_place'] );

			// checkboxes
			$input['caroufredsel'] = ( isset($input['caroufredsel'], $this->choices[$input['caroufredsel']]) ? ( 'yes'===$input['caroufredsel']  ? true : false ) : $this->defaults['settings']['caroufredsel'] );
			$input['lazyload'] = ( isset($input['lazyload'], $this->choices[$input['lazyload']]) ? ( 'yes'===$input['lazyload']  ? true : false ) : $this->defaults['settings']['lazyload'] );
			$input['touchswipe'] = ( isset($input['touchswipe'], $this->choices[$input['touchswipe']]) ? ( 'yes'===$input['touchswipe']  ? true : false ) : $this->defaults['settings']['touchswipe'] );
			$input['jquery'] = ( isset($input['jquery'], $this->choices[$input['jquery']]) ? ( 'yes'===$input['jquery']  ? true : false ) : $this->defaults['settings']['jquery'] );
			$input['transit'] = ( isset($input['transit'], $this->choices[$input['transit']]) ? ( 'yes'===$input['transit']  ? true : false ) : $this->defaults['settings']['transit'] );
			$input['deactivation_delete'] = ( isset($input['deactivation_delete'], $this->choices[$input['deactivation_delete']]) ? ( 'yes'===$input['deactivation_delete']  ? true : false ) : $this->defaults['settings']['deactivation_delete'] );
		

		} else if (isset($_POST['reset_chpcs_settings'])) {
			$input = $this->defaults['settings'];

			add_settings_error('reset_general_settings', 'general_reset', esc_html('Settings restored to defaults.', 'chpcs'), 'updated');
		}

		return $input;
	}

	//init process for registering button
	public function wa_chpcs_shortcode_button_init() {

		if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') && get_user_option('rich_editing') == 'true') {
			 return;
		}
   
		  add_filter('mce_external_plugins', array(&$this, 'wa_chpcs_register_tinymce_plugin'));

		  add_filter('mce_buttons', array(&$this, 'wa_chpcs_add_tinymce_button'));
	}


	//registers plugin  to TinyMCE
	public function wa_chpcs_register_tinymce_plugin( $plugin_array) {

		$plugin_array['wa_chpcs_button'] = plugins_url('assets/js/shortcode/shortcode.js', __FILE__);

		return $plugin_array;
	}

	//add button to the toolbar
	public function wa_chpcs_add_tinymce_button( $buttons) {

		$buttons[] = 'wa_chpcs_button';

		return $buttons;
	}

	//register post type for the slider
	public function wa_chpcs_init() {

		$labels = array(
			'name' => _x('Carousel Horizontal Posts Content Slider', 'post type general name'),
			'singular_name' => _x('slider', 'post type singular name'),
			'add_new' => _x('Add New', 'wa_rs_slider'), 
			'add_new_item' => esc_html('Add new slider'),
			'edit_item' => esc_html('Edit slider'),
			'new_item' => esc_html('New slider'),
			'view_item' => esc_html('View slider'),
			'search_items' => esc_html('Search sliders'),
			'not_found' => esc_html('No records found'),
			'not_found_in_trash' => esc_html('No records found in Trash'),
			'parent_item_colon' => '',
			'menu_name' => 'CHPC slider'
		);

		$args = array(
			'labels' => $labels,
			'public' => false,
			'menu_icon' => plugins_url('/assets/js/shortcode/b_img.png', __FILE__),
			'publicly_queryable' => false,
			'show_ui' => true,
			'show_in_menu' => true,
			'menu_position' => 5,
			'query_var' => false,
			'rewrite' => false,
			'capability_type' => 'post',
			'has_archive' => true,
			'hierarchical' => false,
			'supports' => array('title')
		);

		register_post_type('wa_chpcs', $args);
	}

	//update messages
	public function wa_chpcs_updated_messages( $messages) {

		global $post, $post_ID;
		$messages['wa_chpcs'] = array(
			0 => '',
			1 => sprintf(esc_html('Slider updated.'), esc_url(get_permalink($post_ID))),
			2 => esc_html('Custom field updated.'),
			3 => esc_html('Custom field deleted.'),
			4 => esc_html('Slider updated.'),
			5 => isset($_GET['revision']) ? sprintf(esc_html('Slider restored to revision from %s'), wp_post_revision_title((int) $_GET['revision'], false)) : false,
			6 => sprintf(esc_html('Slider published.'), esc_url(get_permalink($post_ID))),
			7 => esc_html('Slider saved.'),
			8 => sprintf(esc_html('Slider submitted.'), esc_url(add_query_arg('preview', 'true', get_permalink($post_ID)))),
			9 => sprintf(esc_html('Slider scheduled for: <strong>%1$s</strong>. '), date_i18n(esc_html('M j, Y @ G:i'), strtotime($post->post_date)), esc_url(get_permalink($post_ID))),
			10 => sprintf(esc_html('Slider draft updated.'), esc_url(add_query_arg('preview', 'true', get_permalink($post_ID)))),
		);
		return $messages;

	}

	//save data
	public function wa_chpcs_save_metabox_data ( $post_id) {

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}
		
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		if (isset($_POST['post_type_is_wa_chpcs'])&&isset($_REQUEST['wa_chpcs_field'])&&wp_verify_nonce(sanitize_text_field($_REQUEST['wa_chpcs_field']), 'wa_chpcs_action')) {

			$post_type_is_wa_chpcs = sanitize_text_field($_POST['post_type_is_wa_chpcs']);
		}
		
		if ( ! isset( $post_type_is_wa_chpcs ) ) {
			return;
		}

			$slider_id = $post_id;
			$post_slider_options = array();
			$slides = array();
			$image = array(
				'id' => null,
				'url' => null,
				'alt' => null,
				'link' => null,
				'caption' => null,
				'sizes' => array(
					'thumbnail' => null,
					'medium' => null,
					'large' => null,
					'full' => null
				)
			);

			$slider_options_default = self::default_options();
			$slider_options = wp_parse_args( $post_slider_options, $slider_options_default );

			$slider_options =   $_POST['slider_options'] ;

			foreach ( $slider_options as $key => $option ) :
				if ( 'true'===$option  ) {
					$slider_options[$key] = true;
				}
				if ( 'false'===$option  ) {
					$slider_options[$key] = false;
				}
			endforeach;


			if ( ! isset( $_POST['slides'] )  ) {
				$_POST['slides'] = $slides;
			}


			$slides_sani =   $_POST['slides'];
			foreach ( $slides_sani as $key => $slide ) {

				if ( empty( $slide['type'] ) ) {
					$slide['type'] = 'image';
				}

				if ( ! empty( $slide['attachment'] ) ) {

					$image['id'] = $slide['attachment'];

					$image['url'] = wp_get_attachment_url( $image['id'] );

					$image['alt'] = get_post_meta( $image['id'],
					'_wp_attachment_image_alt', true );

					$image['link'] = get_post_meta( $image['id'],
						'_wp_attachment_url', true );

					$image['title'] = get_the_title( $image['id'] );

					$image['caption'] = get_post_field( 'post_excerpt', $image['id'] );

					$sizes = get_intermediate_image_sizes();
					$sizes[] = 'full';
				
					foreach ( $sizes as $size ) {
						$img = wp_get_attachment_image_src(
							$image['id'] , $size );
						$image['sizes'][$size] = $img[0];
					}

				}
				$slide['image'] = $image;
				$slides[] = $slide;
			}

			update_post_meta( $slider_id, 'slides', $slides );
			update_post_meta( $slider_id, 'options', $slider_options );
	}

}

$carousel_horizontal_posts_content_slider = new Carousel_Horizontal_Posts_Content_Slider();