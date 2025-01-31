<?php
/**
 * WordPress related helpers functions
 *
 * @package DB_Plugin
 */

namespace DB_PLUGIN\Inc\Helpers;

/** Retrieves the names of the terms of a taxonomy that are attached to a specific post.
 * 
 * @param int|WP_Post $post     : Post ID or object.
 * @param string      $taxonomy : Taxonomy name.
 * @return string[] : list of terms names
 * 
 */
function get_post_terms_names($post, $taxonomy) {

    $terms = get_the_terms( $post, $taxonomy );

    $term_names = [];

    if ( $terms && ! is_wp_error( $terms ) ){
      foreach ( $terms as $term ) {
        $term_names[] = $term->name;
      }
    }
    return $term_names;
}


if (!function_exists(__NAMESPACE__ . '\print_die')){
  function print_die($var){
    echo '<pre>';
    print_r($var);
    echo '</pre>';
    wp_die();
  }
}

if (!function_exists(__NAMESPACE__ . '\pre_print_r')){
  function pre_print_r($var){
    echo '<pre>';
    print_r($var);
    echo '</pre>';
  }
}