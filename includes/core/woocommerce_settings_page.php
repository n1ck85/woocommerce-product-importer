<?php 

class woocommerce_settings_page
{
    public function create()
    {
        add_filter('woocommerce_get_sections_products', [$this, 'import_products_section'], 99);
        add_filter('woocommerce_get_settings_products', [$this, 'import_products_content'], 10, 2);
        add_action('woocommerce_admin_field_import_button', [$this, 'render_import_button'], 99);
        add_action('woocommerce_update_options_products', [$this, 'save_import_url']);
    }

    public function save_import_url()
    {
        if (isset($_POST['api_url'])) {
            $api_url = sanitize_text_field($_POST['api_url']);
            update_option('href_product_importer_api_url', $api_url);
        }
    }

    public function import_products_section($sections)
    {
        $sections['import_products'] = __('Import Products', 'href-product-importer');

        return $sections;
    }

    public function import_products_content($content, $current_section)
    {
        if ($current_section === 'import_products') {
            $content = [
                [
                    'name' => __('Import Products', 'href-product-importer'),
                    'type' => 'title',
                    'desc' => __('Use this for importing products from an api.', 'href-product-importer'),
                    'id' => 'import_products_options',
                ],
                [
                    'name' => __('Import URL', 'href-product-importer'),
                    'type' => 'text',
                    'id' => 'api_url',
                    'placeholder' => __('Enter API URL', 'href-product-importer'),
                ],
                [
                    'type' => 'import_button',
                    'id'   => 'import_products_button',
                ],
                [
                    'type' => 'sectionend',
                    'id'   => 'import_products_section_end',
                ],
            ];
        }

        return $content;
    }

    public function render_import_button()
    {
        echo 
        '<tr valign="top">
            <th scope="row"></th>
            <td>
                <button type="button" class="button button-primary" id="import_products_button">'
                    . esc_html__('Import Products', 'href-product-importer') .
                '</button>
            </td>
        </tr>';
    }
}