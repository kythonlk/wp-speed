<?php
require_once get_template_directory() . '/react.php';


function my_custom_api_endpoint()
{
  register_rest_route('api-1/v1', '/data', [
    'methods' => 'GET',
    'callback' => 'my_custom_data_callback',
  ]);
}
add_action('rest_api_init', 'my_custom_api_endpoint');

function my_custom_data_callback()
{
  // You can fetch some custom data, for example, the site title
  $data = array(
    'site_title' => get_bloginfo('name'),
    'message' => 'Hello from WordPress API!',
  );

  // Return the data as JSON
  return new WP_REST_Response($data, 200);
}
