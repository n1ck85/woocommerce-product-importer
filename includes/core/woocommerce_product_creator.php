<?php

class woocommerce_product_creator
{
    public function process_product_data($data)
    {
        // map the data to the WooCommerce product fields
        $products = $this->data_mapper($data);

        $this->create_products($products);
    }

    private function create_products($products)
    {

        foreach ($products as $item) {

            // Check for duplicates
            if ($product_id = wc_get_product_id_by_sku($item['id'])) {
                // If product with this SKU already exists, skip it
                $product = wc_get_product($product_id);
                error_log('Product with SKU ' . $item['id'] . ' already exists. Skipping creation.');
            } else {
                // If product does not exist, create a new one
                $product = new WC_Product();
                // Set API ID as SKU in the absence of an actual SKU so we can check for duplicates.
                $product->set_sku($item['id']);

                // Save the product image to media library as it's a new product and use that
                if (!empty($item['image_url'])) {
                    $image_id = media_sideload_image($item['image_url'], 0, null, 'id');
                    if (!is_wp_error($image_id)) {
                        $product->set_image_id($image_id);
                    } else {
                        error_log('Error uploading image: ' . $image_id->get_error_message());
                    }
                }
            }

            // Set product data
            $product->set_name($item['name']);
            $product->set_regular_price($item['price']);
            $product->set_description($item['description']);
            if (!term_exists($item['category'], 'product_cat')) {
                //create a new category if it does not exist
                $category = wp_insert_term($item['category'], 'product_cat');
                $product->set_category_ids([$category['term_id']]);
            }
            else {
                //assign existing category
                $category = get_term_by('name', $item['category'], 'product_cat');
                $product->set_category_ids([$category->term_id]);
            }
            $product->set_average_rating($item['rating']);
            // $product->set_review_count($item['rating_count']);

            // Save the product
            $product->save();
        }
    }
    
    private function data_mapper($data)
    {
        $mapped_data = [];

        foreach ($data as $item) {
            try {
                $mapped_data[] = [
                    'id'            => $item['id'],
                    'name'          => $item['title'],
                    'price'         => $item['price'],
                    'description'   => $item['description'],
                    'category'      => $item['category'] ?? '',
                    'rating'        => $item['rating']['rate'] ?? 0,
                    'rating_count'  => $item['rating']['count'] ?? 0,
                    'image_url'     => $item['image'],
                ];
            } catch (Exception $e) {
                return new WP_Error('error_mapping_item', 'Error mapping item: ' . $e->getMessage());
            }
        }

        if (empty($mapped_data)) {
            return new WP_Error('no_data_mapped', __('No data mapped from the API response.', 'href-product-importer'));
        }

        return $mapped_data;
    }            
}