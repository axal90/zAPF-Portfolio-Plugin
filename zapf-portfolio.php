<?php

/*

Plugin Name: zAPF Portfolio Plugin

Plugin URI: http://zapf.se

Version: 1.0

Author: zAPF

Author URI: http://zapf.se

*/

/**
 * Constants
 */
if ( ! defined( 'ZAPF_BASE_FILE' ) )
    define( 'ZAPF_BASE_FILE', __FILE__ );
if ( ! defined( 'ZAPF_BASE_DIR' ) )
    define( 'ZAPF_BASE_DIR', dirname( ZAPF_BASE_FILE ) );
if ( ! defined( 'ZAPF_PLUGIN_URL' ) )
    define( 'ZAPF_PLUGIN_URL', plugin_dir_url( __FILE__ ) );


/**
 * Register Custom Post Type zapf-portfolio-item
 */
function zapf_portfolio_init() {
	$labels = array(
	    'name'               => 'Portfolio Item',
	    'singular_name'      => 'Portfolio Item',
	    'add_new'            => 'Add New',
	    'add_new_item'       => 'Add New Portfolio Item',
	    'edit_item'          => 'Edit Portfolio Item',
	    'new_item'           => 'New Portfolio Item',
	    'all_items'          => 'All Portfolio Items',
	    'view_item'          => 'View Portfolio Item',
	    'search_items'       => 'Search Portfolio Items',
	    'not_found'          => 'No portfolio items found',
	    'not_found_in_trash' => 'No portfolio items found in Trash',
	    'parent_item_colon'  => '',
	    'menu_name'          => 'Portfolio'
	);

	$args = array(
	    'labels'             => $labels,
	    'public'             => true,
	    'publicly_queryable' => true,
	    'show_ui'            => true,
	    'show_in_menu'       => true,
	    'query_var'          => true,
	    'capability_type'    => 'post',
	    'has_archive'        => false,
	    'hierarchical'       => false,
	    'menu_position'      => 5,
	    'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', )
	);

	register_post_type( 'zapf-portfolio-item', $args );
}
add_action( 'init', 'zapf_portfolio_init' );



/**
 * flush rewrite rules on plugin activation
 * @since 1.0
 */
function zapf_rewrite_flush() {

    zapf_portfolio_init();

    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'zapf_rewrite_flush' );


/**
* Returns template file
*
* @since 1.0
*/
function zapf_template_chooser( $template ) {
 
    // Post ID
    $post_id = get_the_ID();
 
    // For all other CPT
    if ( get_post_type( $post_id ) != 'zapf-portfolio-item' ) {
        return $template;
    }
 
    // Else use custom template
    if ( is_single() ) {
        return zapf_get_template_hierarchy( 'single' );
    }
 
}
add_filter( 'template_include', 'zapf_template_chooser');


/**
* Get the custom template if is set
*
* @since 1.0
*/
 
function zapf_get_template_hierarchy( $template ) {
 
    // Get the template slug
    $template_slug = rtrim( $template, '.php' );
    $template = $template_slug . '.php';
 
    // Check if a custom template exists in the theme folder, if not, load the plugin template file
    if ( $theme_file = locate_template( array( 'plugin_template/' . $template ) ) ) {
        $file = $theme_file;
    }
    else {
        $file = ZAPF_BASE_DIR . '/templates/' . $template;
    }
 
    return apply_filters( 'zapf_repl_template_' . $template, $file );
}
 



/**
 * [zapf-portfolio]
 * @param  array $atts  	possible values
 *                     		sort_items = WP_Query orderby
 *                       	open_link = link target
 *                        	startfx = normal, transparent, overlay, grayscale
 *                         	hoverfx = normal, popout, sliceDown, sliceDownLeft, sliceUp, sliceUpLeft, sliceUpRandom, sliceUpDown, sliceUpDownLeft, fold, foldLeft, boxRandom, boxRain, boxRainReverse, boxRainGrow, boxRainGrowReverse
 *                         	thumbnail_size = thumnail, medium, large, full, custom-thumbnail size
 *         
 * @return string      all profile items
 * @since 1.0
 */
function zapf_portfolio_shortcode( $atts ) {

	global  $startfx, $hoverfx;

	extract( shortcode_atts( array(
		'sort_items' => 'date',
		'open_link' => 'blank',
		'startfx' => 'normal',
		'hoverfx' => 'normal',
		'thumbnail_size' => 'thumnail',
	), $atts ) );



	//Registering Scripts & Styles

	wp_register_script('adipoli', plugins_url( 'zAPF-Portfolio-Plugin/js/adipoli.js'), array( 'jquery' ), false, true);
	wp_register_style('zapf-portfolio-style',  plugins_url( 'zAPF-Portfolio-Plugin/css/style.css' ));
	wp_register_style('adipoli-style', plugins_url( 'zAPF-Portfolio-Plugin/css/adipoli.css'));


	//Enqeueing Scripts & Styles

	wp_enqueue_script('jquery');
	wp_enqueue_script('thickbox');
	wp_enqueue_script('adipoli');

	wp_enqueue_style('thickbox');
	wp_enqueue_style('adipoli-style');
	wp_enqueue_style('zapf-portfolio-style');


	//Adding js to footer

	function zapf_portfolio_footer_js() {
		global  $startfx, $hoverfx;
		echo '
			<script type="text/javascript">
				jQuery(document).ready(function($) {

					$(".img-hover").adipoli({
						"startEffect" : "' . $startfx . '",
						"hoverEffect" : "' . $hoverfx . '"
					});

				});
			</script>
		';
	}
	
	//Start Output
	ob_start(); ?>

	<div class="clearfix"></div>

	<div class="post-list">

		<?php 
		$portfolio_items = new WP_Query( array(
        	'post_type' 		=> 'zapf-portfolio-item',
			'posts_per_page'	=> '0',
       		'orderby' => $sort_items,
   		) );
    	if ( $portfolio_items->have_posts() ) { while ( $portfolio_items->have_posts() ) : $portfolio_items->the_post(); ?>

        	<article class="zapf-portfolio-single-item">


				<header class="entry-header">

					<a class="thickbox" href="<?php the_permalink(); ?>?TB_iframe=true&width=600&height=550">

						<?php the_post_thumbnail( $thumbnail_size, array('class' => 'img-hover') ); ?>

					</a>
					
					<a class="thickbox title-box" href="<?php the_permalink(); ?>?TB_iframe=true&width=600&height=550"><h1 class="entry-title"><?php the_title(); ?></h1></a>
					
				</header>

			</article>

		<?php endwhile; }
		wp_reset_postdata(); ?>

	</div>
    <div class="clearfix"></div>

<?php 
	add_action('wp_footer', 'zapf_portfolio_footer_js');
	return ob_get_clean();


	

	
}
add_shortcode( 'zapf-portfolio', 'zapf_portfolio_shortcode' );



?>