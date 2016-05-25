<?php

// Insert your Customization Functions. Read More - http://codex.wordpress.org/Child_Themes

add_action( 'wp_enqueue_scripts', 'ninetysix_child_styles', 20 );
function ninetysix_child_styles() {
    wp_enqueue_style( 'ninetysix-parent-style', get_template_directory_uri() . '/style.css' );
}

?>