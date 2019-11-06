<?php
/*
Plugin Name:TwitterPLugin
Version:1.0
 */

add_shortcode('t', 'twitter_shortcode');


function twitter_shortcode($content) {
    
    $display = '<a href="https://twitter.com/SundarPichai">Sundar Pichai Twitter</a>';
	
	
	return $display;
	
	
	
}


?>