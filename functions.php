<?php

/**
 * Echoes additional code into the theme header
 * @return void
 */
function nhb_header_code()
{
	// Load header code here
	echo <<<END
END;

}
add_action('wp_head', 'nhb_header_code', 99);

// Turn on confirmation anchors for all forms
add_filter( 'gform_confirmation_anchor', '__return_true' );

// Disable XML-RPC https://www.webhostinghero.com/disable-wordpress-xml-rpc-pingbacks/ Method 3
add_filter( 'xmlrpc_enabled', '__return_false' );

/**
 * Override function to remove Divi Google Fonts (Except Open Sans) from Divi builder
 * @return array
 * @example https://designsbytierney.com/2017/06/optimize-google-font-delivery-wordpress-divi-theme/
 */
function et_builder_get_google_fonts() {
	return array();
}

/**
 * Override function to remove Divi Google Fonts (Except Open Sans) from Divi
 * @return array
 * @example https://designsbytierney.com/2017/06/optimize-google-font-delivery-wordpress-divi-theme/
 */
function et_get_google_fonts() {
	return array();
}

/**
 * General speed tweaks. Remove Query Strings.
 * @example https://geekflare.com/wordpress-performance-optimization-without-plugin/
 * @example https://geekflare.com/wordpress-performance-optimization-without-plugin/#Remove-Query-Strings
 * @return void
 */
function remove_cssjs_ver( $src ) {
	if( strpos( $src, '?ver=' ) )
		$src = remove_query_arg( 'ver', $src );
	return $src;
}
add_filter( 'style_loader_src', 'remove_cssjs_ver', 10, 2 );
add_filter( 'script_loader_src', 'remove_cssjs_ver', 10, 2 );

// Remove RSD Links https://geekflare.com/wordpress-performance-optimization-without-plugin/#Remove-RSD-Links
remove_action( 'wp_head', 'rsd_link' ) ;

// Disable Emoticons https://geekflare.com/wordpress-performance-optimization-without-plugin/#Disable-Emoticons
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );
// Remove Shortlink https://geekflare.com/wordpress-performance-optimization-without-plugin/#Remove-Shortlink
remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);

/**
 * Disable Embeds 
 * @example https://geekflare.com/wordpress-performance-optimization-without-plugin/#Disable-Embeds
 * @return void
 */
function disable_embed(){
	wp_dequeue_script( 'wp-embed' );
}
add_action( 'wp_footer', 'disable_embed' );
// Remove WLManifest Link https://geekflare.com/wordpress-performance-optimization-without-plugin/#Remove-WLManifest-Link
remove_action( 'wp_head', 'wlwmanifest_link' ) ;

/**
 * Disable Self Pingback  
 * @example https://geekflare.com/wordpress-performance-optimization-without-plugin/#Disable-Self-Pingback
 * @return void
 */
function disable_pingback( &$links ) {
	foreach ( $links as $l => $link )
		if ( 0 === strpos( $link, get_option( 'home' ) ) )
			unset($links[$l]);
}
add_action( 'pre_ping', 'disable_pingback' );


/**
 * Enqueues styles and other scripts to make the page work.
 * Add files like theme CSS, fonts, JS libraries.
 * @example https://digwp.com/2016/01/include-styles-child-theme/
 * @return void
 */
function nhb_enqueue_theme_styles()
{
	//Add Google Fonts
	// https://onlinemediamasters.com/load-local-fonts-locally-wordpress/
	wp_enqueue_style( 'site-fonts', get_stylesheet_directory_uri() .'/src/fonts/stylesheet.css', array(), 1.0 );

	// Add font awesome
	// wp_enqueue_style( 'fontawesome', get_stylesheet_directory_uri() .'/fonts/font-awesome/css/all.css', array(), 1.0 );

	// enqueue parent styles
	wp_enqueue_style(
		'parent-theme',
		get_template_directory_uri() . '/style.css'
	);

	// enqueue child styles
	// TODO Switch to the minimized version before launch
	wp_enqueue_style(
		'child-theme',
		get_stylesheet_directory_uri() . '/dist/child-styles.min.css',
		array('parent-theme')
	);

	wp_enqueue_script('neighborhood-bids-js', 
		get_stylesheet_directory_uri() . '/dist/child-scripts.frontend.bundle.min.js', 
		array('jquery'), 
		filemtime(get_stylesheet_directory() . '/dist/child-scripts.frontend.bundle.min.js'), 
		true
	);
	
}
add_action('wp_enqueue_scripts', 'nhb_enqueue_theme_styles');

