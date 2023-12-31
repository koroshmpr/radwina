<?php

function register_new_search()
{
    register_rest_route('search/v1', 'search', array(
        'methods' => WP_REST_SERVER::READABLE,
        'callback' => 'GoldSearchResults'
    ));
}

function GoldSearchResults($data)
{
//    $termsTax = get_terms([
//        'taxonomy' => 'brand',
//        'name__like' => sanitize_text_field($data['term'])
//    ]);
//    $count = count($termsTax);

    $mainQuery = new WP_Query(array(
        'post_type' => array('post', 'product'),
        's' => sanitize_text_field($data['term'])
    ));
    $mainResults = array(
//        'blog' => array(),
        'product' => array(),
//        'brands' => array()
    );

//    if($count > 0){
//        foreach ($termsTax as $term) {
//            array_push($mainResults['brands'], array(
//                'title' => $term->name,
//                'url' => get_term_link($term),
//                'img' => get_field('brand_img',$term->taxonomy . '_' . $term->term_id)
//
//            ));
//        }
//    }
    while ($mainQuery->have_posts()) {
        $mainQuery->the_post();
//        if (get_post_type() == 'post') {
//            array_push($mainResults['blog'], array(
//                'title' => get_the_title(),
//                'url' => get_the_permalink(),
//                'img' => get_the_post_thumbnail_url(),
//            ));
//        }
        global $product;
        if (get_post_type() == 'product') {
            array_push($mainResults['product'], array(
                'title' => get_the_title(),
                'url' => get_the_permalink(),
                'img' => get_the_post_thumbnail_url(0, ''),
                'content' => get_the_content(),
                'price' => $product->get_regular_price()

            ));
        }

    }

    return $mainResults;
}

add_action('rest_api_init', 'register_new_search');