<?php
/**
 * Neighborhood Bids Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package neighborhood-bids
 */

add_action( 'wp_enqueue_scripts', 'astra_parent_theme_enqueue_styles' );

/**
 * Enqueue scripts and styles.
 */
function astra_parent_theme_enqueue_styles() {
	wp_enqueue_style( 'astra-style', get_template_directory_uri() . '/style.css' );
	wp_enqueue_style( 'neighborhood-bids-style',
		get_stylesheet_directory_uri() . '/style.css',
		array( 'astra-style' )
	);

	wp_enqueue_script('neighborhood-bids-js', 
		get_stylesheet_directory_uri() . '/dist/child-scripts.frontend.bundle.min.js', 
		array('jquery'), 
		filemtime(get_stylesheet_directory() . '/dist/child-scripts.frontend.bundle.min.js'), 
		true
	);
}
