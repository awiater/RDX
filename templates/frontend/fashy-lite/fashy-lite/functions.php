<?php
add_action( 'after_setup_theme', 'fashy_theme_setup' );
function fashy_theme_setup() {
	/*woocommerce support*/
	add_theme_support( 'post-formats', array( 'link', 'gallery', 'video' , 'audio', 'quote') );
	/*feed support*/
	add_theme_support( 'automatic-feed-links' );
	/*post thumb support*/
	add_theme_support( 'post-thumbnails' ); // this enable thumbnails and stuffs
	/*title*/
	add_theme_support( 'title-tag' );
	/*lang*/
	load_theme_textdomain( 'fashy', get_template_directory() . '/lang' );
	/*setting thumb size*/
	add_image_size( 'fashy-gallery', 120,80, true ); 
	add_image_size( 'fashy-widget', 255,170, true );
	add_image_size( 'fashy-postBlock', 1160, 770, true );
	add_image_size( 'fashy-related', 345,230, true );
	add_image_size( 'fashy-postGridBlock', 590,390, true );
	add_image_size( 'fashy-postGridBlock-2', 590,437, true );	

	require( get_template_directory() . '/updater/theme-updater.php' );	
	
	register_nav_menus(array(
	
			'fashy_mainmenu' => esc_html__('Main Menu','fashy'),
			'fashy_respmenu' => esc_html__('Responsive Menu','fashy'),	
			'fashy_scrollmenu' => esc_html__('Scroll Menu','fashy'),	
			
	));	
		
		
    register_sidebar(array(
        'id' => 'fashy_sidebar',
        'name' => esc_html__('Sidebar main','fashy'),
        'before_widget' => '<div class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3>',
        'after_title' => '</h3><div class="widget-line"></div>'
    ));	

    register_sidebar(array(
        'id' => 'dremscape-sidebar-under-header-left',
        'name' => esc_html__('Sidebar under header left','fashy'),
        'before_widget' => '<div class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3>',
        'after_title' => '</h3><div class="widget-line"></div>'
    ));		
		
    register_sidebar(array(
        'id' => 'fashy-sidebar-under-header-right',
        'name' => esc_html__('Sidebar under header right','fashy'),
        'before_widget' => '<div class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3>',
        'after_title' => '</h3><div class="widget-line"></div>'
    ));	
	
    register_sidebar(array(
        'id' => 'fashy-sidebar-under-header-fullwidth',
        'name' => esc_html__('Sidebar under header full width','fashy'),
        'before_widget' => '<div class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3>',
        'after_title' => '</h3><div class="widget-line"></div>'
    ));		
	
	
    register_sidebar(array(
        'id' => 'fashy-sidebar-footer-fullwidth',
        'name' => esc_html__('Sidebar above footer full width','fashy'),
        'before_widget' => '<div class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3>',
        'after_title' => '</h3><div class="widget-line"></div>'
    ));	
	
    register_sidebar(array(
        'id' => 'fashy-sidebar-footer-left',
        'name' => esc_html__('Sidebar above footer left','fashy'),
        'before_widget' => '<div class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3>',
        'after_title' => '</h3><div class="widget-line"></div>'
    ));		
		
    register_sidebar(array(
        'id' => 'fashy-sidebar-footer-right',
        'name' => esc_html__('Sidebar above footer right','fashy'),
        'before_widget' => '<div class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3>',
        'after_title' => '</h3><div class="widget-line"></div>'
    ));			

    register_sidebar(array(
        'id' => 'fashy_sidebar-top-left',
        'name' => esc_html__('Top sidebar left','fashy'),
        'before_widget' => '<div class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3>',
        'after_title' => '</h3>'
    ));		  

    register_sidebar(array(
        'id' => 'fashy_sidebar-top-right',
        'name' => esc_html__('Top sidebar right','fashy'),
        'before_widget' => '<div class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3>',
        'after_title' => '</h3>'
    ));		
		
 
    register_sidebar(array(
        'id' => 'fashy_sidebar-logo',
        'name' => esc_html__('Sidebar for advert in logo area','fashy'),
        'before_widget' => '<div class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3>',
        'after_title' => '</h3>'
    ));		
		
    register_sidebar(array(
        'id' => 'fashy_footer1',
        'name' => esc_html__('Footer sidebar 1','fashy'),
        'before_widget' => '<div class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3>',
        'after_title' => '</h3>'
    ));
    
    register_sidebar(array(
        'id' => 'fashy_footer2',
        'name' => esc_html__('Footer sidebar 2','fashy'),
        'before_widget' => '<div class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3>',
        'after_title' => '</h3>'
    ));
	
    
    register_sidebar(array(
        'id' => 'fashy_footer3',
        'name' => esc_html__('Footer sidebar 3','fashy'),
        'before_widget' => '<div class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3>',
        'after_title' => '</h3>'
    ));
    
	
	
	// Responsive walker menu
	class fashy_Walker_Responsive_Menu extends Walker_Nav_Menu {
		
		function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
			global $wp_query;		
			$item_output = $attributes = $prepend ='';
			$class_names = $value = '';
			$classes = empty( $item->classes ) ? array() : (array) $item->classes;
			$class_names = join( ' ', apply_filters( '', array_filter( $classes ), $item ) );			
			$class_names = ' class="'. esc_attr( $class_names ) . '"';			   
			// Create a visual indent in the list if we have a child item.
			$visual_indent = ( $depth ) ? str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-circle"></i>', $depth) : '';
			// Load the item URL
			$attributes .= ! empty( $item->url ) ? ' href="'   . esc_attr( $item->url ) .'"' : '';
			// If we have hierarchy for the item, add the indent, if not, leave it out.
			// Loop through and output each menu item as this.
			if($depth != 0) {
				$item_output .= '<a '. $class_names . $attributes .'>&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-circle"></i>' . $item->title. '</a><br>';
			} else {
				$item_output .= '<a ' . $class_names . $attributes .'><strong>'.$prepend.$item->title.'</strong></a><br>';
			}
			// Make the output happen.
			$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
		}
	}
	
	
	// Main walker menu	
	class fashy_Walker_Main_Menu extends Walker_Nav_Menu
	{		
		function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
		   $this->curItem = $item;
		   global $wp_query;
		   $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
		   $class_names = $value = '';
		   $classes = empty( $item->classes ) ? array() : (array) $item->classes;
		   $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
		   $class_names = ' class="'. esc_attr( $class_names ) . '"';
		   $image  = ! empty( $item->custom )     ? ' <img src="'.esc_attr($item->custom).'">' : '';
		   $output .= $indent . '<li id="menu-item-'.rand(0,9999).'-'. $item->ID . '"' . $value . $class_names .'>';
		   $attributes_title  = ! empty( $item->attr_title ) ? ' <i class="fa '  . esc_attr( $item->attr_title ) .'"></i>' : '';
		   $attributes  = ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
		   $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
		   $attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
		   $prepend = '';
		   $append = '';
		   if($depth != 0)
		   {
				$append = $prepend = '';
		   }
			$item_output = $args->before;
			$item_output .= '<a '. $attributes .'>';
			$item_output .= $attributes_title.$args->link_before .$prepend.apply_filters( 'the_title', $item->title, $item->ID ).$append;
			$item_output .= $args->link_after;
			$item_output .= '</a>';	
			$item_output .= $args->after;
			$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
		}
	}
	
	

}