/**
 * Remove JQuery Migrate
 * Moderns installs don't need it
 * @example https://dotlayer.com/what-is-migrate-js-why-and-how-to-remove-jquery-migrate-from-wordpress/
 * @return void
 */
function nhb_remove_jquery_migrate($scripts)
{
	if (!is_admin() && isset($scripts->registered['jquery'])) {
		$script = $scripts->registered['jquery'];

		if ($script->deps) {
			// Check whether the script has any dependencies
			$script->deps = array_diff($script->deps, array('jquery-migrate'));
		}
	}
}
add_action('wp_default_scripts', 'nhb_remove_jquery_migrate');

/**
 * Dequeues styles and other scripts to make the page work.
 * Add files like theme CSS, fonts, JS libraries.
 * @return void
 */
function nhb_dequeue_theme_styles()
{
	// Remove the loading of Hummingbird's custom css file
	wp_deregister_style('wphb-critical-css');

	// Remove Block Library CSS from WordPress if not using Gutenberg
	// https://wpassist.me/how-to-remove-block-library-css-from-wordpress/
	wp_dequeue_style('wp-block-library');

	//  Remove font from builder
	wp_dequeue_style('et-builder-googlefonts');
	wp_dequeue_style('et-builder-googlefonts-cached');

	// Disable Dashicons on Front-end
	// https://geekflare.com/wordpress-performance-optimization-without-plugin/#Disable-Dashicons-on-Front-end
	if (current_user_can('update_core')) {
	} else {
		wp_deregister_style('dashicons');
	}
}
add_action('wp_enqueue_scripts', 'nhb_dequeue_theme_styles', 20);

/**
 * Deregister Gravity Stylesheets and Scripts from specific pages
 * @example https://aaronjerad.com/blog/remove-gravity-forms-css-and-scripts-from-specific-pages/
 * @return void
 */
function deregister_scripts(){

	//Change this conditional to target whatever page or form you need.
	if(is_page('contact')) {
	} else {
		//These are the CSS stylesheets 
		wp_deregister_style("gforms_formsmain_css"); 	
		wp_deregister_style("gforms_reset_css");
		wp_deregister_style("gforms_ready_class_css");
		wp_deregister_style("gforms_browsers_css");

		//These are the scripts. 
		//NOTE: Gravity forms automatically includes only the scripts it needs, so be careful here. 
		wp_deregister_script("gforms_gravityforms");
		wp_deregister_script("gforms_json");
		wp_deregister_script("gforms_recaptcha");
		//wp_deregister_script("gforms_conditional_logic_lib");
		//wp_deregister_script("gforms_ui_datepicker");
		//wp_deregister_script("gforms_character_counter");
		//wp_deregister_script("jquery");

	}
}
//add_action("gform_enqueue_scripts", "deregister_scripts");

/**
 * Remove Woocommerce scripts/css if not a woocommerce page
 * @example https://sridharkatakam.com/how-to-load-woocommerce-css-and-js-only-on-shop-specific-pages-in-wordpress/
 * @return void
 */
