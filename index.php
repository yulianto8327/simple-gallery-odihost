<?php
/*
Plugin Name: Easy Gallery
Plugin URI: http://www.odihost.com/
Description: This plugin will help you inserting gallery to your Wordpress page or post. If you have any problem with the plugin or need customization please contact us <a href='http://odihost.com/contact-us'>here</a>.
Author: Odihost
Author URI: http://www.odihost.com/
Version: 1.3
License: GPLv2 or later
*/



add_action( 'wp_enqueue_scripts', 'gallery_script' );

include("includes/functions.php");
include("pages/gallery.php");


if(is_admin())
{	include("admin/admin.php");
}

/* Shortcode */




function easy_gallery_install()
{
	global $wpdb;
	
	//$wpdb->query("DROP TABLE easy_gallery");
	//$wpdb->query("DROP TABLE easy_gallery_line");
	
	$sql = "CREATE TABLE IF NOT EXISTS `easy_gallery` (
		  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `gallery_name` varchar(100) DEFAULT NULL,
		  `thumb_width` int(11) DEFAULT NULL,
		  `thumb_height` int(11) DEFAULT NULL,
		  `full_size_width` int(11) DEFAULT NULL,
		  `full_size_height` int(11) DEFAULT NULL,
		  `video_width` varchar(10) NOT NULL,
		  `video_height` varchar(10) NOT NULL,
		  `type` int(11) DEFAULT NULL,
		  `custom_css` text,
		  PRIMARY KEY (`id`),
		  KEY `galleryid` (`id`)
		 )";
	$wpdb->query($sql);

	$sql = "CREATE TABLE IF NOT EXISTS `easy_gallery_line` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `gallery_id` int(11) DEFAULT NULL,
			  `file_name` varchar(100) DEFAULT NULL,
			  `video_url` text NOT NULL,
			  `caption` text NOT NULL,
			  `order_no` int(11) DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `gallery_lineid` (`id`)
			)";
	$wpdb->query($sql);	
}
    function galleryshow_func( $atts ) {
	
	ob_start();
	show_gallery($atts["id"]);
	$change= ob_get_contents();
	ob_end_clean();
	return $change;
    
    
    }
    
    
    function easygallery_func( $atts ) {
	
	ob_start();
	display_easy_gallery($atts["id"]);
	$change= ob_get_contents();
	ob_end_clean();
	return $change;
    
    
    }
    
    /**
function gallerylist_func( $attsb ) {
	
	ob_start();
	display_gallery_list($attsb["id"]);
	$change= ob_get_contents();
	ob_end_clean();
	return $change;
}

*/

function gallery_script() {		
	//fix conflict with responsive lightbox plugin problem
	wp_deregister_script('responsive-lightbox-prettyphoto');
	wp_deregister_script('responsive-lightbox-swipebox');
	wp_deregister_script('responsive-lightbox-fancybox');
	wp_deregister_script('responsive-lightbox-nivo');
	wp_deregister_script('responsive-lightbox-front');
	// end scripts
	
	wp_deregister_script('jquery');
	wp_register_script('jquery', "http" . ($_SERVER['SERVER_PORT'] == 443 ? "s" : "") . "://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js", array() );
	wp_enqueue_script('jquery');
	wp_enqueue_script( 'jquery-noconflict', plugins_url( '/js/noconflict.js' , __FILE__ ), array( 'jquery' ) );
	
	wp_enqueue_script( 'prototype' );
	
    wp_enqueue_script( 'scriptaculous' );
    wp_enqueue_style( 'css-gallery-list', plugins_url( '/css/gallerylist.css' , __FILE__ ) );
	wp_enqueue_style( 'css-gallery', plugins_url( '/css/gallery.css' , __FILE__ ) );	
	wp_enqueue_style( 'css-lightbox', plugins_url( '/css/lightbox.css' , __FILE__ ) );	
	wp_enqueue_style( 'css-carousel', plugins_url( '/css/carousel.css' , __FILE__ ) );
	wp_enqueue_script( 'gallery-jcarousel', plugins_url( '/js/jquery.jcarousel.min.js' , __FILE__ ), array()  );
	wp_enqueue_script( 'gallery-simple-carousel', plugins_url( '/js/simple_carousel.js' , __FILE__ ), array()  );
	
	if(get_option('lightbox_theme') == 'on')wp_enqueue_script( 'gallery-lightbox', plugins_url( '/js/lightbox.js' , __FILE__ ), array(), false ,true );
	wp_enqueue_script( 'gallery', plugins_url( '/js/gallery.js' , __FILE__ ), array() );
}

add_option('lightbox_theme','on');
add_shortcode( 'easygallerylist', 'galleryshow_func' );

add_shortcode('easygallery', 'easygallery_func'); /**
add_shortcode( 'easygallerylist', 'gallerylist_func' );
*/
register_activation_hook(__FILE__,'easy_gallery_install');


/**
 * 
 * #dhi
function gallerylist_setup(){
    
        wp_enqueue_style( 'css-gallery-list1', plugins_url( '/css/gallerylist.css' , __FILE__ ) );	
       	wp_enqueue_style( 'css-gallery-list', plugins_url( '/css/gallery.css' , __FILE__ ) );	
    	wp_enqueue_style( 'css-lightbox-list', plugins_url( '/css/lightbox.css' , __FILE__ ) );	
    	wp_enqueue_style( 'css-carousel-list', plugins_url( '/css/carousel.css' , __FILE__ ) );
        wp_enqueue_script( 'gallerylist-jquery', plugins_url( 'js/jquery.js', __FILE__ ) );
		//load ajax //
        $array = array( 'GalleryListajaxurl' => plugins_url('/pages/gallery-id.php', __FILE__ ) );  // /wp-content/plugins/simple-gallery-odihost-dhi
        wp_localize_script( 'gallerylist-jquery', 'object_names', $array );
        
        wp_enqueue_script( 'gallerylist-jquery' );
		 wp_enqueue_script( 'gallerylist', plugins_url( 'gallerylist.js', __FILE__ ) );
}

 add_action( 'wp_enqueue_scripts', 'gallerylist_setup' );
	*/	
?>