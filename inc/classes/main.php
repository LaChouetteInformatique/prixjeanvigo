<?php
/**
 * Bootstraps / Encapsulates the Plugin
 * 
 * @package DB_Plugin
 */

namespace DB_PLUGIN\Inc;

class Main {

	use Traits\Singleton;

	protected function __construct() {

		// load others classes.
		Rest_API::getInstance();
		// Utils::getInstance();

		// Register main scripts and styles
		add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);

		add_shortcode( 'acf', [$this, 'acf_shortcodes']);
		add_shortcode( 'mobile_menu_btn', [$this, 'print_mobile_menu_btn_shortcode']);
		add_shortcode( 'last-palmares-image', [$this, 'last_palmares_image_shortcode']);
		add_shortcode( 'last-palmares-details', [$this, 'last_palmares_details_shortcode']);
		add_shortcode( 'last-palmares-link', [$this, 'last_palmares_link_shortcode']);
		add_shortcode( 'lci-year', function() { $year = date('Y'); return $year; });

		// Add body classes
		add_filter( 'body_class', function($classes){ $classes[] = 'body-flex'; return $classes; } );

		// Core vital improvement
		add_filter( 'elementor_pro/custom_fonts/font_display', function( $current_value, $font_family, $data ) { return 'swap'; }, 10, 3 );

		// Patch bug in Elementor Tinymce that remove &nbsp; when switching between code and visual editor
		add_filter( 'tiny_mce_before_init', [$this, 'allow_nbsp_in_tinymce'] );

		// Move Yoast SEO configuration to the bottom of the page in editor
		add_filter( 'wpseo_metabox_prio', function () { return 'low';	});

		// Include scroll top button in the footer
    add_action( 'wp_footer', [$this, 'print_scrolltopbutton']);

		// laureats-listing Shortcode
		add_action('init', [ $this, 'laureats_listing_shortcode_resource' ]);
		add_shortcode( 'laureats-listing', [$this, 'laureats_listing_shortcode']);

		// Ajax request Handler for "laureats_listing" action
    add_action('wp_ajax_nopriv_laureats_listing', [ $this, 'laureats_listing_filter_ajax' ]);
    add_action('wp_ajax_laureats_listing', [ $this, 'laureats_listing_filter_ajax' ]);

