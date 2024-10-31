<?php

class ReviewController
{
  private $apiUrl;

  /**
   * WidgetController constructor.
   */
  public function __construct()
  {
    $this->apiUrl = 'https://reviewdodo.com/api/';
  }

  public function getOrCreateReview($orderId)
  {
    $order = wc_get_order($orderId);

    $data = [
      'customer_name' => $order->billing_first_name,
      'customer_email' => $order->billing_email,
      'order_reference' => $order->get_id(),
    ];

    return $this->doRequest('getReviewContent', 'POST', $data);
  }

  public function getReviewContentByHash($reviewHash)
  {
    return $this->doRequest('getReviewContentByHash', 'POST', ['review_hash' => $reviewHash]);
  }

  public function saveReview($params)
  {
    return $this->doRequest('submit', 'POST', $params);
  }

  public function getReviews()
  {
    return $this->doRequest('reviews', 'GET');
  }

  public function getApiUrl()
  {
    return $this->apiUrl;
  }

  public function getInternalApiUrl()
  {
    return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/wp-json/reviewdodo/save";
  }

  private function doRequest($endpoint, $method = 'POST', $data = [])
  {
    $options = get_option('reviewdodo_api_settings');
    $url = $this->apiUrl . $endpoint;

    $args = [
      'headers' => [
        'Authorization' => 'Bearer ' . $options['reviewdodo_api_key']
      ],
      'timeout' => 5
    ];

    if ($method === 'POST') {
      $args['body'] = $data;
      $response = wp_remote_post($url, $args);
    } else {
      $response = wp_remote_get($url, $args);
    }

    $httpCode = wp_remote_retrieve_response_code($response);

    if ($httpCode === 200) {
      $body = wp_remote_retrieve_body($response);
      return json_decode($body);
    }

    return false;
  }
}