define('BOX_PATH', get_template_directory() . '/includes/boxes/');
define('OPTIONS', 'of_options_pmc'); // Name of the database row where your options are stored
/*theme options*/
require( get_template_directory()  . '/option-tree/assets/theme-mode/functions.php' );

require_once (get_template_directory() . '/option-tree/import/plugins/options-importer.php');   // Options panel settings and custom settings
add_option('IMPORT_FASHY_OPTION_4', 'false');
add_option('IMPORT_OLD_OPTIONS', 'false');


if (is_admin() && isset($_GET['activated'] ) && $pagenow == "themes.php" ) {
	//Call action that sets
	if(get_option('IMPORT_FASHY_OPTION_4') == 'false'){
		import(get_template_directory() . '/option-tree/import/options.json');
		fashy_options('default-layout-sidebar');
		update_option('IMPORT_FASHY_OPTION_4', 'true');
		update_option('IMPORT_OLD_OPTIONS', 'true' );
		wp_redirect(  esc_url_raw(admin_url( 'themes.php?page=ot-theme-options#section_import' )) );
	}
	else{
		wp_redirect(  esc_url_raw(admin_url( 'themes.php?page=ot-theme-options' )) );
	}
}

// Build Options

$includes =  get_template_directory() . '/includes/';
$widget_includes =  get_template_directory() . '/includes/widgets/';
/* include custom widgets */
require_once ($widget_includes . 'recent_post_widget.php'); 
require_once ($widget_includes . 'popular_post_widget.php');
require_once ($widget_includes . 'social_widget.php');
require_once ($widget_includes . 'post_widget.php');
require_once ($widget_includes . 'post_slider_widget.php');
require_once ($widget_includes . 'video_widget.php');
/* include scripts */
function fashy_scripts() {
	/*scripts*/
	wp_enqueue_script('fitvideos', get_template_directory_uri() . '/js/jquery.fitvids.js', array('jquery'),true,false);	
	wp_enqueue_script('scrollto', get_template_directory_uri() . '/js/jquery.scrollTo.js', array('jquery'),true,true);	
	wp_enqueue_script('fashy_customjs', get_template_directory_uri() . '/js/custom.js', array('jquery'),true,true);  	     
	wp_enqueue_script('easing', get_template_directory_uri() . '/js/jquery.easing.1.3.js', array('jquery'),true,true);
	wp_enqueue_script('cycle', get_template_directory_uri() . '/js/jquery.cycle.all.min.js', array('jquery'),true,true);		
	wp_register_script('news', get_template_directory_uri() . '/js/jquery.li-scroller.1.0.js', array('jquery'),true,true);  
	wp_enqueue_script('gistfile', get_template_directory_uri() . '/js/gistfile_pmc.js', array('jquery') ,true,true);  
	wp_enqueue_script('bxSlider', get_template_directory_uri() . '/js/jquery.bxslider.js', array('jquery') ,true,false);  	
	wp_enqueue_script('isotope', get_template_directory_uri() . '/js/jquery.isotope.min.js', array('jquery') ,true,true);  
	wp_enqueue_script('infinity', get_template_directory_uri() . '/js/pmc_infinity.js', array('jquery') ,true,false);
	wp_enqueue_script('retinaimages', get_template_directory_uri() . '/js/retina.min.js', array('jquery'),true,true);	  	
	$share_options = ot_get_option( 'single_display_share_select' );
	if(!empty($share_options[0])){	
		wp_enqueue_script('addthis', 'https://s7.addthis.com/js/300/addthis_widget.js', array('jquery') ,true,false); 
	}
	if ( is_singular() && get_option( 'thread_comments' ) ) {wp_enqueue_script( 'comment-reply' ); }
	wp_enqueue_script('jquery-ui-tabs');
	/*style*/
	
	wp_enqueue_style( 'fashy-style', get_template_directory_uri() . '/style.css' );

	$css_dir = get_template_directory() . '/css/'; // Shorten code, save 1 call
	ob_start(); // Capture all output (output buffering)
	require($css_dir . 'style_options.php'); // Generate CSS
	$css = ob_get_clean(); // Get generated CSS (output buffering)
    wp_add_inline_style( 'fashy-style', $css );

	wp_enqueue_script('font-awesome_pms', 'https://use.fontawesome.com/30ede005b9.js' , '',null);				
}
add_action( 'wp_enqueue_scripts', 'fashy_scripts' );
require_once ($includes . 'class-tgm-plugin-activation.php');

