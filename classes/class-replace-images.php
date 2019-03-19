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

		add_filter(
			'woocommerce_single_product_image_thumbnail_html',
			array(
				$this,
				'product_gallery_main_image',
			),
			10,
			2
		);

		add_filter( 'image_send_to_editor', array( $this, 'insert_webp_into_post_content' ), 50, 8 );

		add_action( 'admin_menu', array( $this, 'add_admin_submenu_page' ) );
	}

	/**
	 *
	 * Get the HTML with support for WebP
	 *
	 * @param string $webp Path to the webp file.
	 * @param string $default Path to the default file.
	 * @param string $extension The extension of the file.
	 * @param int    $id Attachment ID.
	 * @param string $size Image size.
	 * @param string $classes Classes for the html element.
	 *
	 * @return string Newly generated HTML.
	 */
	public function get_webp_html( $webp, $default, $extension, $id, $size, $classes = '' ) {

		$srcset     = wp_get_attachment_image_srcset( $id, $size );
		$alt        = get_post_meta( $id, '_wp_attachment_image_alt', true );
		$main_image = false;

		$classes .= ' webp-image ';

		$output = '<picture class="' . $classes . '">';
		$output .= '<source class="remove-image" srcset="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" type="image/jpeg" media="(min-width: 800px)">';
		if ( ! empty( $srcset ) ) {
			$output .= '<source srcset="' . str_replace( $extension, 'webp', $srcset ) . '" alt="' . $alt . '" type="image/webp">';
		} else {
			$output .= '<source src="' . $webp . '" type="image/webp" alt="' . $alt . '">';
		}
		$output .= '<source src="' . $default . '" type="image/jpeg" alt="' . $alt . '">';

		$full_size = apply_filters( 'woocommerce_gallery_full_size', apply_filters( 'woocommerce_product_thumbnails_large_size', 'full' ) );
		$full_src  = wp_get_attachment_image_src( $id, $full_size );

		// Rebuild the image element so it can integrate with photoswipe.
		$output .= wp_get_attachment_image(
			$id,
			$size,
			false,
			apply_filters(
				'woocommerce_gallery_image_html_attachment_image_params',
				array(
					'title'                   => _wp_specialchars( get_post_field( 'post_title', $id ), ENT_QUOTES, 'UTF-8', true ),
					'data-caption'            => _wp_specialchars( get_post_field( 'post_excerpt', $id ), ENT_QUOTES, 'UTF-8', true ),
					'data-src'                => esc_url( $full_src[0] ),
					'data-large_image'        => esc_url( $full_src[0] ),
					'data-large_image_width'  => esc_attr( $full_src[1] ),
					'data-large_image_height' => esc_attr( $full_src[2] ),
					'class'                   => esc_attr( $main_image ? 'wp-post-image' : '' ),
				),
				$id,
				$size,
				$main_image
			)
		);
		$output .= '</picture>';

		return $output;
	}

	/**
	 *
	 * Get the webp and original image
	 *
	 * @param int    $id ID of the attachment image.
	 * @param string $size Size of the image.
	 *
	 * @return object Object with paths and extension.
	 */
	public function get_images( $id, $size ) {
		$alt_image     = wp_get_attachment_image_src( $id, $size, false );
		$alt_image_src = $alt_image[0];
		$path          = pathinfo( $alt_image_src );
		$webp          = str_replace( $path['extension'], 'webp', $alt_image_src );

		return (object) array(
			'webp'      => $webp,
			'default'   => $alt_image_src,
			'extension' => $path['extension'],
		);
	}

	/**
	 *
	 * Replaces the woocommerce overview images
	 *
	 * @param string $image HTML of the original image.
	 * @param object $product Product Object.
	 * @param string $size Size of the image.
	 *
	 * @return string Newly generated image HTML.
	 */
	public function replace_woocommerce_image( $image, $product, $size ) {
		$image_id = $product->get_image_id();
		$images   = $this->get_images( $image_id, $size );

		return $this->get_webp_html( $images->webp, $images->default, $images->extension, $image_id, $size );
	}

	/**
	 *
	 * Replaces the product detail image
	 *
	 * @param string $html Original HTML.
	 * @param int    $id Attachment ID.
	 *
	 * @return string Newly generated HTML.
	 */
	public function product_gallery_main_image( $html, $id ) {

		$gallery_thumbnail = wc_get_image_size( 'gallery_thumbnail' );
		$images            = $this->get_images(
			$id,
			array(
				$gallery_thumbnail['width'],
				$gallery_thumbnail['height'],
			)
		);

		$html = '<div data-thumb="' . esc_url( $images->default ) . '" class="woocommerce-product-gallery__image"><a href="' . esc_url( $images->webp ) . '">' . $this->get_webp_html( $images->webp, $images->default, $images->extension, $id, 'woocommerce_single', 'wp-post-image' ) . '</a></div>';

		return $html;

	}
}
