<?php

add_action('init', 'reviewdodo_create_cp_review');
add_action('admin_menu', 'reviewdodo_add_admin_menu');
add_action('admin_init', 'reviewdodo_api_settings_init');
add_filter('manage_review_posts_columns', 'reviewdodo_set_custom_edit_review_columns');
add_action('manage_review_posts_custom_column', 'reviewdodo_custom_review_column', 10, 2);

function reviewdodo_add_admin_menu()
{
  add_options_page('ReviewDodo', 'ReviewDodo', 'manage_options', 'reviewdodo-api-page', 'reviewdodo_api_options_page');
}

function reviewdodo_api_settings_init()
{
  register_setting('reviewdodoPlugin', 'reviewdodo_api_settings');
  add_settings_section(
    'reviewdodo_api_reviewdodoPlugin_section',
    __('API key', 'wordpress'),
    'reviewdodo_api_settings_section_callback',
    'reviewdodoPlugin'
  );

  add_settings_field(
    'reviewdodo_api_key',
    __('Enter your API key', 'wordpress'),
    'reviewdodo_api_key_render',
    'reviewdodoPlugin',
    'reviewdodo_api_reviewdodoPlugin_section'
  );
}

function reviewdodo_set_custom_edit_review_columns($columns)
{
  unset($columns['comments']);
  unset($columns['date']);

  $columns['customer_name'] = __('Name', 'customer_name');
  $columns['order_reference'] = __('Order', 'order_reference');
  $columns['average_score_rounded'] = __('Score', 'average_score_rounded');
  $columns['single_line'] = __('Single line', 'single_line');
  $columns['free_text'] = __('Free text', 'free_text');

  return $columns;
}

function reviewdodo_custom_review_column($column, $post_id)
{
  switch ($column) {
    case 'customer_name' :
      echo get_post_meta($post_id, 'customer_name', true);
      break;

    case 'order_reference' :
      echo '<a href="' . admin_url('post.php?post=' . absint(get_post_meta($post_id, 'order_reference', true)) . '&action=edit') . '" >' . get_post_meta($post_id, 'order_reference', true) . '</a>';
      break;

    case 'average_score_rounded' :
      echo get_post_meta($post_id, 'average_score_rounded', true) . '/10';
      break;

    case 'single_line' :
      echo get_post_meta($post_id, 'single_line', true);
      break;

    case 'free_text' :
      echo get_post_meta($post_id, 'free_text', true);
      break;
  }
}

function reviewdodo_api_key_render()
{
  $options = get_option('reviewdodo_api_settings'); ?>
  <input type='text' name='reviewdodo_api_settings[reviewdodo_api_key]'
         value='<?php echo $options['reviewdodo_api_key']; ?>'>
  <?php
}

function reviewdodo_api_settings_section_callback()
{
  echo __('On this page, you can fill in your ReviewDodo API key. This API key connects your webshop to the ReviewDodo system and allows you to start collecting valuable reviews directly. <br/><br/> If you don\'t have an API key yet, please visit <a target="_blank" href="https://reviewdodo.com">https://reviewdodo.com</a> and signup.', 'wordpress');
}

function reviewdodo_api_options_page()
{
  collectAndAddReviews(); // First collect new reviews
  ?>
  <form action='options.php' method='post'>

    <h2>ReviewDodo</h2>

    <?php
    settings_fields('reviewdodoPlugin');
    do_settings_sections('reviewdodoPlugin');
    submit_button();
    ?>
  </form>
  <?php
}

function reviewdodo_create_cp_review()
{
  $labels = [
    'name' => _x('Reviews', 'post type general name'),
    'singular_name' => _x('Review', 'post type singular name'),
    'add_new' => _x('Add New', 'Review'),
    'add_new_item' => __('Add New Review'),
    'edit_item' => __('Edit Review'),
    'new_item' => __('New Review'),
    'all_items' => __('All Reviews'),
    'view_item' => __('View Review'),
    'search_items' => __('Search reviews'),
    'not_found' => __('No reviews found'),
    'not_found_in_trash' => __('No reviews found in the Trash'),
    'parent_item_colon' => '',
    'menu_name' => 'Reviews'
  ];

  $args = [
    'labels' => $labels,
    'description' => 'Displays all reviews given through ReviewDodo',
    'public' => true,
    'menu_position' => 30,
    'supports' => ['title', 'editor', 'thumbnail', 'excerpt', 'comments', 'custom-fields'],
    'has_archive' => true,
    'capabilities' => array(
      'create_posts' => 'create_review',
    ),
    'menu_icon' => 'dashicons-star-filled',
  ];

  register_post_type('review', $args);
}

function collectAndAddReviews()
{
  $reviewController = new ReviewController();
  $reviews = $reviewController->getReviews();

  foreach ($reviews->reviews as $review) {
    $args = [
      'post_type' => 'review',
      'meta_query' => [
        [
          'key' => 'review_hash',
          'value' => sanitize_text_field($review->hash)
        ]
      ],
      'fields' => 'ids'
    ];
    $reviews = new WP_Query($args);
    $reviewIds = $reviews->posts;

    if (empty($reviewIds)) {
      wp_insert_post([
        'post_title' => sanitize_email($review->customer_email),
        'post_type' => 'review',
        'post_content' => $review->free_text ? sanitize_text_field($review->free_text) : '',
        'post_status' => 'private',
        'comment_status' => 'closed',
        'ping_status' => 'closed',
        'date' => sanitize_text_field(date('Y-m-d H:i:s', strtotime($review->date))),

        'meta_input' => [
          'review_hash' => $review->hash ? sanitize_text_field($review->hash) : '',
          'customer_name' => $review->customer_name ? sanitize_text_field($review->customer_name) : '',
          'average_score_rounded' => $review->average_score_rounded ? sanitize_text_field($review->average_score_rounded) : 0,
          'single_line' => $review->single_line ? sanitize_text_field($review->single_line) : '',
          'free_text' => $review->free_text ? sanitize_text_field($review->free_text) : '',
          'order_reference' => $review->order_reference ? sanitize_text_field($review->order_reference) : '',
        ]
      ]);
    }
  }
}
