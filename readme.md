# Functionnalities :

## Autoloader

Custom autoloader and classes using singleton pattern

## class Main

### Transient

Usage of a transient to store custom WP_Query result in database for a few seconds. This transient is then called from multiple shortcodes

``` php
add_shortcode( 'last-palmares-image', [$this, 'last_palmares_image_shortcode']);
add_shortcode( 'last-palmares-details', [$this, 'last_palmares_details_shortcode']);
add_shortcode( 'last-palmares-link', [$this, 'last_palmares_link_shortcode']);
function get_last_palmares(){...}
```

### Shortcodes

Usage of multiple shortcodes

``` php
add_shortcode( 'acf', [$this, 'acf_shortcodes']);
add_shortcode( 'mobile_menu_btn', [$this, 'print_mobile_menu_btn_shortcode']);
add_shortcode( 'last-palmares-image', [$this, 'last_palmares_image_shortcode']);
add_shortcode( 'last-palmares-details', [$this, 'last_palmares_details_shortcode']);
add_shortcode( 'last-palmares-link', [$this, 'last_palmares_link_shortcode']);
add_shortcode( 'lci-year', function() { $year = date('Y'); return $year; });

// laureats-listing Shortcode
add_action('init', [ $this, 'laureats_listing_shortcode_resource' ]);
add_shortcode( 'laureats-listing', [$this, 'laureats_listing_shortcode']);
```


### Custom post grid with filters and Ajax loading

``` php
// Ajax request Handler for "laureats_listing" action
add_action('wp_ajax_nopriv_laureats_listing', [ $this, 'laureats_listing_filter_ajax' ]);
add_action('wp_ajax_laureats_listing', [ $this, 'laureats_listing_filter_ajax' ]);
```

#### Linked Template parts :
`/inc/template_parts/ajax-laureats-listing-content.php`
`/inc/template_parts/ajax-laureats-listing-filters.php`

### Admin pannel add custom columns

``` php
    // Check if we are on the back-end, and on the edit page for the laureat post-type
    if ( is_admin() && 'edit.php' == $pagenow && 'laureat' == $_GET['post_type'] ) {
      // Add the "Année" column to the "laureat" post type admin pannel
      add_filter( "manage_laureat_posts_columns", [$this, "laureat_custom_columns_list"] );
      // Populate "laureat" post type admin pannel's custom column
      add_action( "manage_laureat_posts_custom_column", [$this, "laureat_custom_columns_values"], 10, 2 );
      // Allow to sort "annee" column in "Lauréats" post type admin pannel
      add_filter( 'manage_edit-laureat_sortable_columns', [$this, 'laureat_sortable_columns']);
      // Configure annee sorting behavior for queries
      add_action( 'pre_get_posts', [$this, 'laureat_posts_orderby'] );
    }
```


### Others

``` php
// Core vital improvement
add_filter( 'elementor_pro/custom_fonts/font_display', function( $current_value, $font_family, $data ) { return 'swap'; }, 10, 3 );

// Patch bug in Elementor Tinymce that remove &nbsp; when switching between code and visual editor
add_filter( 'tiny_mce_before_init', [$this, 'allow_nbsp_in_tinymce'] );

// Include scroll top button in the footer
add_action( 'wp_footer', [$this, 'print_scrolltopbutton']);
```


## class Rest_API

Custom rest route to be used by wpdatatable plugin

`add_rest_routes()` ->
url : `/wp-json/pjv/v1/laureats`