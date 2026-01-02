<?php

/**
 * Plugin Name: Healsend Products CPT
 * Description: Registers Healsend Products CPT with SEO-friendly URLs
 */

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Register Healsend Products CPT
 */
function healsend_register_products_cpt()
{

    $labels = array(
        'name'               => 'Healsend Products',
        'singular_name'      => 'Healsend Product',
        'add_new'            => 'Add Product',
        'add_new_item'       => 'Add New Product',
        'edit_item'          => 'Edit Product',
        'new_item'           => 'New Product',
        'view_item'          => 'View Product',
        'search_items'       => 'Search Products',
        'not_found'          => 'No products found',
        'menu_name'          => 'Healsend Products',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'show_in_rest'       => true, // REQUIRED for Elementor + ACF
        'has_archive'        => false,
        'rewrite'            => array(
            'slug'       => 'products/%healsend_category%',
            'with_front' => false
        ),
        'supports'           => array(
            'title',
            'editor',
            'thumbnail',
            'excerpt'
        ),
    );

    register_post_type(HLD_PRODUCT_POST_TYPE, $args);
}
add_action('init', 'healsend_register_products_cpt');

/**
 * Register Category Taxonomy (Weight Loss, Sexual Health)
 */
function healsend_register_product_taxonomy()
{

    $labels = array(
        'name'          => 'Product Categories',
        'singular_name' => 'Product Category',
    );

    register_taxonomy('healsend_category', HLD_PRODUCT_POST_TYPE, array(
        'labels'            => $labels,
        'public'            => true,
        'hierarchical'      => true,
        'show_in_rest'      => true,
        'rewrite'           => array(
            'slug'       => 'products',
            'with_front' => false
        ),
    ));
}
add_action('init', 'healsend_register_product_taxonomy');

/**
 * Replace %healsend_category% with actual category slug in URL
 */
function healsend_product_permalink($post_link, $post)
{

    if ($post->post_type !== HLD_PRODUCT_POST_TYPE) {
        return $post_link;
    }

    $terms = get_the_terms($post->ID, 'healsend_category');

    if (! empty($terms) && ! is_wp_error($terms)) {
        return str_replace('%healsend_category%', $terms[0]->slug, $post_link);
    }

    return str_replace('%healsend_category%', 'product', $post_link);
}
add_filter('post_type_link', 'healsend_product_permalink', 10, 2);
