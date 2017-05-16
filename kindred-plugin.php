<?php 

/**
* Enqueue Typekit Fonts
*/
function theme_typekit() {
	wp_enqueue_script( 'theme_typekit', '//use.typekit.net/lhw5sqe.js');
}
add_action( 'wp_enqueue_scripts', 'theme_typekit' );

function theme_typekit_inline() {
  if ( wp_script_is( 'theme_typekit', 'done' ) ) { ?>
	  <script type="text/javascript">try{Typekit.load();}catch(e){}</script>
<?php }
}
add_action( 'wp_head', 'theme_typekit_inline' );


/**
* Enqueue Google Fonts
*/
function wpb_add_google_fonts() {

wp_enqueue_style( 'wpb-google-fonts', 'http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,700italic,400,700,300', false ); 
}

add_action( 'wp_enqueue_scripts', 'wpb_add_google_fonts' );

/**
 * Register custom widget areas.
 */
function dreyfus_custom_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Footer', 'dreyfus' ),
		'id'            => 'sidebar-2',
		'description'   => esc_html__( 'Add widgets here.', 'dreyfus' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'dreyfus_custom_widgets_init' );

/*
* Enable shortcodes in text widgets
*/
add_filter('widget_text','do_shortcode');

/*
 ** Allow break tags in widget titles
 */
function html_widget_title( $title ) {
//HTML tag opening/closing brackets
$title = str_replace( '[', '<', $title );
$title = str_replace( '[/', '</', $title );
$title = str_replace( 'br]', 'br>', $title );

return $title;
}
add_filter( 'widget_title', 'html_widget_title' );


/*
** Custom post type function
*/

