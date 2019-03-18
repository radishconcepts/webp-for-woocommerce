<?php
/**
 * Plugin Name: Radish WebP
 * Version: 1.0
 * Plugin URI: https://www.radishconcepts.com/radish-webp
 * Description: Replaces normal images with webp format
 * Author: Radish Concepts
 * Author URI: https://www.radishconcepts.com
 * Text Domain: radish-webp
 * Domain Path: /languages/
 * License: GPL v3
 *
 * @package    Radish_WebP
 * @author     Radish Concepts <info@radishconcepts.com>
 * @link       https://radishconcepts.com
 */

/**
 * Radish WebP
 * Copyright (C) 2018, Radish Concepts <info@radishconcepts.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Require all files with composer.
 */
require plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

new Radish_WebP\Replace_Images();
