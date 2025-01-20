<?php

function enqueue_admin_media_uploader_script($hook)
{
  wp_enqueue_media();
  wp_enqueue_script(
    'site-data-admin-script',
    get_template_directory_uri() . '/js/admin.js',
    ['jquery'],
    null,
    true
  );
}
add_action('admin_enqueue_scripts', 'enqueue_admin_media_uploader_script');

function add_site_data_admin_page()
{
  add_menu_page(
    'Site Data Manager',
    'Site Data',
    'manage_options',
    'site-data-manager',
    'render_site_data_page',
    'dashicons-admin-generic',
    100
  );
}
add_action('admin_menu', 'add_site_data_admin_page');

// Render the admin page
function render_site_data_page()
{
?>
  <div class="wrap">
    <h1>Site Data Manager</h1>
    <form method="post" action="options.php">
      <?php
      settings_fields('site_data_options');
      do_settings_sections('general');
      submit_button();
      ?>
    </form>
  </div>
  <?php
}

// Register settings and options page
function register_site_data_settings()
{
  // Register settings
  register_setting('site_data_options', 'header_name');
  register_setting('site_data_options', 'header_image');
  register_setting('site_data_options', 'footer_title');
  register_setting('site_data_options', 'social_links');
  register_setting('site_data_options', 'contact_phone');
  register_setting('site_data_options', 'contact_email');
  register_setting('site_data_options', 'theme_color');

  // Add settings section
  add_settings_section(
    'site_data_section',
    'Site Data Settings',
    function () {
      echo '<p>Manage site data for header, footer, and other settings.</p>';
    },
    'general'
  );

  // Add fields
  add_settings_field(
    'header_name',
    'Header Name',
    function () {
      $value = get_option('header_name', '');
      echo '<input type="text" name="header_name" value="' . esc_attr($value) . '" class="regular-text">';
    },
    'general',
    'site_data_section'
  );

  add_settings_field(
    'header_image',
    'Header Image',
    function () {
      $value = get_option('header_image', '');
  ?>
    <div>
      <img id="header_image_preview" src="<?php echo esc_url($value); ?>"
        style="max-width: 150px; display: <?php echo $value ? 'block' : 'none'; ?>;">
      <input type="hidden" name="header_image" id="header_image" value="<?php echo esc_url($value); ?>">
      <button type="button" class="button" id="upload_header_image">Select Image</button>
      <button type="button" class="button" id="remove_header_image"
        style="display: <?php echo $value ? 'inline-block' : 'none'; ?>;">Remove Image</button>
    </div>
<?php
    },
    'general',
    'site_data_section'
  );

  add_settings_field(
    'footer_title',
    'Footer Title',
    function () {
      $value = get_option('footer_title', '');
      echo '<input type="text" name="footer_title" value="' . esc_attr($value) . '" class="regular-text">';
    },
    'general',
    'site_data_section'
  );

  add_settings_field(
    'social_links',
    'Social Media Links (JSON)',
    function () {
      $value = get_option('social_links', '{}');
      echo '<textarea name="social_links" class="large-text code" rows="5">' . esc_textarea($value) . '</textarea>';
      echo '<p class="description">Enter social media links as JSON (e.g., {"facebook": "url", "twitter": "url"}).</p>';
    },
    'general',
    'site_data_section'
  );

  add_settings_field(
    'contact_phone',
    'Contact Phone',
    function () {
      $value = get_option('contact_phone', '');
      echo '<input type="text" name="contact_phone" value="' . esc_attr($value) . '" class="regular-text">';
    },
    'general',
    'site_data_section'
  );

  add_settings_field(
    'contact_email',
    'Contact Email',
    function () {
      $value = get_option('contact_email', '');
      echo '<input type="email" name="contact_email" value="' . esc_attr($value) . '" class="regular-text">';
    },
    'general',
    'site_data_section'
  );

  add_settings_field(
    'theme_color',
    'Theme Color',
    function () {
      $value = get_option('theme_color', '#000000');
      echo '<input type="color" name="theme_color" value="' . esc_attr($value) . '">';
    },
    'general',
    'site_data_section'
  );
}
add_action('admin_init', 'register_site_data_settings');

function register_site_data_api()
{
  register_rest_route('theme/v1', '/site-data', array(
    'methods'  => 'GET',
    'callback' => 'get_site_data',
    'permission_callback' => '__return_true', // Public access
  ));

  register_rest_route('theme/v1', '/site-data', array(
    'methods'  => 'POST',
    'callback' => 'update_site_data',
    'permission_callback' => function () {
      return current_user_can('manage_options');
    },
  ));
}
add_action('rest_api_init', 'register_site_data_api');

function get_site_data()
{
  $data = array(
    'header_name'  => get_option('header_name', ''),
    'header_image' => get_option('header_image', ''),
    'footer_title' => get_option('footer_title', ''),
    'social_links' => json_decode(get_option('social_links', '{}'), true),
    'contact_phone' => get_option('contact_phone', ''),
    'contact_email' => get_option('contact_email', ''),
    'theme_color'   => get_option('theme_color', '#000000'),
  );
  return rest_ensure_response($data);
}

function update_site_data(WP_REST_Request $request)
{
  $params = $request->get_json_params();

  if (isset($params['header_name'])) {
    update_option('header_name', sanitize_text_field($params['header_name']));
  }

  if (isset($params['header_image'])) {
    update_option('header_image', esc_url_raw($params['header_image']));
  }

  if (isset($params['footer_title'])) {
    update_option('footer_title', sanitize_text_field($params['footer_title']));
  }

  if (isset($params['social_links'])) {
    $social_links = json_encode($params['social_links']);
    update_option('social_links', $social_links);
  }

  if (isset($params['contact_phone'])) {
    update_option('contact_phone', sanitize_text_field($params['contact_phone']));
  }

  if (isset($params['contact_email'])) {
    update_option('contact_email', sanitize_email($params['contact_email']));
  }

  if (isset($params['theme_color'])) {
    update_option('theme_color', sanitize_hex_color($params['theme_color']));
  }

  return rest_ensure_response(array('message' => 'Site data updated successfully.'));
}
