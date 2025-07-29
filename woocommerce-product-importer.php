<?php
/**
 * Plugin Name:       Woocommerce Product Importer
 * Description:       Import products from various sources into your WooCommerce store.
 * Version:           1.0.0
 * Author:            nick bibby
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woocommerce-product-importer
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The core plugin class.
 */
require plugin_dir_path( __FILE__ ) . 'includes/core/product_importer.php';

/**
 * Begins execution of the plugin.
 *
 */
function run_the_plugin() {
	// Check if WooCommerce is active
	$plugin = new product_importer();
	$plugin->run();
}
add_action('plugins_loaded', 'run_the_plugin');

