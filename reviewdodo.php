<?php
/**
 * Plugin Name: ReviewDodo - Customer engaging reviews
 * Plugin URI: https://reviewdodo.com
 * Description: Get started with ReviewDodo en increase review volume and score today
 * Version: 0.0.1
 * Author: Lightcube
 * Author URI: https://lightcube.nl
 */

include('settings.php');
include('embed.php');
include('ReviewController.php');

add_action('wp_enqueue_scripts', 'register_reviewdodo_scripts');
function register_reviewdodo_scripts()
{
  wp_enqueue_style('styles', plugins_url('widget/css/font-awesome-4.7.0.min.css', __FILE__));
  wp_enqueue_style('styles', plugins_url('widget/css/main.css', __FILE__));
  wp_enqueue_script('script', plugins_url('widget/js/script-min.js', __FILE__));
}

add_action('rest_api_init', function () {
  register_rest_route('reviewdodo', 'save', [
    'methods' => 'POST',
    'callback' => 'save_reviewdodo_review',
  ]);
});

add_action('woocommerce_thankyou', 'load_reviewdodo_widget');
function load_reviewdodo_widget($orderId)
{
  if (!$orderId) return;

  $reviewController = new ReviewController();
  $reviewContent = $reviewController->getOrCreateReview($orderId);

  if ($reviewContent->success) {
    $postUrl = $reviewController->getInternalApiUrl();
    include('widget/widget.php');
  }
}

add_action('wp_footer', 'load_reviewdodo_finish_widget');
function load_reviewdodo_finish_widget()
{
  $reviewHash = sanitize_text_field($_GET['review_hash']);
  if (!$reviewHash) {
    return;
  }

  $reviewController = new ReviewController();
  $reviewContent = $reviewController->getReviewContentByHash($reviewHash);

  if ($reviewContent->success) {
    $postUrl = $reviewController->getInternalApiUrl();
    include('widget/widget.php');
  }
}

function save_reviewdodo_review(WP_REST_Request $request)
{
  $reviewController = new ReviewController();
  return $reviewController->saveReview($request->get_params());
}