/*shorcode to excerpt*/
remove_filter( 'get_the_excerpt', 'wp_trim_excerpt'  ); //Remove the filter we don't want
add_filter( 'get_the_excerpt', 'fashy_wp_trim_excerpt' ); //Add the modified filter
add_filter( 'the_excerpt', 'do_shortcode' ); //Make sure shortcodes get processed


function fashy_wp_trim_excerpt($text = '') {
	$raw_excerpt = $text;
	if ( '' == $text ) {
		$text = get_the_content('');
		//$text = strip_shortcodes( $text ); //Comment out the part we don't want
		$text = apply_filters('the_content', $text);
		$text = str_replace(']]>', ']]&gt;', $text);
		$excerpt_length = apply_filters('excerpt_length', 900);
		$excerpt_more = apply_filters('excerpt_more', ' ' . '[...]');
		$text = wp_trim_words( $text, $excerpt_length, $excerpt_more );
	}
	return apply_filters('wp_trim_excerpt', $text, $raw_excerpt);
}

/*add boxed to body class*/

add_filter('body_class','fashy_body_class');

function fashy_body_class($classes) {
	$fashy_data = get_option(OPTIONS);
	$class = '';
	if(!empty($fashy_data['use_boxed'])){
		$classes[] = 'fashy_boxed';
	}
	return $classes;
}

