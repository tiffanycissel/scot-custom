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

// Disable Theme Editor
define( 'DISALLOW_FILE_EDIT', true );

// Register Sidebar(s)
add_action( 'widgets_init', 'scot_custom_sidebars' );
function scot_custom_sidebars(){
    register_sidebar(
        array(
            'id' => 'home-page-widget-area',
            'name' => __( 'Home Page Widget Area', 'twentytwentychild' ),
            'description' => __( 'A home page specific widget area', 'twentytwentychild' ),
            'before_widget' => '',
            'after_widget' => '',
            'before_title' => '',
            'after_title' => ''
        )
    );
}

// Create a custom sort for Board Member CPT
add_filter( 'pre_get_posts', 'scot_board_member_custom_sort' );
function scot_board_member_custom_sort( $query ){
    if( $query->is_main_query() && is_post_type_archive( 'scot_board_member' ) ){
        $meta_query_array = array(
            'relation' => 'AND',
            'rank_clause' => array(
                'key' => 'scot_board_member_role'
            ),
            'surname_clause' => array(
                'key' => 'scot_board_member_last_name'
            )
        );

        $orderby_array = array(
            'rank_clause' => 'ASC',
            'surname_clause' => 'ASC'
        );

        $query->set( 'meta_query', $meta_query_array );
        $query->set( 'orderby', $orderby_array );

        return $query;
    }
}

// Customize Newsletter CPT query
add_filter( 'pre_get_posts', 'scot_newsletter_custom_sort' );
function scot_newsletter_custom_sort( $query ){
    if( $query->is_main_query() && is_post_type_archive( 'scot_newsletter' ) ){
        $query->set( 'posts_per_page', -1 );

        return $query;
    }
}

/**
 * Displays the site logo, text and/or image.
 *
 * A custom, child theme version of the function
 * twentytwenty_site_logo (themes\twentytwenty\inc\template-tags.php:25) 
 * 
 */
function scotcustom_site_logo( $output = 'standard', $args = array(), $echo = true ) {
	$logo       = get_custom_logo();
	$site_title = get_bloginfo( 'name' );
	$contents   = '';
	$classname  = '';

	$defaults = array(
		'logo'        => '%1$s<span class="screen-reader-text">%2$s</span>',
		'logo_class'  => 'site-logo',
		'title'       => '<a href="%1$s">%2$s</a>',
		'title_class' => 'site-title',
		'home_wrap'   => '<h1 class="%1$s">%2$s</h1>',
		'single_wrap' => '<div class="%1$s faux-heading">%2$s</div>',
		'condition'   => ( is_front_page() || is_home() ) && ! is_page(),
        'both'        => '%1$s<span class="screen-reader-text">%2$s</span><a href="%3$s">%4$s</a>',
        'both_class'  => 'site-logo site-title'
	);

	$args = wp_parse_args( $args, $defaults );

	$args = apply_filters( 'twentytwenty_site_logo_args', $args, $defaults );

    if( $output === 'standard' ){
        if ( has_custom_logo() ) {
            $contents  = sprintf( $args['logo'], $logo, esc_html( $site_title ) );
            $classname = $args['logo_class'];
        } else {
            $contents  = sprintf( $args['title'], esc_url( get_home_url( null, '/' ) ), esc_html( $site_title ) );
            $classname = $args['title_class'];
        }
    } elseif( $output === 'logo' ){
        $contents  = sprintf( $args['logo'], $logo, esc_html( $site_title ) );
        $classname = $args['logo_class'];
    } elseif( $output === 'title' ){
        $contents  = sprintf( $args['title'], esc_url( get_home_url( null, '/' ) ), esc_html( $site_title ) );
        $classname = $args['title_class'];
    } elseif ($output === 'both' ){
        $contents  = sprintf( $args['both'], $logo, esc_html( $site_title ), esc_url( get_home_url( null, '/' ) ), esc_html( $site_title ) );
        $classname = $args['both_class'];
    }	

	$wrap = $args['condition'] ? 'home_wrap' : 'single_wrap';

	$html = sprintf( $args[ $wrap ], $classname, $contents );

	$html = apply_filters( 'twentytwenty_site_logo', $html, $args, $classname, $contents );

	if ( ! $echo ) {
		return $html;
	}

	echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

}

/**
 * Custom theme support for Scot Custom theme based on parent theme
 * 
 * See themes\twentytwenty\functions.php:36
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 *
 * @since Twenty Twenty 1.0
 */
function scot_custom_theme_support() {
    remove_theme_support( 'custom-logo' );

    // Custom logo.
	$logo_width  = 300;
	$logo_height = 84;
    
    // If the retina setting is active, double the recommended width and height.
	if ( get_theme_mod( 'retina_logo', false ) ) {
		$logo_width  = floor( $logo_width * 2 );
		$logo_height = floor( $logo_height * 2 );
	}

    add_theme_support(
		'custom-logo',
		array(
			'height'      => $logo_height,
			'width'       => $logo_width,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);    
}
add_action( 'after_setup_theme', 'scot_custom_theme_support', 11 );
