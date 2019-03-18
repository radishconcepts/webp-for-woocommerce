<?php
/**
 * Replace Images
 *
 * @package    Radish_WebP
 */

namespace Radish_WebP;

/**
 * Replace Images
 *
 * Replace images in both WordPress as Woocommerce
 *
 * @category   Components
 * @package    Radish_WebP
 * @subpackage Replace_Images
 * @author     Radish Concepts <info@radishoncepts.com>
 * @license    https://www.gnu.org/licenses/gpl-3.0.txt GNU/GPLv3
 * @link       website
 * @since      1.0.0
 */
class Replace_Images {
	/**
	 * Constructor.
	 *
	 * @since 1.0
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Initialisation of the plugin.
	 *
	 * @since 1.0
	 */
	public function init() {
		add_filter( 'woocommerce_product_get_image', array( $this, 'replace_woocommerce_image' ), 10, 3 );

		add_filter( 'woocommerce_single_product_image_thumbnail_html', array(
			$this,
			'product_gallery_main_image'
		), 10, 2 );

		add_filter( 'image_send_to_editor', array( $this, 'insert_webp_into_post_content' ), 50, 8 );

		add_action( 'admin_menu', array( $this, 'add_admin_submenu_page' ) );
	}

	public function get_webp_html( $webp, $default ) {
		// TODO: Copy srcset from old attribute and replace
		$output = '<picture>';
		$output .= '<source srcset="' . $webp . '" type="image/webp">';
		$output .= '<source srcset="' . $default . '" type="image/jpeg">';
		$output .= '<img src="' . $default . '" alt="Alt Text!">';
		$output .= '</picture>';

		return $output;
	}

	public function get_images( $id, $size ) {
		$alt_image     = wp_get_attachment_image_src( $id, $size, false );
		$alt_image_src = $alt_image[0];
		$path          = pathinfo( $alt_image_src );
		$webp          = str_replace( $path['extension'], 'webp', $alt_image_src );

		return (object) array(
			'webp'    => $webp,
			'default' => $alt_image_src,
		);
	}

	public function replace_woocommerce_image( $image, $product, $size ) {
		$image_id = $product->get_image_id();
		$images   = $this->get_images( $image_id, $size );

		return $this->get_webp_html( $images->webp, $images->default );
	}

	public function product_gallery_main_image( $html, $id ) {

		$images = $this->get_images( $id, 'full' );

		return preg_replace( '/(<)([img])(\w+)([^>]*>)/', $this->get_webp_html( $images->webp, $images->default ), $html );

	}

	public function replace_content_images( $content ) {

		$imgs = preg_match_all( '(<img.+src=[\'"]([^\'"]+)[\'"].*srcset=[\'"]([^\'"]+)[\'"][^>]*>)', $content, $elements );

		$mi = new \MultipleIterator();
		foreach ( $elements as $img ) {
			$mi->attachIterator( new \ArrayIterator( $img ) );
		}

		foreach ( $mi as $key => $elem ) {
			var_dump( $elem );
		}

		return $content;
	}

	public function insert_webp_into_post_content( $html, $id, $caption, $title, $align, $url, $size, $alt ) {
		$images = $this->get_images( $id, $size );

		return $this->get_webp_html( $images->webp, $images->default );
	}

	public function add_admin_submenu_page() {
		add_submenu_page( 'options-general.php',
			__( 'Replace images by WebP', 'radish-webp' ),
			__( 'Replace images by WebP', 'radish-webp' ),
			'manage_options',
			'radish-webp',
			array( $this, 'admin_screen' )
		);
	}

	public function admin_screen() {
		$replace_by_webp = isset($_GET['replace_webp']);

		echo '<h1>Replace images by WebP</h1>';

		if ( $replace_by_webp ) {
			$posts = get_posts( array(
				'numberposts' => 5,
				'post_type'   => array('post', 'page'),
			) );

			foreach ( $posts as $post ) {
				wp_update_post( array(
					'ID'           => $post->ID,
					'post_content' => $this->replace_content_images($post->post_content)
				) );
				echo '<a href="'.get_permalink($post->ID).'">'.$post->post_title.'</a>';
			}

			echo '<p>All post images have been replaced by picture tags with webp source.</p>';
		} else {
			echo '<p><a href="'.$_SERVER['REQUEST_URI'].'&replace_webp=true">Click here</a> to start.</p>';
		}
	}
}