/* custom breadcrumb */
function fashy_breadcrumb($title = false) {
	$fashy_data = get_option(OPTIONS);
	$breadcrumb = '';
	if (!is_home()) {
		if($title == false){
			$breadcrumb .= '<a href="';
			$breadcrumb .=  esc_url(home_url('/'));
			$breadcrumb .=  '">';
			$breadcrumb .= esc_html__('Home', 'fashy');
			$breadcrumb .=  "</a> &#187; ";
		}
		if (is_single()) {
			if (is_single()) {
				$name = '';
				if($title == false){
					$breadcrumb .= $name .' &#187; <span>'. get_the_title().'</span>';
				}
				else{
					$breadcrumb .= get_the_title();
				}
			}	
		} elseif (is_page()) {
			$breadcrumb .=  '<span>'.get_the_title().'</span>';
		}
		else if(is_tag()){
			$tag = get_query_var('tag');
			$tag = str_replace('-',' ',$tag);
			$breadcrumb .=  '<span>'.$tag.'</span>';
		}
		else if(is_search()){
			$breadcrumb .= esc_html__('Search results for ', 'fashy') .'"<span>'.get_search_query().'</span>"';			
		} 
		else if(is_category()){
			$cat = get_query_var('cat');
			$cat = get_category($cat);
			$breadcrumb .=  '<span>'.$cat->name.'</span>';
		}
		else if(is_archive()){
			$breadcrumb .=  '<span>'.esc_html__('Archive','fashy').'</span>';
		}	
		else{
			$breadcrumb .=  esc_html__('Home','fashy');
		}

	}
	return $breadcrumb ;
}
/* social share links */
function fashy_socialLinkSingle($link,$title) {
	$social = '';
	$social  .= '<div class="addthis_toolbox">';
	$social .= '<div class="custom_images">';
	$share_options = ot_get_option( 'single_display_share_select' );
	if(!empty($share_options[0])){
	$social .= '<a class="addthis_button_facebook" addthis:url="'.esc_url($link).'" addthis:title="'.esc_attr($title).'" ><i class="fa fa-facebook"></i></a>';
	}
	if(!empty($share_options[1])){
	$social .= '<a class="addthis_button_twitter" addthis:url="'.esc_url($link).'" addthis:title="'.esc_attr($title).'"><i class="fa fa-twitter"></i></a>';  
	}
	if(!empty($share_options[2])){
	$social .= '<a class="addthis_button_pinterest_share" addthis:url="'.esc_url($link).'" addthis:title="'.esc_attr($title).'"><i class="fa fa-pinterest"></i></a>'; 
	}
	if(!empty($share_options[3])){
	$social .= '<a class="addthis_button_google_plusone_share" addthis:url="'.esc_url($link).'" g:plusone:count="false" addthis:title="'.esc_attr($title).'"><i class="fa fa-google-plus"></i></a>'; 	
	}
	if(!empty($share_options[4])){
	$social .= '<a class="addthis_button_stumbleupon" addthis:url="'.esc_url($link).'" addthis:title="'.esc_attr($title).'"><i class="fa fa-stumbleupon"></i></a>';
	}
	if(!empty($share_options[5])){
	$social .= '<a class="addthis_button_vk" addthis:url="'.esc_url($link).'" addthis:title="'.esc_attr($title).'"><i class="fa fa-vk"></i></a>';
	}	
	if(!empty($share_options[6])){
	$social .= '<a class="addthis_button_whatsapp" addthis:url="'.esc_url($link).'" addthis:title="'.esc_attr($title).'"><i class="fa fa-whatsapp"></i></a>';
	}	
	$social .='</div>';	
	$social .= '</div>'; 
	echo $social;
	
	
}
/* links to social profile */
function fashy_socialLink() {
	$social = '';
	$fashy_data = get_option(OPTIONS); 
	$icons = $fashy_data['socialicons'];
	if(is_array($icons)){
		foreach ($icons as $icon){
			$social .= '<a target="_blank"  href="'.esc_url($icon['link']).'" title="'.esc_attr($icon['title']).'"><i class="fa '.esc_attr($icon['url']).'"></i></a>';	
		}
	}
	echo $social;
}

