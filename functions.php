<?php
function divi__child_theme_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );


	wp_enqueue_script('neighborhood-bids-js', 
		get_stylesheet_directory_uri() . '/dist/child-scripts.frontend.bundle.min.js', 
		array('jquery'), 
		filemtime(get_stylesheet_directory() . '/dist/child-scripts.frontend.bundle.min.js'), 
		true
	);

}
add_action( 'wp_enqueue_scripts', 'divi__child_theme_enqueue_styles' );
 
 
//you can add custom functions below this line:
function nbids_additional_information($additional){
	return "Deal Requirements";
}
add_filter('woocommerce_product_additional_information_heading', 'nbids_additional_information');
