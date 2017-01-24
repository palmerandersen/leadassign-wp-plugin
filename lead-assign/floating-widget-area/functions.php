<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// This file includes all the functions. Nice!

// loads anything needed on normal pages that is not a script or style
function floating_load_resources() {
  global $floating_setting_image_url_name;
  // load image using url found in database for src
  $icon = get_option($floating_setting_image_url_name);
  echo "<img id='floating-widget-area-icon' class='floating-widget-area-icon' src='$icon'/>";
  // when js is disabled, display the appropriate style
  echo '<noscript><link rel="stylesheet" href="'.plugins_url('/jsdisabledstyle.css',__FILE__).'"</noscript>';
}

// loads style for non-admin pages
function floating_load_styles() {
  // load main style sheet
  wp_enqueue_style('floating-stylesheet-css',plugins_url('/style.css',__FILE__));
}

// loads items used on every page (admin and non-admin)
function floating_load_universal_items() {
  // load universal style sheet
  wp_enqueue_style('floating-stylesheet-universal-css',plugins_url('/universalstyle.css',__FILE__));
  // load jquery (all problems solved)
  wp_enqueue_script('jquery');
}

// creates menu, icon, on non-admin pages (when widget area is not empty)
function floating_createDiv() {
  if (is_admin()) return;
  // do not create sidebar if it contains no widgets
  if (!is_active_sidebar('floating-widget-area-menu')) return;

  // load sidebar and other things
  floating_create_dynamic_sidebar();
  floating_load_resources();
  // load main script
  wp_enqueue_script('floating-main-script-js',plugins_url('/floating_main_script.js',__FILE__));

  global $floating_setting_min_width_name;
  // carry over some php vars to JS
  $browserMinWidth = get_option($floating_setting_min_width_name);
  $script =
  "var floating_widget_area=floating_widget_area || {};
  floating_widget_area.setBrowserMinWidth($browserMinWidth);";
  wp_add_inline_script('floating-main-script-js',$script);
  return;
}

// add settings link on plugins page
// this is the link that is found on this plugin's entry in the list of plugins
function floating_add_settings_link( $links ) {
  global $floating_settings_name;
  // create local link using plugin name
  $admin_url = admin_url();
  $settings_link = '<a href="${admin_url}options-general.php?page='.$floating_settings_name.'">' . __( 'Settings' ) . '</a>';
  // add said link to the array of links for this plugin's actions
  array_push( $links, $settings_link );
  return $links;
}

// prints HTML for settings page header
function floating_settings_page() {
  global $floating_settings_name;
  // settings_fields($name) sets up settings for use
  // do_settings_sections($name) renders sections
  ?>
  <div>
    <form method="post" action="options.php">
      <?php settings_fields( $floating_settings_name ); ?>
      <?php do_settings_sections( $floating_settings_name ); ?>

      <input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
    </form>
  </div>
  <?php
}

// adds menu for this plugin to main settings menu in admin panel
function floating_create_menu() {
  global $floating_settings_name;
  // add_options_page( $page_title, $menu_title, $capability, $menu_slug, $function);
  add_options_page("Floating Widget Area Settings",
  "Floating Widget Area",
  "manage_options",
  $floating_settings_name,
  "floating_settings_page");
}

// loads scripts used on this plugin's admin page
function floating_admin_scripts() {
  // scripts used for the media selector function of the image url setting
  wp_enqueue_script('media-upload');
  wp_enqueue_script('thickbox');

  // general admin script for this plugin
  wp_enqueue_script('floating-admin-script',plugins_url('/floating_admin_script.js',__FILE__));
}

// loads styles used on this plugin's admin page
function floating_admin_styles() {
  // style used for the media selector
  wp_enqueue_style('thickbox');

  // general admin stylsheet for this plugin
  wp_enqueue_style('floating-admin-stylesheet',plugins_url('/adminstyle.css',__FILE__));
}

// registers settings for use with settings page
function floating_register_settings() {
  global $floating_settings_name,
  $floating_setting_min_width_name,$floating_setting_min_width_id,
  $floating_setting_image_url_name,$floating_setting_image_url_id;

  // settings registered individually
  // register_setting( string $option_group, string $option_name, callable $sanitize_callback = '' );
  register_setting($floating_settings_name,$floating_setting_min_width_name,"floating_min_width_validate");
  register_setting($floating_settings_name,$floating_setting_image_url_name,"floating_image_url_validate");

  // all settings under one section
  // add_settings_section( $id, $title, $callback, $page );
  add_settings_section($floating_settings_name,
  "Floating Widget Area Settings",
  "floating_settings_section",
  $floating_settings_name);

  // all fields added to said section
  // add_settings_field( $id, $title, $callback, $page, $section, $args );
  // id is used to find the element containing the setting data
  // name is used to save said data to the correct option
  // the correct option must then be set into the element to contain setting data...
  // ... on page load

  // field for image url
  add_settings_field($floating_setting_image_url_id,
  "Icon image",
  "floating_image_setting_shortcode",
  $floating_settings_name,
  $floating_settings_name
  );
  // field for min width
  add_settings_field($floating_setting_min_width_id,
  'Minimum width',
  'floating_min_width_render',
  $floating_settings_name,
  $floating_settings_name);
}

