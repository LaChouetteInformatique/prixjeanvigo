<?php
// DB_PLUGIN\Inc\Helpers\pre_print_r(get_defined_vars());
$paged = ( empty($data['pagenum']) ) ? 1 : $data['pagenum'];
// if(strlen($paged) > 3) $paged = 1;
// DB_PLUGIN\Inc\Helpers\pre_print_r($paged);

$args = array(
  'post_type' => 'laureat',
  'posts_per_page' => 8,
  'fields' => 'ids',
  'paged' => $paged,
  'orderby' => 'order_by_date',
);

// Recherche texte
if(!empty($data['s'])){
  $args['s'] = $data['s'];
}

$meta_query = [];

// Type de prix
if(!empty($data['price']) && $data['price'] != 'default' ){
  $meta_query[] = [
      'key'       => 'prix_jean_vigo',
      'value'     => $data['price'],
      'compare'   => '='
  ];
}

// Date
if(!empty($data['date']) && $data['date'] != 'default' ){
  $meta_query[] = [
      'key'       => 'annee',
      'value'     => $data['date'],
      'compare'   => '='
  ];
}

// Order
$meta_query["order_by_date"] = [
    'key'       => 'annee',
    'compare'   => 'EXISTS'
];
if(!empty($data['order']) && $data['order'] != 'default' ){
  $args['order'] = 'ASC';
}

if( count( $meta_query ) > 1 ){
  $meta_query['relation'] = 'AND';
}

if( count( $meta_query ) > 0 ){
  $args['meta_query'] = $meta_query;
}



$query = new WP_Query($args);

// Display Results
if($query->have_posts()) { ?>
    <div class="laureats-wrapper">
    <?php
    if ( $query->have_posts() ) { 
      while($query->have_posts()) : $query->the_post();

        $thumbnail_id = get_post_thumbnail_id( get_the_ID() );
        $alt = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);
        if(!$alt) $alt = get_the_title();
        ?>

        <div class="laureat-item">
          <a class="l-thumb" href="<?= get_permalink() ?>">
            <div>
              <?php the_post_thumbnail( 'medium', array( 'alt' => $alt ) ); ?>
            </div> 
          </a>
          
          <div class="l-content">
            <a href="<?= get_permalink() ?>">
              <h3><?= the_title() ?></h3>
            </a>
            <div>
              <p class="l-price"><?= get_field("prix_jean_vigo") ?></p>
              <p class="l-year"><?= get_field("annee") ?></p>
            </div>                                                  
          </div>                                 
        </div>
      <?php
      endwhile;
    }
    ?>
    </div>
    
    <div class="pagination-block">        
        <?php
        //  PAGINATION LINKS
        echo paginate_links( array(
            'total'        => $query->max_num_pages,
            'current'      => $paged,
            'show_all'     => true,
            'prev_next'    => false,
            'base' => "#%#%" //will make hrefs like "#3"
        ) );
        ?>
    </div>

    <?php
}
else { ?>
    <div class="laureats-wrapper">
        <div class="no-result">
            <h2>Aucun résultat trouvé.</h2>
        </div>
    </div>
<?php }

wp_reset_postdata();
?>