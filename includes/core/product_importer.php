<?php

class product_importer
{
    public function run()
    {
        //Check for woocommer installation
        if (!class_exists('WooCommerce')) {
            // warning message if WooCommerce is not installed
            add_action('admin_notices', function () {
                echo '<div class="notice notice-error"><p>' . __('WooCommerce is not installed or activated. Please install WooCommerce to use the Href Product Importer.', 'href-product-importer') . '</p></div>';
            });

            //deactivate the plugin
            deactivate_plugins('href-product-importer/href-product-importer.php');

            // Exit the function if WooCommerce is not installed    
            return;
        }

        require_once plugin_dir_path(__FILE__) . '/woocommerce_settings_page.php';
        // Add the menu item for the plugin
        $settings_page = new woocommerce_settings_page();
        $settings_page->create();

        require_once plugin_dir_path(__FILE__) . '/process_api_request.php';
        // process the api request
        $process_input = new process_api_request();
        $process_input->init();
    }
}