/* remove double // char */
function fashy_stripText($string) 
{ 
    return str_replace("\\",'',$string);
} 
	
/* custom post types */	
add_action('save_post', 'fashy_update_post_type');
add_action("admin_init", "fashy_add_meta_box");



function fashy_add_meta_box(){
	add_meta_box("fashy_post_type", "Fashy options", "fashy_post_type", "post", "normal", "high");	
	
}	



function fashy_post_type(){
	global $post;
	$fashy_data = get_post_custom(get_the_id());

	if (isset($fashy_data["video_post_url"][0])){
		$video_post_url = $fashy_data["video_post_url"][0];
	}else{
		$video_post_url = "";
	}	
	
	if (isset($fashy_data["link_post_url"][0])){
		$link_post_url = $fashy_data["link_post_url"][0];
	}else{
		$link_post_url = "";
	}	
	
	if (isset($fashy_data["audio_post_url"][0])){
		$audio_post_url = $fashy_data["audio_post_url"][0];
	}else{
		$audio_post_url = "";
	}


?>
    <div id="portfolio-category-options">
        <table cellpadding="15" cellspacing="15">		
            <tr class="videoonly" style="border-bottom:1px solid #000;">
            	<td><label><?php esc_attr_e('Video URL(*required) - add if you select video post:','fashy'); ?> <i style="color: #999999;"></i></label><br><input name="video_post_url" value="<?php echo esc_attr($video_post_url); ?>" /> </td>	
			</tr>		
            <tr class="linkonly" >
            	<td><label><?php esc_attr_e('Link URL - add if you select link post:','fashy'); ?> <i style="color: #999999;"></i></label><br><input name="link_post_url"  value="<?php echo esc_attr($link_post_url); ?>" /></td>
            </tr>				
            <tr class="audioonly">
            	<td><label><?php esc_attr_e('Audio URL - add if you select audio post:','fashy'); ?> <i style="color: #999999;"></i></label><br><input name="audio_post_url"  value="<?php echo esc_attr($audio_post_url); ?>" /></td>
            </tr>	
            <tr class="nooptions">
            	<td><?php esc_attr_e('No options for this post type.','fashy'); ?></td>
            </tr>				
        </table>
    </div>
      
<?php
	
}


function fashy_update_post_type(){
	global $post;
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return $post_id;
    }
	if($post){

		if( isset($_POST["video_post_url"]) ) {
			update_post_meta($post->ID, "video_post_url", $_POST["video_post_url"]);
		}		
		if( isset($_POST["link_post_url"]) ) {
			update_post_meta($post->ID, "link_post_url", $_POST["link_post_url"]);
		}	
		if( isset($_POST["audio_post_url"]) ) {
			update_post_meta($post->ID, "audio_post_url", $_POST["audio_post_url"]);
		}							
		
	}
	
	
	
}
if( !function_exists( 'fashy_fallback_menu' ) )
{

	function fashy_fallback_menu()
	{
		$current = "";
		if (is_front_page()){$current = "class='current-menu-item'";} 
		echo "<div class='fallback_menu'>";
		echo "<ul class='Fashy_fallback menu'>";
		echo "<li $current><a href='".esc_url(esc_url(home_url('/')))."'>".esc_attr__('Home','fashy')."</a></li>";
		wp_list_pages('title_li=&sort_column=menu_order');
		echo "</ul></div>";
	}
}

add_filter( 'the_category', 'fashy_add_nofollow_cat' );  

function fashy_add_nofollow_cat( $text ) { 
	$text = str_replace('rel="category tag"', "", $text); 
	return $text; 
}

/* get image from post */
function fashy_getImage($id, $image){
	$return = '';
	if ( has_post_thumbnail($id) ){
		$return = get_the_post_thumbnail($id,$image);
		}
	else
		$return = '';
	
	return 	$return;
}

