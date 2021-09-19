<?php

// Enqueue stylesheets
add_action( 'wp_enqueue_scripts', 'enqueue_scot_custom_styles' );
function enqueue_scot_custom_styles(){
    $parenthandle = 'twentytwenty-style'; // This is 'twentyfifteen-style' for the Twenty Fifteen theme.
    $theme = wp_get_theme();
    wp_enqueue_style( $parenthandle, get_template_directory_uri() . '/style.css', 
        array(),  // if the parent theme code has a dependency, copy it to here
        $theme->parent()->get('Version')
    );
    wp_enqueue_style( 'child-style', get_stylesheet_uri(),
        array( $parenthandle ),
        $theme->get('Version') // this only works if you have Version in the style header
    );
}

// Add theme-specific class to body
add_filter( 'body_class', function( $classes ){ return array_merge( $classes, array( 'scot-custom-theme' ) ); } );