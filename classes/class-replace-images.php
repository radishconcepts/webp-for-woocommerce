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

	public function get_webp_html( $webp, $default, $extension, $html ) {
		$srcset = '';
		$alt = '';

		preg_match("/srcset=[\"']?((?:.(?![\"']?\s+(?:\S+)=|[>\"']))+.)[\"']?/", $html, $matches);

		if ($matches) {
			$srcset = $matches[1];
		}

		preg_match("/alt=[\"']?((?:.(?![\"']?\s+(?:\S+)=|[>\"']))+.)[\"']?/", $html, $matches);
		if ($matches) {
			$alt = $matches[1];
		}

		$output = '<picture>';
		if (!empty($srcset)) {
			$output .= '<source srcset="' . str_replace($extension, 'webp', $srcset) . '" alt="'.$alt.'" type="image/webp">';
		} else {
			$output .= '<source src="' . $webp . '" type="image/webp" alt="'.$alt.'">';
		}
		$output .= '<source src="' . $default . '" type="image/jpeg" alt="'.$alt.'">';
		$output .= '<img src="' . $default . '" alt="'.$alt.'">';
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
			'extension' => $path['extension']
		);
	}

	public function replace_woocommerce_image( $image, $product, $size ) {
		$image_id = $product->get_image_id();
		$images   = $this->get_images( $image_id, $size );


		return $this->get_webp_html( $images->webp, $images->default, $images->extension, $image  );
	}

	public function product_gallery_main_image( $html, $id ) {

		$images = $this->get_images( $id, 'full' );

		return preg_replace( '/(<)([img])(\w+)([^>]*>)/', $this->get_webp_html( $images->webp, $images->default, $images->extension, $html ), $html );

	}
}