if ( ! isset( $content_width ) ) $content_width = 800;


function fashy_add_this_script_footer(){ 

	$fashy_script = '	
		"use strict"; 
		jQuery(document).ready(function($){	
			jQuery(".searchform #s").attr("value","'. esc_html__("Search and hit enter...","fashy").'");	
			jQuery(".searchform #s").focus(function() {
				jQuery(".searchform #s").val("");
			});
			
			jQuery(".searchform #s").focusout(function() {
				if(jQuery(".searchform #s").attr("value") == "")
					jQuery(".searchform #s").attr("value","'. esc_html__("Search and hit enter...","fashy") .'");
			});		
				
		});	
		
		';
	wp_add_inline_script( 'fashy_customjs', $fashy_script );
}

add_action( 'wp_enqueue_scripts', 'fashy_add_this_script_footer' );

function fashy_security($string){
	echo stripslashes(wp_kses(stripslashes($string),array('img' => array('src' => array(),'alt'=>array()),'a' => array('href' => array()),'span' => array(),'div' => array('class' => array()),'b' => array(),'strong' => array(),'br' => array(),'p' => array()))); 

}

/* SEARCH FORM */
function fashy_search_form( $form ) {
	$form = '<form method="get" id="searchform" class="searchform" action="' . home_url( '/' ) . '" >
	<input type="text" value="' . get_search_query() . '" name="s" id="s" />
	<i class="fa fa-search search-desktop"></i>
	</form>';

	return $form;
}
add_filter( 'get_search_form', 'fashy_search_form' );



	add_action('save_post', 'fashy_update_post_rev');
	add_action("admin_init", "fashy_add_rev");
	
	function fashy_add_rev(){
	
	$screens = array( 'page' );

	foreach ( $screens as $screen ) {

		add_meta_box(
			"fashy_post_content", esc_html('Fashy Options','fashy'), "fashy_post_content",
			$screen,'side','high'
		);
	}	
		
		
	}	
	


	
	function fashy_post_content(){	
		global $post;	
		$fashy_data = get_post_custom(get_the_id());
		if (isset($fashy_data["custom_post_rev"][0])){		
			$custom_post_rev = $fashy_data["custom_post_rev"][0];	
		}else{		
			$custom_post_rev = "";	
		}		
		?>	
         <table cellpadding="15" cellspacing="0">	

			<tr>
			<td><label><?php esc_html__('Select custom revolution slider:','fashy'); ?> </label>				
			<br>	
				<?php if(shortcode_exists( 'rev_slider')) {  ?>
				<select id="custom_post_rev"  name="custom_post_rev">	
				<option value="empty" <?php if($custom_post_rev == 'empty') echo 'selected'; ?>>Empty</option>	
				<?php 				
				$slider = new RevSlider();				
				$arrSliders = $slider->getArrSliders();				
				if(!empty($arrSliders)){ 	
					$revSliderArray = array();					
					foreach($arrSliders as $sliders){ ?>
						<option value="<?php echo esc_attr($sliders->getAlias()); ?>" <?php if($sliders->getAlias() == $custom_post_rev) echo 'selected'; ?>>
						<?php echo esc_attr($sliders->getShowTitle()) ?>
						</option>						
					<?php
					} 						
				}																
				?>

				<?php } ?>
			</td>            
			</tr>		
		</table>  
		
	<?php	
	}
	
	function fashy_update_post_rev()
	{
	global $post;
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return $post_id;
    }
	if($post){

		if( isset($_POST["custom_post_rev"]) ) {
			update_post_meta($post->ID, "custom_post_rev", $_POST["custom_post_rev"]);
		}		
	}
	}

function fashy_excerpt_more( $more ) {
    return '...';
}
add_filter( 'excerpt_more', 'fashy_excerpt_more' );


add_filter( 'the_content_more_link', 'fashy_modify_read_more_link' );
function fashy_modify_read_more_link() {
	return '<div class="fashy-read-more"><a class="more-link" href="' . get_permalink() . '">'.esc_html__('Continue reading','fashy').'</a></div>';
}
/*set excerpt lenght for grid layout*/
if(!function_exists('fashy_custom_excerpt_length')){
	function fashy_custom_excerpt_length( $length ) {
		return 999;
	}
	add_filter( 'excerpt_length', 'fashy_custom_excerpt_length', 999 );
}