function sk_conditionally_remove_wc_assets() {

	// if WooCommerce is not active, abort.
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	// if this is a WooCommerce related page, abort.
	if ( is_woocommerce() || is_cart() || is_checkout() || is_page( array( 'my-account' ) ) ) {
		return;
	}

	wp_deregister_style('woocommerce-layout');
	wp_deregister_style('woocommerce-smallscreen');
	wp_deregister_style('woocommerce-general');
	wp_deregister_style('wc-block-style');
	remove_action( 'wp_enqueue_scripts', [ WC_Frontend_Scripts::class, 'load_scripts' ] );
	remove_action( 'wp_print_scripts', [ WC_Frontend_Scripts::class, 'localize_printed_scripts' ], 5 );
	remove_action( 'wp_print_footer_scripts', [ WC_Frontend_Scripts::class, 'localize_printed_scripts' ], 5 );

}
//add_action( 'get_header', 'sk_conditionally_remove_wc_assets' );

/**
 * Add year shortcut for copyright footer 
 * [cur_year] shortcode => 2012
 * @example https://optimusdivi.com/use-copy-year-shortcodes-divi-custom-footer-text/
 * @return void
 */
function nhb_year_shortcode() { 
	return date('Y'); 
}
add_shortcode('cur_year', 'nhb_year_shortcode');


function wc_subscriptions_custom_price_string( $pricestring ) {
	// More verstile than the method below as global product isn't available on the cart's page
	$newprice = str_replace( 'on the 1st of each month', ' / month', $pricestring );

	// global $product;

	// $newprice = $pricestring;
	// $terms = get_the_terms( $product->ID, 'product_cat' );

	// // Only take in the first category
	// $category = wp_list_pluck($terms, 'slug')[0] ?? false;

	// // Skip if there are no categories
	// if ( !$category ) return $newprice;
	
	// if ( in_array($category, ['pool-service', 'lawn-care']) ) {
	// 	$newprice = str_replace( 'on the 1st of each month', ' / month', $pricestring );
	// }

	// if ( in_array($category, ['pest-control']) ) {
	// 	// No change
	// }
	
	return $newprice;
}

add_filter( 'woocommerce_subscriptions_product_price_string', 'wc_subscriptions_custom_price_string' );
add_filter( 'woocommerce_subscription_price_string', 'wc_subscriptions_custom_price_string' );


add_action( 'wp_head', 'include_tracking_scripts', 20 );
function include_tracking_scripts() { 
	$ga_id = "UA-180725808-1";
	$facebook_tracking_id = "459044155157597";
?>
	<!-- Google Analytics Code -->
	<script async src = "https://www.googletagmanager.com/gtag/js?id=<?=$ga_id;?>"></script>
	<script>
		window.dataLayer = window.dataLayer || [];
		function gtag(){dataLayer.push(arguments);}
		gtag('js', new Date());
		gtag('config', '<?= $ga_id; ?>');
	</script>

	<!-- Facebook Pixel Code --> 
	<script>
		!function(f,b,e,v,n,t,s)
		{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
		n.callMethod.apply(n,arguments):n.queue.push(arguments)};
		if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
		n.queue=[];t=b.createElement(e);t.async=!0;
		t.src=v;s=b.getElementsByTagName(e)[0];
		s.parentNode.insertBefore(t,s)}(window, document,'script',
		'https://connect.facebook.net/en_US/fbevents.js');
		fbq('init', '<?=$facebook_tracking_id; ?>');
		fbq('track', 'PageView');
	</script> 
	<noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=<?=$facebook_tracking_id;?>&ev=PageView&noscript=1" /></noscript>
	<?php
}

//you can add custom functions below this line:
function nbids_additional_information($additional){
	return "Deal Requirements";
}
add_filter('woocommerce_product_additional_information_heading', 'nbids_additional_information');

function nbids_body_class($classes){
	global $post;
	if($post->post_type !== 'product'){
		return $classes;
	}
	$product=WC()->product_factory->get_product($post);
	
	if($product->get_type() === 'subscription'){
		$classes[]='simple-subscription';
	}
	if($product->get_type() === 'variable-subscription'){
		$classes[]='variable-subscription';
	}
	return $classes;
}
add_filter('body_class', 'nbids_body_class');


/*-----------------------------------------------------------------------------------*/
/* Don't add any code below here or the sky will fall down */
/*-----------------------------------------------------------------------------------*/

?>
