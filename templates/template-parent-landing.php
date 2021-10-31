<?php
/**
 * Template Name: Parent Landing Page Template
 * Template Post Type: page
 *
 * @package WordPress
 * @subpackage Twenty_Twenty
 * @since Twenty Twenty 1.0
 */

// get_template_part( 'singular' );

function getCurrentPageMenuId( $menuName ){
    $theMenu = wp_get_nav_menu_items( $menuName );

    $currentPageMenuItem = array_filter( $theMenu, function($val){
        return $val->object_id  == get_the_ID();
    });

    $currentPageMenuId =  $currentPageMenuItem[array_keys($currentPageMenuItem)[0]]->ID;

    return $currentPageMenuId;
}

function get_child_links(){
    $childLinks = array();

    $childPages = get_pages(array(
        'parent' => get_the_ID(),
    ));

    foreach( $childPages as $cp ){
        $childLinks[$cp->post_title] = get_permalink( $cp );
    }

    $mainMenu = wp_get_nav_menu_items('NewMainMenu');
    
    $currentMenuItemChildren = array_filter( $mainMenu, function($val){
        $currentPageMenuId = getCurrentPageMenuId( 'NewMainMenu' );
        return $val->menu_item_parent == $currentPageMenuId;
    });

    foreach( $currentMenuItemChildren as $menuChild ){
        if( $menuChild->type_label === 'Page' || $menuChild->type_label === 'Post'){
            if( !isset($childLinks[$menuChild->title] ) ){
                $childLinks[$menuChild->title] = get_permalink( $menuChild->object_id );
            }
        }
    }

    $archive_links = get_field( 'post_type_archive' );

    if( $archive_links ){
        foreach( $archive_links as $archive_link ){
            if( $archive_link === 'post' ){
                $childLinks['Post Archive'] = get_post_type_archive_link( 'post' );
            } elseif( $archive_link === 'scot_board_member' ){
                $childLinks['Meet the Board of Directors'] = get_post_type_archive_link( 'scot_board_member' );
            } elseif( $archive_link === 'scot_newsletter' ){
                $childLinks['Great SCOT Newsletter'] = get_post_type_archive_link( 'scot_newsletter' );
            }
        }    
    }
    
    $uniqueChildLinks = array_unique($childLinks);
    
    ksort( $uniqueChildLinks );

    return $uniqueChildLinks;
}

get_header(); ?>
<main id="site-content" role="main">
    <article <?php post_class(); ?> id="post-<?php the_ID(); ?>">

    <?php

	get_template_part( 'template-parts/entry-header' );

	if ( ! is_search() ) {
		get_template_part( 'template-parts/featured-image' );
	}

	?>

        <div class="post-inner <?php echo is_page_template( 'templates/template-full-width.php' ) ? '' : 'thin'; ?> ">

            <div class="entry-content">

                <?php the_content( __( 'Continue reading', 'twentytwenty' ) );                
                

                $childLinks = get_child_links();
                if( $childLinks ){ ?>
                <h2>More Information</h2>
                <ul>
                <?php
                    foreach( $childLinks as $name=>$link ): ?>
                        <li><a href="<?php echo $link; ?>"><?php echo $name; ?></a></li>
                    <?php endforeach;?>
                    </ul>
                <?php } ?>

            </div><!-- .entry-content -->

        </div><!-- .post-inner -->

    </article>
</main><!-- #site-content -->
<?php get_footer();