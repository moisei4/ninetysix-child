<?php

// Insert your Customization Functions. Read More - http://codex.wordpress.org/Child_Themes

add_action( 'wp_enqueue_scripts', 'ninetysix_child_styles', 25 );
function ninetysix_child_styles() {
    wp_enqueue_style( 'ninetysix-parent-style', get_template_directory_uri() . '/style.css' );
	
	wp_enqueue_script('gentle', get_stylesheet_directory_uri() . '/assets/js/gentle.js', array( 'jquery' ), '1.0', true);
}

add_action( 'wp_enqueue_scripts', 'ninetysix_gentle_styles', 50 );
function ninetysix_gentle_styles() {
    wp_enqueue_style( 'ninetysix-child-style', get_stylesheet_directory_uri() . '/gentle.css' );
}

add_image_size('waves_archive_product', 300, 450, true); 
?>