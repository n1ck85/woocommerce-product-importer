<?php
class process_api_request
{
    public function init()
    {
        add_action('admin_enqueue_scripts', [$this, 'register_scripts'], 10, 1);
        add_action('wp_ajax_href-import-script', [$this, 'handle_import_request']);
    }

    public function register_scripts($hook)
    {
        if ($hook === 'woocommerce_page_wc-settings') {
            wp_enqueue_script(
                'href-import-script',
                plugins_url('includes/js/import-products.js', dirname(__DIR__)),
                ['jquery'],
                null,
                true
            );
            wp_localize_script('href-import-script', 'href_import_script', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce'    => wp_create_nonce('href_import_nonce'),
            ]);
        }
    }

    public function handle_import_request()
    {
        check_ajax_referer('href_import_nonce', 'nonce');

        $valid_url = $this->process_api_url($_POST['api_url'] ?? '');

        if (!$valid_url instanceof WP_Error) {
            //process the request
            $data = $this->process_api_request($valid_url);
        } else {
            wp_send_json_error(__('API URL is required.', 'href-product-importer'));
            return;
        }

        if (is_wp_error($data)) {
            wp_send_json_error($data->get_error_message());
            return;
        }

        if (empty($data) || !is_array($data)) {
            wp_send_json_error(__('No valid data returned from the API.', 'href-product-importer'));
            return;
        }

        // create woocommerce products from the data
        require_once plugin_dir_path(__FILE__) . '/woocommerce_product_creator.php';
        $product_creator = new woocommerce_product_creator();
        $result = $product_creator->process_product_data($data);

        wp_send_json_success($data);
    }

    public function process_api_url($api_url)
    {
        // Validate and sanitize the API URL
        $api_url = filter_var($api_url, FILTER_SANITIZE_URL);
        if (!filter_var($api_url, FILTER_VALIDATE_URL)) {
            return new WP_Error('invalid_url', __('Invalid API URL provided.', 'href-product-importer'));
        }

        return $api_url;
    }

    public function process_api_request($api_url)
    {
        $response = wp_remote_get($api_url);

        if (is_wp_error($response)) {
            return $response;
        }

        $body = wp_remote_retrieve_body($response);
        
        return json_decode($body, true);
    }
}