function custom_post_type() {

// Set UI labels for Custom Post Type
	$labels = array(
		'name'                => _x( 'Properties', 'Post Type General Name', 'dreyfus' ),
		'singular_name'       => _x( 'Property', 'Post Type Singular Name', 'dreyfus' ),
		'menu_name'           => __( 'Properties', 'dreyfus' ),
		'parent_item_colon'   => __( 'Parent Property', 'dreyfus' ),
		'all_items'           => __( 'All Properties', 'dreyfus' ),
		'view_item'           => __( 'View Property', 'dreyfus' ),
		'add_new_item'        => __( 'Add New Property', 'dreyfus' ),
		'add_new'             => __( 'Add New', 'dreyfus' ),
		'edit_item'           => __( 'Edit Property', 'dreyfus' ),
		'update_item'         => __( 'Update Property', 'dreyfus' ),
		'search_items'        => __( 'Search Properties', 'dreyfus' ),
		'not_found'           => __( 'Not Found', 'dreyfus' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'dreyfus' ),
	);

// Set other options for Custom Post Type

	$args = array(
		'label'               => __( 'property', 'dreyfus' ),
		'description'         => __( 'Dreyfus Properties', 'dreyfus' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields', ),
		'hierarchical'        => false,
		'taxonomies'		  => array( 'category' ),
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_icon'           => 'dashicons-admin-customizer',
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 5,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => true,
		'publicly_queryable'  => true,
		'capability_type'     => 'page',
	);

	// Registering your Custom Post Type
	register_post_type( 'videos', $args );

}

/* Hook into the 'init' action so that the function
* Containing our post type registration is not
* unnecessarily executed.
*/

add_action( 'init', 'custom_post_type', 0 );

function namespace_add_custom_types( $query ) {
  if( is_category() || is_tag() && empty( $query->query_vars['suppress_filters'] ) ) {
	$query->set( 'post_type', array(
	 'post', 'nav_menu_item', 'properties'
		));
	  return $query;
	}
}
add_filter( 'pre_get_posts', 'namespace_add_custom_types' );

/*
* Add custom taxonomy for CPT
*/

add_action( 'init', 'dreyfus_taxonomies', 0 );

function dreyfus_taxonomies() {
	register_taxonomy(
		'listing_status',
		'properties',
		array(
			'labels' => array(
				'name' => 'Listing Status',
				'add_new_item' => 'Add New Listing Status',
				'new_item_name' => "New Listing Status"
			),
			'show_ui' => true,
			'show_tagcloud' => false,
			'hierarchical' => true
		)
	);
}

/*
* Modify the excerpt
*/
function wpse_allowedtags() {
	// Add custom tags to this string
		return '<br>,<em>,<i>,<ul>,<ol>,<li>,<a>,<p>';
	}
if ( ! function_exists( 'wpse_custom_wp_trim_excerpt' ) ) :
	function wpse_custom_wp_trim_excerpt($wpse_excerpt) {
	$raw_excerpt = $wpse_excerpt;
		if ( '' == $wpse_excerpt ) {
			$wpse_excerpt = get_the_content('');
			$wpse_excerpt = strip_shortcodes( $wpse_excerpt );
			$wpse_excerpt = apply_filters('the_content', $wpse_excerpt);
			$wpse_excerpt = str_replace(']]>', ']]&gt;', $wpse_excerpt);
			$wpse_excerpt = strip_tags($wpse_excerpt, wpse_allowedtags()); /*IF you need to allow just certain tags. Delete if all tags are allowed */
			//Set the excerpt word count and only break after sentence is complete.
				$excerpt_word_count = 80;
				$excerpt_length = apply_filters('excerpt_length', $excerpt_word_count);
				$tokens = array();
				$excerptOutput = '';
				$count = 0;
				// Divide the string into tokens; HTML tags, or words, followed by any whitespace
				preg_match_all('/(<[^>]+>|[^<>\s]+)\s*/u', $wpse_excerpt, $tokens);
				foreach ($tokens[0] as $token) {
					if ($count >= $excerpt_length && preg_match('/[\,\;\?\.\!]\s*$/uS', $token)) {
					// Limit reached, continue until , ; ? . or ! occur at the end
						$excerptOutput .= trim($token);
						break;
					}
					// Add words to complete sentence
					$count++;
					// Append what's left of the token
					$excerptOutput .= $token;
				}
			$wpse_excerpt = trim(force_balance_tags($excerptOutput));
				$excerpt_end = ' <div class="more-link"><a class="read-more" href="'. get_permalink( get_the_ID() ) . '">' . __('Read More') . '</a></div>';
				$excerpt_more = apply_filters('excerpt_more', '' . $excerpt_end);
				// After the content
				$wpse_excerpt .= $excerpt_more; /*Add read more in new paragraph */
			return $wpse_excerpt;
		}
		return apply_filters('wpse_custom_wp_trim_excerpt', $wpse_excerpt, $raw_excerpt);
	}
endif;
remove_filter('get_the_excerpt', 'wp_trim_excerpt');
add_filter('get_the_excerpt', 'wpse_custom_wp_trim_excerpt');


/*
* Custom excerpt lengths
*/
function excerpt($limit) {
	  $excerpt = explode(' ', get_the_excerpt(), $limit);
	  if (count($excerpt)>=$limit) {
		array_pop($excerpt);
		$excerpt = implode(" ",$excerpt).'...';
	  } else {
		$excerpt = implode(" ",$excerpt);
	  }
	  $excerpt = preg_replace('`\[[^\]]*\]`','',$excerpt);
	  return $excerpt;
	}

	function content($limit) {
	  $content = explode(' ', get_the_content(), $limit);
	  if (count($content)>=$limit) {
		array_pop($content);
		$content = implode(" ",$content).'...';
	  } else {
		$content = implode(" ",$content);
	  }
	  $content = preg_replace('/\[.+\]/','', $content);
	  $content = apply_filters('the_content', $content);
	  $content = str_replace(']]>', ']]&gt;', $content);
	  return $content;
}

// WOOCOMMERCE

/**
* Declare WooCommerce support
*/
add_action( 'after_setup_theme', 'woocommerce_support' );
function woocommerce_support() {
    add_theme_support( 'woocommerce' );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );
}

/**
* Limit number of related products
*/
function woo_related_products_limit() {
  global $product;
 $args['posts_per_page'] = 3;
 return $args;
}

add_filter( 'woocommerce_output_related_products_args', 'jk_related_products_args' );

  function jk_related_products_args( $args ) {
 $args['posts_per_page'] = 3; // 3 related products
 $args['columns'] = 3; // arranged in 3 columns
 return $args;
}

/**
* Remove "Add to Cart" button from shop/category pages
*/
function remove_loop_button(){
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
}
add_action('init','remove_loop_button');

/**
* Change wrapper on shop page
*/
// First remove default wrapper
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);

// Then add new wrappers
add_action('woocommerce_before_main_content', 'dff_wrapper_start', 10);
add_action('woocommerce_after_main_content', 'dff_wrapper_end', 10);