// prints HTML for min width setting
function floating_min_width_render() {
  global $floating_setting_min_width_id,$floating_setting_min_width_name;
  $option = get_option($floating_setting_min_width_name);
  echo "<input type='text' id='$floating_setting_min_width_id' name='$floating_setting_min_width_name' value='$option' />";
  echo "<br />";
  echo "The minimum browser width (in pixels) required to see the icon";
}

// validates min width setting
function floating_min_width_validate($input) {
  // value must be integer
  $input = intval($input);
  return $input;
}

// validates image url setting
function floating_image_url_validate($input) {
  // value must be a url
  $input = esc_url_raw($input);
  // esc_url_raw used because value will be inserted into a database
  // see: https://codex.wordpress.org/Data_Validation#URLs
  return $input;
}

// prints HTML for image url setting
function floating_image_setting_shortcode() {
  global $floating_setting_image_url_name,$floating_setting_image_url_id;
  $option = get_option($floating_setting_image_url_name);
  // in addition to the normal text box, an image preview is present here.
  // it doesn't matter to the setting,
  // but it's src is set so people can see what their icon looks like
  ?>
  <input id="<?php echo $floating_setting_image_url_id ?>" type="text" size="36"
  name="<?php echo $floating_setting_image_url_name ?>" value="<?php echo $option ?>" />
  <input id="upload_image_button" type="button" value="Upload Image" />
  <br />
  <img id = "floating-widget-area-image-preview" class='floating-widget-area-icon' src = "<?php echo $option ?>" />
  <br />Enter an URL or upload an image for the icon.
  <br />Leave blank to reset to default icon.
  <?php
}

// prints HTML for settings section header
function floating_settings_section() {
  echo "<p>Configure your floating widget area</p>";
  echo "<p>You can set the content of the widget area at Appearance -> Widgets</p>";
}

// adds options through Wordpress options API
function floating_widget_area_activate() {
  global $floating_setting_image_url_name,$floating_setting_min_width_name;
  // trim http:// from default icon url so that it is compatible with both HTTP and HTTPS
  $input = plugins_url('default-icon.png',__FILE__);
  $pos = strpos($input,'://');
  if (!($pos===false)) { // semicolon found
    $input = substr($input,$pos+1);
  }
  add_option($floating_setting_image_url_name,$input);
  add_option($floating_setting_min_width_name,'500');
}

// used for options which should reset when blank
function floating_clear_blank_value($option_name) {
  if (get_option($option_name)=="") {
    delete_option($option_name);
  }
}

// registers widget area for admin menu and dynamic sidebar
function floating_register_widget_area() {
  register_sidebar( array(
  'name'          => 'Floating Widget Area',
  'id'            => 'floating-widget-area-menu',
  'before_widget' => '<div>',
  'after_widget'  => '</div>',
  'before_title'  => '<h2 class="rounded">',
  'after_title'   => '</h2>',
) );
}

// creates floating-widget-area-menu, and adds widgets
function floating_create_dynamic_sidebar() {
  echo "<div id = floating-widget-area-menu>";
  // reference to sidebar registered in floating_register_widget_area
  dynamic_sidebar('Floating Widget Area');
  echo "</div>";
}

// check if the default contact form for this plugin is in the database. If it isn't, create it
function floating_check_for_contact_form() {
  global $wpdb,$floating_default_contact_form_name,$floating_default_contact_form_content;
  $table_name = $wpdb->prefix.'posts';
  $results = $wpdb->get_results("SELECT * FROM $table_name WHERE post_type='wpcf7_contact_form' AND post_title='$floating_default_contact_form_name';",OBJECT);
  // nothing found
  if (count($results)==0) {
    // create the default
    // echo "Floating widget area: Generating default contact form";
    // build array for row insert
    $post_id = wp_insert_post(
      array(
        // 'post_content'=>$floating_default_contact_form_content,
        'post_title'=>$floating_default_contact_form_name,
        'post_type'=>'wpcf7_contact_form',
        'post_status'=>'publish',
        'post_name'=>'contact-form-1'
      )
    );
    // add all the post meta (where wpcf7 actually pulls things from)
    // get all our globals
    global $floating_default_contact_form_form,
    $floating_default_contact_form_mail,
    $floating_default_contact_form_locale,
    $floating_default_contact_form_mail_2,
    $floating_default_contact_form_messages,
    $floating_default_contact_form_additional_settings;
    // holy globals batman

    // if post was created succesfully
    if ($post_id!=0) {
      // add all this data and link it to the post
      // each of these meta values can only be set once per contact form;
      // they are unique
      $data_is_unique = True;
      add_post_meta($post_id , '_form' , $floating_default_contact_form_form , $data_is_unique);
      add_post_meta($post_id , '_mail' , $floating_default_contact_form_mail , $data_is_unique);
      add_post_meta($post_id , '_mail_2' , $floating_default_contact_form_mail_2 , $data_is_unique);
      add_post_meta($post_id , '_messages' , $floating_default_contact_form_messages , $data_is_unique);
      add_post_meta($post_id , '_additional_settings' , $floating_default_contact_form_additional_settings , $data_is_unique);
      add_post_meta($post_id , '_locale' , $floating_default_contact_form_locale , $data_is_unique);
    }
  }
  else {
    // echo "Floating widget area: Default Contact Form found";
  }
}