		global $pagenow;

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

	}

	function allow_nbsp_in_tinymce( $mceInit ) {
		// action=elementor
		$mceInit['entities'] = '160,nbsp';   
		$mceInit['entity_encoding'] = 'named';
		return $mceInit;
	}

	function enqueue_scripts() {  

		wp_enqueue_style( 
			'db_plugin_style', 
			DB_PLUGIN_DIR_URL . 'assets/build/main.css',
			array(),
			DB_PLUGIN_VERSION
		);

		wp_enqueue_script(
			'db_plugin_main_script',
			DB_PLUGIN_DIR_URL . 'assets/build/main.js',
			['jquery'],
			DB_PLUGIN_VERSION,
			[ 'strategy' => 'defer', 'in_footer' => true ]
		);

		if ( is_singular( ['actualite','palmares-media', 'laureat'] ) ) {
			wp_enqueue_style( 
				'db_plugin_style_single', 
				DB_PLUGIN_DIR_URL . 'assets/build/single.css',
				array(),
				DB_PLUGIN_VERSION
			);
		}

	}

	function acf_shortcodes ($atts) {

		$atts = shortcode_atts( array(
			'field' => 'default',
			'post_id' => 1,
		), $atts, 'acf' );

		$field = esc_attr($atts['field']);
		$post_id = esc_attr($atts['post_id']);

		$value = apply_filters('acf/format_value', get_field($atts['field'], $atts['post_id']));
		$field = get_field_object($atts['field'], $atts['post_id']);
		return $value;
		// echo "<pre>";
		// print_r($field);
		// echo "</pre>";
	}

  function print_mobile_menu_btn_shortcode( $atts ) {
    // start buffer
		ob_start();

		$atts = shortcode_atts( array(
					'url' => '#mobile-menu-popup',
					'active' => '',
					'close' => '',
			), $atts, 'mobile_menu_btn' );

		$url = esc_attr($atts['url']);
		$active = esc_attr($atts['active']) === 'true' ? 'active' : '';
		$close = esc_attr($atts['close']) === 'true';

		//get_template_part('template-parts', 'mobile_menu_btn');

		if($close) {
			echo <<<EOL
			<a href="#" onclick="return false;" class="mobile-menu-btn mobile-menu-btn-close active" aria-label="Close Menu" role="button" aria-pressed="true">
			<span></span>
			</a>
			EOL;
		} else {
			echo <<<EOL
			<a href="$url" class="mobile-menu-btn $active" aria-label="Main Menu" aria-haspopup="menu" role="button" aria-pressed="false">
			<span></span>
			</a>
			EOL;
		}

		return ob_get_clean();
  }

	function print_scrolltopbutton() {
		echo <<<END
			<div id="scroll-top-button" class="print-hide hidden-sm" >

				<a href="#top">
						<svg focusable="false" aria-hidden="true" width="1em" height="1em" viewBox="0 0 16 16" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;">
								<path fill="currentColor" d="M7.626,0C7.992,-0.006 8.361,0.131 8.641,0.411L14.841,6.611C15.388,7.158 15.388,8.047 14.841,8.595L14.06,9.376C13.512,9.923 12.623,9.923 12.076,9.376L9.868,7.168L9.868,14.077C9.867,14.216 9.845,14.354 9.798,14.485C9.678,14.827 9.403,15.105 9.062,15.23C8.932,15.278 8.795,15.302 8.656,15.304L6.632,15.304C6.493,15.304 6.356,15.281 6.225,15.235C5.877,15.113 5.597,14.832 5.474,14.485C5.428,14.354 5.406,14.216 5.405,14.077L5.405,7.146L3.176,9.376C2.628,9.923 1.739,9.923 1.191,9.376L0.411,8.595C-0.137,8.047 -0.137,7.158 0.411,6.611L6.611,0.411C6.89,0.131 7.259,-0.006 7.626,0Z"/>
						</svg>
						<span class="sr-only"><?php echo "back to top"; ?></span>
				</a>
				<div class="trigger-top"></div>
				<div class="trigger-bottom"></div>
			</div>
		END;
	}

	// On récupère la derniere actualité en DB avec utilisation d'un transient
	function get_last_palmares() {
		$nomtransient = 'derniere_actualite'; // custom name
		// delete_transient($nomtransient);
		// Le transient est-il inexistant ou expiré ?
		if ( false === ( $transient = get_transient( $nomtransient ) ) ) {

			// Helpers\pre_print_r("inside");

			// On récupère la derniere actualite en DB
			$args = array(
				'post_type' => 'actualite',
				'post_status' => 'publish',
				'posts_per_page'=>1,
				'order'=>'DESC',
				'orderby'=>'date',
				// 'tax_query' => array(
				// 	array(
				// 		'taxonomy' => 'categories-mediatheque',
				// 		'field' => 'slug',
				// 		'terms' => 'palmares',
				// 	),
				// ),
				'meta_query'    => array(
					// 'relation'      => 'AND',
					array(
						'key'       => 'is_palmares',
						'value'     => '1',
						'compare'   => '=',
					),
				),
			);
			$query = new \WP_Query( $args );
			$posts = $query->posts;

			if( $query->have_posts() ) :
				$query->the_post();
				global $post;
			endif;
			// Je met à jour la valeur du transient avec $value, et j'indique à WordPress une durée d'expiration de x secondes
			set_transient($nomtransient, $post, 10);
			wp_reset_postdata();
			// Je met à jour la valeur de ma variable $transient
			$transient = get_transient( $nomtransient );
		}
		// Helpers\pre_print_r($transient);
		return $transient;
	}

	function last_palmares_image_shortcode() {
		// start buffer
		ob_start();

			$post = $this->get_last_palmares();
		
			if (has_post_thumbnail( $post->ID ) ):
				$image_id = get_post_thumbnail_id($post->ID);
				$image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', TRUE);
				$image_title = get_the_title($image_id);
				$size = 'medium_large'; // Defaults to 'thumbnail' if omitted.
				$image_src = wp_get_attachment_image_src($image_id, $size)[0];

				// Without extension
				// $image = substr($image_src, 0, strrpos($image_src, '.'));
				?>
			<img loading="lazy" decoding="async" width="350" height="350" src="<?=$image_src?>" class="attachment-large size-large wp-image" alt="<?=$image_alt?>">
			<?php endif;
		
		return ob_get_clean();
	}

	function last_palmares_details_shortcode() {
		ob_start();
			$post = $this->get_last_palmares();
			the_field("detail_palmares", $post->ID);
		return ob_get_clean();
	}

	function last_palmares_link_shortcode() {
		ob_start();
			$post = $this->get_last_palmares();
			echo get_permalink( $post );
		return ob_get_clean();
	}

	function laureats_listing_shortcode_resource(){
		wp_register_style(
      "ajax-laureats-listing", DB_PLUGIN_DIR_URL . 'assets/build/ajax-laureats-listing.css',
      array(), DB_PLUGIN_VERSION, "all");

    wp_register_script(
        'ajax-laureats-listing', DB_PLUGIN_DIR_URL . 'assets/build/ajax-laureats-listing.js',
        array('jquery'), DB_PLUGIN_VERSION, true );

    // wp_ajax variable reachable from JS
    wp_localize_script('ajax-laureats-listing', 'ajax_laureat_listing_filters', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'security' => wp_create_nonce('laureat_listing_filters_nonce') // transmit nonce to frontend JS
      )
    );
	}

	function laureats_listing_shortcode($atts) {
		// Enqueue script and style registred earlier in function laureats_listing_shortcode_resource
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script("ajax-laureats-listing");
    wp_enqueue_style("ajax-laureats-listing");

		ob_start(); ?>
		<div class="ajll-wrap">

			<div class="ajll-filters">
				<?php require_once DB_PLUGIN_DIR_PATH."inc/template_parts/ajax-laureats-listing-filters.php"; ?>
			</div>

			<div class="ajll-content">
				<?php require_once DB_PLUGIN_DIR_PATH."inc/template_parts/ajax-laureats-listing-content.php"; ?>
			</div>


		</div>
		<?php
    return ob_get_clean();
	}

	function laureats_listing_filter_ajax() {

		$data = $_POST;
		// Helpers\pre_print_r(get_defined_vars());
		check_ajax_referer('laureat_listing_filters_nonce', 'security'); // Security: verify submitted nonce

		require_once DB_PLUGIN_DIR_PATH."inc/template_parts/ajax-laureats-listing-content.php";

		die();
	}

	/** Add the "Année" column to the "laureat" post type  admin pannel */
	public function laureat_custom_columns_list( $columns ) {
		$index = 0;
		$new_cols = [];
		foreach ($columns as $key => $value) {
			$new_cols[$key] = $value;
			if($index == 1 /* position */) {
				$new_cols['annee'] = __( 'Année', 'db-plugin' );
			}
			$index++;
		}
    return $new_cols;
  }

	/** Populate annee column values */
	public function laureat_custom_columns_values( $column_key, $post_id ) {
    global $post;

    if( $column_key == 'annee' ) {
      $annee = get_field('annee');
      if( $annee ) {
        echo $annee;
      } else {
        echo date('Y');
      }
    }
  }

  /** Allow to sort "annee" column in "Lauréats" post type admin pannel */
  public function laureat_sortable_columns( $columns ) {
		// $columns['annee'] = 'annee';
		$columns['annee'] = [
			'annee', // Menu's internal name, same as key in array
			true, // Initialise with my specified order, false to disregard
			__( 'Année', 'db-plugin' ), // Short column name (abbreviation) for `abbr` attribute
			__( 'Trier par année.', 'db-plugin' ), // Translatable string of a brief description to be used for the current sorting
			'desc' // Initialise in ascending order, can also be 'desc', false, or omitted to default to false
		];
		// Helpers\print_die($columns);
    return $columns;
  }

	/** Configure orderby annee behavior in queries */
  public function laureat_posts_orderby( $query ) {
		// Return if not main query
    if( /*! is_admin() || */! $query->is_main_query() ) {
      return;
    }

    $orderby = $query->get( 'orderby' );
		// Helpers\print_die($orderby);
    if ( 'annee' == $orderby ) {
      $query->set( 'orderby', 'meta_value' );
      $query->set( 'meta_key', 'annee' );
      $query->set( 'meta_type', 'numeric' );
    }
  }
	
}