add_filter('dynamic_sidebar_params','fashy_blog_widgets');
 
/* Register our callback function */
function fashy_blog_widgets($params) {	 
 
     global $blog_widget_num; //Our widget counter variable
 
     //Check if we are displaying "Footer Sidebar"
      if(isset($params[0]['id']) && $params[0]['id'] == 'sidebar-delas-blog'){
         $blog_widget_num++;
		$divider = 2; //This is number of widgets that should fit in one row		
 
         //If it's third widget, add last class to it
         if($blog_widget_num % $divider == 0){
	    $class = 'class="last '; 
	    $params[0]['before_widget'] = str_replace('class="', $class, $params[0]['before_widget']);
	 }
 
	}
 
      return $params;
}

/*reading time*/
function fashy_estimated_reading_time( $id) {
	$post = get_post($id);
    $words = str_word_count( strip_tags( $post-> post_content ) );
    $minutes = floor( $words / 200 );
	if($minutes < 1) $minutes = 1;
	wp_reset_postdata(); 
    return $minutes;
}

/*post options*/
function fashy_set_post_views($postID) {
    $count_key = 'wpb_post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        $count = 0;
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
    }else{
        $count++;
        update_post_meta($postID, $count_key, $count);
    }
}
//To keep the count accurate, lets get rid of prefetching
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);

function fashy_track_post_views ($post_id) {
    if ( !is_single() ) return;
    if ( empty ( $post_id) ) {
        global $post;
        $post_id = $post->ID;    
    }
    fashy_set_post_views($post_id);
}
add_action( 'wp_head', 'fashy_track_post_views');

function fashy_get_post_views($postID){
    $count_key = 'wpb_post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
        return "0 View";
    }
    return $count.' Views';
}

/*globals*/

function fashy_globals($var){
	$fashy_data = get_option(OPTIONS);
	if(!empty($fashy_data[$var])){
		return true;
	}
	else{
		return false;
	}

}

function fashy_data($data){
	$fashy_data = get_option(OPTIONS);
	if(isset($fashy_data[$data])){
		return $fashy_data[$data];	
	} else {
		return '';	
	}
}

function fashy_block_one(){
$fashy_data = get_option(OPTIONS);
$categories = $fashy_data['featured_categories']; ?>
<div class="block1"> 
<?php
	foreach ($categories as $key => $category) {
		?>
		<a <?php if( ($key-1) % 3 == 0) echo 'class="last"';?>href="<?php echo esc_url($category['link']) ?>" title="Image">
		
			<div class="block1_img">
				<img src="<?php echo esc_url($category['image']) ?>" alt="<?php echo esc_html($category['title']) ?>">
			</div>
			
			<div class="block1_all_text">
				<div class="block1_text">
					<p><?php echo esc_html($category['title']) ?></p>
				</div>
				<div class="block1_lower_text">
					<p><?php echo esc_html($category['lower_title']) ?></p>
				</div>
			</div>									
		</a>						
	<?php
	} ?>
</div>
<?php
}


function fashy_block_two(){
$fashy_data = get_option(OPTIONS);
?>
	<div class="block2">
		<div class="block2_content">
					
			<div class="block2_img">
				<img class="block2_img_big" src="<?php echo esc_url($fashy_data['block2_img']) ?>" alt="Image">
			</div>						
			
			<div class="block2_text">
				<p><?php fashy_security($fashy_data['block2_text']) ?></p>
			</div>
		</div>								
	</div>
<?php
}

function fashy_logo(){?>
	<div class="logo-inner">
		<div id="logo" class="<?php if(is_active_sidebar( 'fashy_sidebar-logo' )) { echo 'logo-sidebar'; } ?>">
			<?php $logo = esc_url(fashy_data('logo')); ?>
			<a href="<?php echo esc_url(home_url('/')); ?>"><img src="<?php if (!empty($logo)) {?>
			<?php echo esc_url($logo); ?><?php } else {?><?php echo get_template_directory_uri(); ?>/images/logo.png<?php }?>" data-rjs="3" alt="<?php esc_html(bloginfo('name')); ?> - <?php esc_html(bloginfo('description')) ?>" /></a>
		</div>
		<?php if(is_active_sidebar( 'fashy_sidebar-logo' )) { ?> 
			<div class="logo-advertise">
				<?php if(is_active_sidebar( 'fashy_sidebar-logo' )) { ?>
					<?php dynamic_sidebar( 'fashy_sidebar-logo' ); ?>
				<?php } ?>
			</div>
		<?php } ?>									
	</div>	
