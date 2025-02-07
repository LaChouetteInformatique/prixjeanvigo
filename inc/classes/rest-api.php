<?php
/**
 * WordPress Rest API Custom Routes
 * 
 * @package DB_Plugin
 */

namespace DB_PLUGIN\Inc;

class Rest_API {

	use Traits\Singleton;

	protected function __construct() {

    add_action('rest_api_init', [$this, 'add_rest_routes']);
		
	}

  function add_rest_routes() {
    // url : /wp-json/pjv/v1/laureats
    register_rest_route('pjv/v1', 'laureats', [
      'methods' => 'GET',
      'callback' => [$this, 'pjv_laureats'], // [new UTILS,'pjv_laureats']
    ]);
  }

  function pjv_laureats() {
    $args = [
      'numberposts' => -1,
      'post_type' => 'laureat'
    ];

    $posts = get_posts($args);

    $data = [];
    $i = 0;

    foreach($posts as $post) {
      $data[$i]['Nom'] = $post->post_title;
      $data[$i]['Prix'] = get_field('prix_jean_vigo', $post->ID);
      $data[$i]['Année'] = get_field('annee', $post->ID);
      $data[$i]['Film'] = empty(get_field('film', $post->ID))? '-' : get_field('film', $post->ID);
      $i++;
    }

    return $data;
  }

}