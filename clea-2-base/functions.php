<?php
/**
 * 
 * this file is designed to provide specific functions for the child theme
 *
 * @package    clea-2-base
 * @subpackage Functions
 * @version    1.0
 * @since      0.1.0
 * @author     Anne-Laure Delpech <ald.kerity@gmail.com>  
 * @copyright  Copyright (c) 2015 Anne-Laure Delpech
 * @link       
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */


// Do theme setup on the 'after_setup_theme' hook.
add_action( 'after_setup_theme', 'c2b_theme_setup', 11 ); 


# Change Read More link in automatic Excerpts
remove_filter('get_the_excerpt', 'wp_trim_excerpt');
add_filter('get_the_excerpt', 'c2b_custom_wp_trim_excerpt');


function c2b_theme_setup() {

	// Get the child template directory and make sure it has a trailing slash.
	$child_dir = trailingslashit( get_stylesheet_directory() );

	// Add support for the Wordpress custom-Logo 
	// see https://codex.wordpress.org/Theme_Logo
	add_theme_support( 'custom-logo', array(
		'height'      => 78,
		'width'       => 150,
		'flex-width'  => true,
	) );
	
	// add featured images to rss feed
	add_filter('the_excerpt_rss', 'c2b_featuredtoRSS');
	add_filter('the_content_feed', 'c2b_featuredtoRSS');
	
}
 
 


/*******************************************
* Change Read More link in Excerpts 
*
* see 
* http://wordpress.stackexchange.com/questions/207050/read-more-tag-shows-up-on-every-post
* http://wordpress.stackexchange.com/questions/141125/allow-html-in-excerpt/141136#141136
*  

*******************************************/

function c2b_allowedtags() {
    // Add custom tags to this string
	// <a>,<img>,<video>,<script>,<style>,<audio> are not in
    return '<br>,<em>,<i>,<ul>,<ol>,<li>,<p>'; 
}


function c2b_custom_wp_trim_excerpt($c2b_excerpt) {
	$raw_excerpt = $c2b_excerpt;
	
	// text for the "read more" link
	$rm_text = __( 'La suite &raquo;', 'stargazer' ) ;
	$excerpt_end = ' <a class="more-link" href="'. esc_url( get_permalink() ) . '">' . $rm_text . '</a>'; 
	
	
	if ( '' == $c2b_excerpt ) {  

		$c2b_excerpt = get_the_content('');
		$c2b_excerpt = strip_shortcodes( $c2b_excerpt );
		$c2b_excerpt = apply_filters('the_content', $c2b_excerpt);
		$c2b_excerpt = str_replace(']]>', ']]&gt;', $c2b_excerpt);
		$c2b_excerpt = strip_tags($c2b_excerpt, c2b_allowedtags()); /*IF you need to allow just certain tags. Delete if all tags are allowed */

		//Set the excerpt word count and only break after sentence is complete.
			$excerpt_word_count = 75;
			$excerpt_length = apply_filters('excerpt_length', $excerpt_word_count); 
			$tokens = array();
			$excerptOutput = '';
			$count = 0;

			// Divide the string into tokens; HTML tags, or words, followed by any whitespace
			preg_match_all('/(<[^>]+>|[^<>\s]+)\s*/u', $c2b_excerpt, $tokens);

			foreach ($tokens[0] as $token) { 

				if ($count >= $excerpt_length && preg_match('/[\,\;\?\.\!]\s*$/uS', $token)) { 
				// Limit reached, continue until , ; ? . or ! occur at the end
					$excerptOutput .= trim($token);
					break;
				}

				// Add words to complete sentence
				$count++;

				// Append what's left of the token
				$excerptOutput .= $token;
			}

		$c2b_excerpt = trim(force_balance_tags($excerptOutput));
	   
			// $c2b_excerpt .= $excerpt_end ;
			$excerpt_more = apply_filters( 'excerpt_more', ' ' . $excerpt_end ); 

			$pos = strrpos($c2b_excerpt, '</');
			if ($pos !== false) {
				// Inside last HTML tag
				$c2b_excerpt = substr_replace($c2b_excerpt, $excerpt_end, $pos, 0); // Add read more next to last word 
			} else {
				// After the content
				$c2b_excerpt .= $excerpt_more; //Add read more in new paragraph 
			}
			
		return $c2b_excerpt;   

	} /* else {
		return 'AAA ! ' . $raw_excerpt;
	} */
	
	// add read more link to the manual extract
	$c2b_excerpt .= $excerpt_end ;
	// return the manual extract
	// return apply_filters('c2b_custom_wp_trim_excerpt', 'AAA ! ' . $c2b_excerpt, $raw_excerpt);
	return apply_filters('c2b_custom_wp_trim_excerpt', $c2b_excerpt, $raw_excerpt);
}
  
	
function c2b_featuredtoRSS( $content ) {
	// https://woorkup.com/show-featured-image-wordpress-rss-feed/
	
	global $post;
	if ( has_post_thumbnail( $post->ID ) ){
		$content = '<div>' . get_the_post_thumbnail( $post->ID, 'thumbnail', array( 'style' => 'margin-bottom: 15px; margin-right: 15px; float: left;' ) ) . '</div>' . $content;
	}
	
	return $content;
}



?>