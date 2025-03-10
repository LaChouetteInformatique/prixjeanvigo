<?php
/**
 * Bootstraps / Encapsulates the Plugin
 * 
 * @package DB_Plugin
 */

namespace DB_PLUGIN\Inc;

class Utils {

	use Traits\Singleton;

	protected function __construct() {

		// Add image dimensions to all images that are missing them
		add_filter( 'the_content', [$this, 'add_image_dimensions'] );

		//Allow SVG upload
		add_filter( 'wp_check_filetype_and_ext', [$this, 'check_filetype_and_ext'], 10, 4 );;
		add_filter( 'upload_mimes', [$this, 'cc_mime_types'] );
	}

  // Create warning if image is missing
	function add_image_dimensions( $content ) {

    preg_match_all( '/<img[^>]+>/i', $content, $images);

    if (count($images) < 1)
        return $content;

    foreach ($images[0] as $image) {
        preg_match_all( '/(alt|title|src|width|class|id|height)=("[^"]*")/i', $image, $img );

        if ( !in_array( 'src', $img[1] ) )
            continue;

        if ( !in_array( 'width', $img[1] ) || !in_array( 'height', $img[1] ) ) {
            $src = $img[2][ array_search('src', $img[1]) ];
            $alt = in_array( 'alt', $img[1] ) ? ' alt=' . $img[2][ array_search('alt', $img[1]) ] : '';
            $title = in_array( 'title', $img[1] ) ? ' title=' . $img[2][ array_search('title', $img[1]) ] : '';
            $class = in_array( 'class', $img[1] ) ? ' class=' . $img[2][ array_search('class', $img[1]) ] : '';
            $id = in_array( 'id', $img[1] ) ? ' id=' . $img[2][ array_search('id', $img[1]) ] : '';

						list( $width, $height, $type, $attr ) = getimagesize( str_replace( "\"", "" , $src ) );
						$image_tag = sprintf( '<img src=%s%s%s%s%s width="%d" height="%d" />', $src, $alt, $title, $class, $id, $width, $height );
						$content = str_replace($image, $image_tag, $content);
        }
    }

    return $content;
	}

	//Allow SVG upload
	function check_filetype_and_ext($data, $file, $filename, $mimes) {
		global $wp_version;
		if ( $wp_version !== '4.7.1' ) {
			return $data;
		}
		$filetype = wp_check_filetype( $filename, $mimes );
		return [
				'ext'             => $filetype['ext'],
				'type'            => $filetype['type'],
				'proper_filename' => $data['proper_filename']
		];
	}
	
	function cc_mime_types( $mimes ){
		$mimes['svg'] = 'image/svg+xml';
		return $mimes;
	}

}