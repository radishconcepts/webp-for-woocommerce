<?php
/**
 * Front end facing functionality
 *
 * @package    Radish_WebP
 */

namespace Radish_WebP;

/**
 * Enqueue Scripts
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
class Enqueue_Scripts {
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
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 50 );
	}

	/**
	 * Enqueues Front-end facing scripts
	 */
	public function enqueue_scripts() {
		//wp_enqueue_style( 'webp-for-woocommerce', WebP_for_WC_URL . 'assets/css/webp-for-woocommerce.' . ( ( ! defined( 'SCRIPT_DEBUG' ) || ! SCRIPT_DEBUG ) ? 'min.' : '' ) . 'css', array() );
		wp_enqueue_script( 'webp-for-woocommerce', WebP_for_WC_URL . 'assets/js/webp-for-woocommerce.' . ( ( ! defined( 'SCRIPT_DEBUG' ) || ! SCRIPT_DEBUG ) ? 'min.' : '' ) . 'js', array(), WebP_for_WC_VERSION, true );
	}
}
