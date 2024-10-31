<?php

function reviewdodo_shortcode_large($attributes)
{
  $limit = 10;
  $title = '';
  $subtitle = '';

  if (isset($attributes['limit'])) {
    $limit = $attributes['limit'];
  }

  if (isset($attributes['title'])) {
    $title = $attributes['title'];
  }

  if (isset($attributes['subtitle'])) {
    $subtitle = $attributes['subtitle'];
  }

  $cachePath = dirname(__FILE__) . '/cache/cache.json';

  if (file_exists($cachePath)) {
    $reviews = json_decode(file_get_contents($cachePath));
  } else {
    $reviews = (new ReviewController())->getReviews();
    file_put_contents($cachePath, json_encode($reviews));
  }

  $reviews = array_slice($reviews->reviews, 0, $limit);

  $content = '';

  $content .= '<h2> ' . $title . '</h2>';
  $content .= '<small> ' . $subtitle . '</small>';
  foreach ($reviews as $review):
    $reviewHtml = '<article class="col-md-3" itemscope itemtype="http://schema.org/Review">
      <span>' . $review->average_score_rounded . '</span>
    
      <div itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating">
        <meta itemprop="worstRating" content="1">
        <meta itemprop="bestRating" content="10">
        <meta itemprop="ratingValue" content="{{total_score}}">
    ';
    for ($i = 0; $i < 5; $i++) :
      $reviewHtml .= '<i class="fa fa-star"></i>';
    endfor;
    $reviewHtml .= '
      </div>
    
      <p itemprop="reviewBody">' . $review->single_line . '</p>
    
      <small>
        <meta itemprop="author">Anonieme klant</meta>
      </small>
    </article>';

    $content .= $reviewHtml;
  endforeach;

  echo $content;
}

add_shortcode('reviewdodo-large', 'reviewdodo_shortcode_large');
