<?php
/*
Plugin Name: Words Count
Version:1.0
 */

/* This function will count all the words from content */


function wordscount($content) {
    
    return $content . " (" . str_word_count(strip_tags($content)) . "words.)";
}

/* Call function using filter */
add_filter('the_content', 'wordscount');
?>