function dff_wrapper_start() {
  echo '<section id="shop-wrap" class="full">';
  echo '<div class="wrap-big">';
}

function dff_wrapper_end() {
  echo '</div>';
  echo '</section>';
}

// Remove breadcrumbs, result count, and sidebar

add_action( 'init', 'jk_remove_wc_breadcrumbs' );
function jk_remove_wc_breadcrumbs() {
	remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );
	remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
	remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);
	remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
}

/**
* Remove/rearrange items from product page
*/
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );

// hide coupon field on checkout page
function hide_coupon_field_on_checkout( $enabled ) {
	if ( is_checkout() ) {
		$enabled = false;
	}
	return $enabled;
}
add_filter( 'woocommerce_coupons_enabled', 'hide_coupon_field_on_checkout' );

/**
* Remove tabs
*/
add_filter( 'woocommerce_product_tabs', 'woo_remove_product_tabs', 98 );

function woo_remove_product_tabs( $tabs ) {

	unset( $tabs['description'] );      	// Remove the description tab
	unset( $tabs['reviews'] ); 			// Remove the reviews tab
	unset( $tabs['additional_information'] );  	// Remove the additional information tab

	return $tabs;

}

/*
* Move Product Thumbnails
*/

// remove images from left

remove_action( 'woocommerce_product_thumbnails', 'woocommerce_show_product_thumbnails', 20 );

// add them under short description
// note: this will need a bit of CSS customization!

add_action( 'woocommerce_after_single_product_summary', 'woocommerce_show_product_thumbnails', 5 );

/*
* Remove wysiwyg editor on product pages
*/

add_action('init', 'init_remove_support',100);
function init_remove_support(){
	$post_type = 'product';
	remove_post_type_support( $post_type, 'editor');
}

/*
* Remove fields from Checkout page
*/

// Hook in
add_filter( 'woocommerce_checkout_fields' , 'custom_override_checkout_fields' );

// Our hooked in function - $fields is passed via the filter!
function custom_override_checkout_fields( $fields ) {
	 unset($fields['billing']['billing_company']);
	 unset($fields['shipping']['shipping_company']);

	 return $fields;
}

add_filter( 'woocommerce_checkout_fields', 'webendev_woocommerce_checkout_fields' );
/**
 * Change Order Notes Placeholder Text - WooCommerce
 *
 */
function webendev_woocommerce_checkout_fields( $fields ) {

	$fields['order']['order_comments']['label'] = 'Please let us know below if you require ASL interpretation and/or wheelchair access';
	$fields['order']['order_comments']['placeholder'] = '';
	return $fields;
}

/**
 * Add checkbox field to the checkout
 **/
add_action('woocommerce_after_order_notes', 'my_custom_checkout_field');

function my_custom_checkout_field( $checkout ) {

	echo '<div id="checkout-custom-field"><h3>'.__('Please check the box below if you are purchasing a ticket to a screening and require ASL interpretation: ').'</h3>';

	woocommerce_form_field( 'my_checkbox', array(
		'type'          => 'checkbox',
		'class'         => array('input-checkbox'),
		'label'         => __('I require ASL interpretation.'),
		'required'  => false,
		), $checkout->get_value( 'my_checkbox' ));

	echo '</div>';
}

/**
 * Process the checkout
 **/
add_action('woocommerce_checkout_process', 'my_custom_checkout_field_process');

function my_custom_checkout_field_process() {
	global $woocommerce;

}

/**
 * Update the order meta with field value
 **/
add_action('woocommerce_checkout_update_order_meta', 'my_custom_checkout_field_update_order_meta');

function my_custom_checkout_field_update_order_meta( $order_id ) {
	if ($_POST['asl_checkbox']) update_post_meta( $order_id, 'ASL Interpretation', esc_attr($_POST['asl_checkbox']));
}

/**
* Add custom field to new order email
**/

add_filter('woocommerce_email_order_meta_keys', 'my_custom_order_meta_keys');

function my_custom_order_meta_keys( $keys ) {
	 $keys[] = 'ASL Interpretation'; // This will look for a custom field called 'Tracking Code' and add it to emails
	 return $keys;
}

/**
* Make phone number not required
**/

add_filter( 'woocommerce_billing_fields', 'wc_npr_filter_phone', 10, 1 );
function wc_npr_filter_phone( $address_fields ) {
	$address_fields['billing_phone']['required'] = false;
	return $address_fields;
}



?>