<?php
}

/*import plugins*/

add_action( 'tgmpa_register', 'fashy_required_plugins' );

function fashy_required_plugins() {

    /**
     * Array of plugin arrays. Required keys are name and slug.
     * If the source is NOT from the .org repo, then source is also required.
     */
    $plugins = array(
			
		array(
				'name'      => esc_html__('Shortcode Ultimate','fashy'),
				'slug'      => 'shortcodes-ultimate',
				'required'  => false,
			),		
		array(
				'name'      => esc_html__('Contact Form 7','fashy'),
				'slug'      => 'contact-form-7',
				'required'  => false,
			),			
		array(
				'name'      => esc_html__('Facebook Page Plugin','fashy'),
				'slug'      => 'facebook-page-feed-graph-api',
				'required'  => false,
			),			
		array(
				'name'      => esc_html__('MailPoet Newsletters','fashy'),
				'slug'      => 'wysija-newsletters',
				'required'  => false,
			),			
		array(
				'name'      => esc_html__('Instagram Feed','fashy'),
				'slug'      => 'instagram-feed',
				'required'  => false,
			),			
		array(
				'name'      => esc_html__('SoundCloud Shortcode','fashy'),
				'slug'      => 'instagram-feed',
				'required'  => false,
			),	
			
			
			
				
    );

    $config = array(
        'id'           => 'fashy',                 // Unique ID for hashing notices for multiple instances of TGMPA.
        'default_path' => get_template_directory() . '/includes/plugins/',                      // Default absolute path to pre-packaged plugins.
        'menu'         => 'tgmpa-install-plugins', // Menu slug.
        'has_notices'  => true,                    // Show admin notices or not.
        'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
        'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
        'is_automatic' => true,                   // Automatically activate plugins after installation or not.
        'message'      => '',                      // Message to output right before the plugins table.
        'strings'      => array(
            'page_title'                      => __( 'Install Required Plugins', 'fashy' ),
            'menu_title'                      => __( 'Install Plugins', 'fashy' ),
            'installing'                      => __( 'Installing Plugin: %s', 'fashy' ), // %s = plugin name.
            'oops'                            => __( 'Something went wrong with the plugin API.', 'fashy' ),
            'notice_can_install_required'     => _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.', 'fashy' ), // %1$s = plugin name(s).
            'notice_can_install_recommended'  => _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.', 'fashy' ), // %1$s = plugin name(s).
            'notice_cannot_install'           => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.', 'fashy' ), // %1$s = plugin name(s).
            'notice_can_activate_required'    => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.', 'fashy' ), // %1$s = plugin name(s).
            'notice_can_activate_recommended' => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.', 'fashy' ), // %1$s = plugin name(s).
            'notice_cannot_activate'          => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.', 'fashy' ), // %1$s = plugin name(s).
            'notice_ask_to_update'            => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.', 'fashy' ), // %1$s = plugin name(s).
            'notice_cannot_update'            => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.', 'fashy' ), // %1$s = plugin name(s).
            'install_link'                    => _n_noop( 'Begin installing plugin', 'Begin installing plugins', 'fashy' ),
            'activate_link'                   => _n_noop( 'Begin activating plugin', 'Begin activating plugins', 'fashy' ),
            'return'                          => __( 'Return to Required Plugins Installer', 'fashy' ),
            'plugin_activated'                => __( 'Plugin activated successfully.', 'fashy' ),
            'complete'                        => __( 'All plugins installed and activated successfully. %s', 'fashy' ), // %s = dashboard link.
            'nag_type'                        => 'updated' // Determines admin notice type - can only be 'updated', 'update-nag' or 'error'.
        )
    );

    tgmpa( $plugins, $config );

}
?>