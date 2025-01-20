<?php
require_once get_template_directory() . '/react.php';
require_once get_template_directory() . '/admin.php';


function my_custom_api_endpoint()
{
  register_rest_route('theme/v1', '/data', array(
    'methods'  => 'GET',
    'callback' => 'my_custom_data_callback',
  ));
}
add_action('rest_api_init', 'my_custom_api_endpoint');

function my_custom_data_callback()
{
  $data = array(
    'site_title' => get_bloginfo('name'),
    'message'    => 'Hello from WordPress API!',
  );

  return new WP_REST_Response($data, 200);
}

function register_theme_menus()
{
  register_nav_menus(array(
    'primary_menu' => __('Primary Menu', 'wp-speed'),
  ));
}
add_action('after_setup_theme', 'register_theme_menus');



function register_menu_to_api()
{
  register_rest_route('theme/v1', '/menu/(?P<location>[a-zA-Z0-9_-]+)', array(
    'methods'  => 'GET',
    'callback' => 'fetch_menu_items',
    'permission_callback' => '__return_true',
  ));
}
add_action('rest_api_init', 'register_menu_to_api');

function fetch_menu_items($data)
{
  $menu_location = $data['location'];
  $locations = get_nav_menu_locations();

  if (!isset($locations[$menu_location])) {
    return new WP_Error('no_menu', 'Menu not found', array('status' => 404));
  }

  $menu = wp_get_nav_menu_object($locations[$menu_location]);
  if (!$menu) {
    return new WP_Error('no_menu', 'Menu not found', array('status' => 404));
  }

  $menu_items = wp_get_nav_menu_items($menu->term_id);
  if (!$menu_items) {
    return [];
  }

  $items = [];
  foreach ($menu_items as $item) {
    $items[] = array(
      'id'    => $item->ID,
      'title' => $item->title,
      'url'   => $item->url,
      'parent' => $item->menu_item_parent,
      'order' => $item->menu_order,
    );
  }

  return rest_ensure_response($items);
}
