<?php

class RandomDataGenerator {

  static function get_random_user():int {
    $random_password = wp_generate_password();
    $random_username = bin2hex( openssl_random_pseudo_bytes(8) );
    $random_email = sprintf( '%s@%s.com', $random_username, $random_username );
    $user_id = wp_create_user( $random_username, $random_password, $random_email );
    return $user_id;
  }
  
  static function get_random_wholesaler( $user_id ):int {
    $term_query = new WP_Term_Query( [
      'taxonomy' => 'customtaxonomy',
      'fields' => 'ids',
      'hide_empty' => false,
    ] );
    $terms = $term_query->get_terms();
    $rand_term_id = self::get_random_term( $terms );
    $meta_input = [
      'is_shoptet' => rand( 0, 1 ),
      'category' => $rand_term_id,
      '_category' => 'field_5b5ed5a9ddd56',
      'country' => 'cz',
      '_country' => 'field_5bbdc26030686',
      'region' => rand( 0, 13 ),
      '_region' => 'field_5b5ed2ca0a22d',
      'project_title' =>	'Project #' . $user_id,
      '_project_title' =>	'field_5d39e3b8467ea',
      'street' =>	'Oblouková',
      '_street' =>	'field_5b5ec9b4052f8',
      'city' =>	'Praha',
      '_city' =>	'field_5b5eca63052f9',
      'zip' =>	'10100',
      '_zip' =>	'field_5b5eca9d052fa',
      'in' =>	'25341308',
      '_in' =>	'field_5b5ecaf4052fb',
      'tin' =>	'',
      '_tin' =>	'field_5b5ecc9d052fc',
      'website' =>	'https://www.google.cz',
      '_website' =>	'field_5b5eccbb052fd',
      'facebook' =>	'',
      '_facebook' =>	'field_5b5ecd01052fe',
      'twitter' => '',
      '_twitter' => 'field_5b5ecd0f052ff',
      'instagram' => '',
      '_instagram' => 'field_5b854f2feaf16',
      'logo' => '',
      '_logo' => 'field_5b5ed8dcce814',
      '_is_shoptet' => 'field_5b86c6c02b205',
      'contact_full_name' => 'John Doe',
      '_contact_full_name' => 'field_5b5ed477147d7',
      'contact_email' => 'jk.oolar@gmail.com',
      '_contact_email' => 'field_5b5ed49d147d8',
      'contact_tel' => '666666666',
      '_contact_tel' => 'field_5b5ed4d3147d9',
      'contact_photo' => '',
      '_contact_photo' => 'field_5b5ed50b147da',
      'minor_category_1' => '',
      '_minor_category_1' => 'field_5bff0ff2793a9',
      'minor_category_2' => '',
      '_minor_category_2' => 'field_5bff1029793aa',
      'short_about' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
      '_short_about' => 'field_5b5ed640ddd57',
      'services' =>	["Shoptet XML feed","Dárky k větším objednávkám"],
      '_services' =>	'field_5b5ed686ddd58',
      'about_company' =>	'',
      '_about_company' =>	'field_5b5ed759ddd59',
      'about_products' =>	'',
      '_about_products' =>	'field_5b5ed775ddd5a',
      'gallery' =>	'',
      '_gallery' =>	'field_5b5f0597506c8',
      'video' =>	'',
      '_video' =>	'field_5b5f05c8506c9',
    ];
    $wholesaler_id = wp_insert_post([
      'post_type' => 'custom',
      'post_title' => 'Wholesaler #' . $user_id,
      'post_status' => 'publish',
      'post_author' => $user_id,
      'meta_input' => $meta_input,
    ]);
    wp_set_post_terms( $wholesaler_id, [$rand_term_id], 'customtaxonomy' );
    stop_the_insanity();  
    return $wholesaler_id;
  }
  
  static function generate_wholeasers( $wholesalers, $products_per_wholesaler ) {
    $wholesalers_num = $wholesalers;
    $products_per_wholesaler = $products_per_wholesaler;
    for( $i = 0; $i < $wholesalers_num; $i++ ) {
      $user_id = self::get_random_user();
      $wholesaler_id = self::get_random_wholesaler( $user_id );
      $wholesalers_by_user_id[$user_id] = $wholesaler_id;
      self::generate_random_products( $products_per_wholesaler, $wholesaler_id, $user_id );
    }
  }
  
  static function generate_random_attachments( $attachments_num, $product_id ) {
    require_once( ABSPATH . 'wp-admin/includes/image.php' );
    $wp_upload_dir = wp_upload_dir();
    $filename = $wp_upload_dir['basedir'] . '/dummy.png';
    $attachment = [
      'post_mime_type' => 'image/png',
      'post_title'     => 'Attachment',
      'post_content'   => '',
      'post_status'    => 'inherit'
    ];
    for( $i = 0; $i < $attachments_num; $i++ ) {
      $attach_id = wp_insert_attachment( $attachment, false, $product_id );
      $attach_data = wp_generate_attachment_metadata( $attach_id, '' );
      wp_update_attachment_metadata( $attach_id, $attach_data );
      //set_post_thumbnail( $product, $attach_id );
    }
  }
  
  static function get_random_term( $terms ) {
    $random_term = $terms[array_rand($terms)];
    return $random_term;
  }
  
  static function generate_random_products( $products_num, $wholesaler_id, $user_id ) {
    global $wpdb;
    $term_query = new WP_Term_Query( [
      'taxonomy' => 'producttaxonomy',
      'fields' => 'ids',
      'hide_empty' => false,
    ] );
    $terms = $term_query->get_terms();
    $site_url = get_site_url();
    $date = date('Y-m-d H:i:s');
    $gmdate = gmdate('Y-m-d H:i:s');
    for( $i = 0; $i < $products_num; $i++ ) {
      $name = bin2hex( openssl_random_pseudo_bytes(8) );
      $guid = $site_url . '/?post_type=product&#038;p=' . $product_id;
      $rand_term_id = self::get_random_term( $terms );
      $price = rand(99,9999);
      $min_order = rand(0,50);
      $wpdb->insert(
        $wpdb->posts,
        [
          'post_author' => $user_id,
          'post_date' => $date,
          'post_date_gmt' => $gmdate,
          'post_content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
          'post_title' => 'Product ' . $name,
          'post_status' => 'publish',
          'comment_status' => 'closed',
          'ping_status' => 'closed',
          'post_name' => $name,
          'post_modified' => $date,
          'post_modified_gmt' => $gmdate,
          'guid' => $guid,
          'post_type' => 'product',
        ]
      );
      $product_id = $wpdb->insert_id;
      $thumbnail_name = 't' . $name;
      $thumbnail_guid = $site_url . '/wp-content/uploads/dummy.png?' . $thumbnail_name;
      $wpdb->insert(
        $wpdb->posts,
        [
          'post_author' => $user_id,
          'post_date' => $date,
          'post_date_gmt' => $gmdate,
          'post_title' => 'Thumbnail ' . $thumbnail_name,
          'post_status' => 'inherit',
          'comment_status' => 'closed',
          'ping_status' => 'closed',
          'post_name' => $thumbnail_name,
          'post_modified' => $date,
          'post_modified_gmt' => $gmdate,
          'guid' => $thumbnail_guid,
          'post_type' => 'attachment',
          'post_parent' => $product_id,
          'post_mime_type' => 'image/png',
        ]
      );
      $thumbnail_id = $wpdb->insert_id;
      $wpdb->query("
        INSERT INTO $wpdb->postmeta
        (post_id,meta_key,meta_value)
        VALUES
        ($thumbnail_id,'_wp_attached_file','dummy.png'),
        ($thumbnail_id,'_wp_attachment_metadata','a:5:{s:5:\"width\";i:1;s:6:\"height\";i:1;s:4:\"file\";s:17:\"dummy.png\";s:5:\"sizes\";a:0:{}s:10:\"image_meta\";a:12:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\";s:6:\"camera\";s:0:\";s:7:\"caption\";s:0:\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}}}'),
        ($product_id,'related_wholesaler',$wholesaler_id),
        ($product_id,'_related_wholesaler','field_5c7d1fbf2e01c'),
        ($product_id,'category',$rand_term_id),
        ($product_id,'_category','field_5cc6fbe565ff6'),
        ($product_id,'short_description','Lorem ipsum dolor sit amet, consectetur adipiscing elit.'),
        ($product_id,'_short_description','field_5c7d1d41a01b2'),
        ($product_id,'price',$price),
        ($product_id,'_price','field_5c7d1d6aa01b3'),
        ($product_id,'minimal_order',''),
        ($product_id,'_minimal_order','field_5c7d1f09c3f47'),
        ($product_id,'ean','7501031311309'),
        ($product_id,'_ean','field_5cbf069f3ae2d'),
        ($product_id,'description','asdfasdf'),
        ($product_id,'_description','Lorem ipsum dolor sit amet, consectetur adipiscing elit.'),
        ($product_id,'_thumbnail_id',$thumbnail_id),
        ($product_id,'thumbnail',$thumbnail_id),
        ($product_id,'_thumbnail','field_5c7d203dd6c7b'),
        ($product_id,'gallery',''),
        ($product_id,'_gallery','field_5c7d1f71c3f48')
      ");
      $wpdb->insert(
        $wpdb->term_relationships,
        [
          'object_id' => $product_id,
          'term_taxonomy_id' => $rand_term_id,
        ]
      );
    }
  }
  
  static function generate( $wholesalers, $products_per_wholesaler ): void
  {
    $wholesalers_by_user_id = self::generate_wholeasers( $wholesalers, $products_per_wholesaler );
  }

}