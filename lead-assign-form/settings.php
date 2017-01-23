<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// prints HTML for settings page header
function leadassign_settings_page() {
  global $leadassign_settings_name;
  // settings_fields($name) sets up settings for use
  // do_settings_sections($name) renders sections
  $logo_url = plugins_url('img/logo.png',__FILE__);
  ?>
  <img src="<?php echo $logo_url;?>" />
  <p><?php _e("This plugin sends contact form data straight to your lead assign account.
    Find out more at <a href='https://leadassign.com'>Lead Assign</a>"); ?>
  </p>
  <div>
    <form method="post" action="options.php">
      <?php settings_fields( $leadassign_settings_name ); ?>
      <?php do_settings_sections( $leadassign_settings_name ); ?>
      <br>
      <?php submit_button(); ?>
    </form>
  </div>
  <?php
}

// registers settings for use with settings page
function leadassign_register_settings() {
  global $leadassign_settings_name;
  global $leadassign_setting_do_style_id, $leadassign_setting_do_style_name;
  global $leadassign_setting_surpress_warnings_id, $leadassign_setting_surpress_warnings_name;
  // settings registered individually
  // register_setting( string $option_group, string $option_name, callable $sanitize_callback = '' );
  register_setting($leadassign_settings_name,$leadassign_setting_surpress_warnings_name,'leadassign_setting_do_style_sanitize');
  register_setting($leadassign_settings_name,$leadassign_setting_do_style_name,'leadassign_setting_do_style_sanitize');

  // add_settings_section( $id, $title, $callback, $page );
  add_settings_section(
    'leadassign_settings_status_section',
    __("Status",'lead-assign'),
    "leadassign_settings_status_section",
    $leadassign_settings_name
  );

  add_settings_section(
    'leadassign_settings_options_section',
    __('Options','lead-assign'),
    'leadassign_settings_options_section',
    $leadassign_settings_name
  );

  add_settings_section(
    'leadassign_settings_links_section',
    __('Links','lead-assign'),
    'leadassign_settings_links_section',
    $leadassign_settings_name
  );
  // all fields added to said section
  // $callback has no args
  // add_settings_field( $id, $title, $callback, $page, $section, $args );
  // id is used to find the element containing the setting data
  // name is used to save said data to the correct option
  // the correct option must then be set into the element to contain setting data...
  // ... on page load
  add_settings_field(
    $leadassign_setting_surpress_warnings_id,
    __('Surpress warnings','lead-assign'),
    'leadassign_setting_surpress_warnings_render',
    $leadassign_settings_name,
    'leadassign_settings_options_section'
  );
  add_settings_field(
    $leadassign_setting_do_style_id,
    __('Enable styling','lead-assign'),
    'leadassign_setting_do_style_render',
    $leadassign_settings_name,
    'leadassign_settings_options_section'
  );

}

// prints HTML for settings status section header
function leadassign_settings_status_section() {
  global $leadassign_contact_form_status,$leadassign_recaptcha_status;
  global $leadassign_dependencies_loaded;
  ?>
  <table class="form-table">
    <tbody>
      <tr>
        <th scope="row"><?php _e('Contact Form Status','lead-assign'); ?></th>
        <td><?php echo $leadassign_contact_form_status; ?></td>
      </tr>
      <tr>
        <th scope="row"><?php _e('reCAPTCHA Status','lead-assign'); ?></th>
        <td><?php echo $leadassign_recaptcha_status; ?></td>
      </tr>
    </tbody>
  </table>
  <?php
  if ($leadassign_dependencies_loaded) {
    ?>
    <p><?php
    printf(
      /* translators note: %s = dynamic widgets.php url */
      __("Everything's ready! Add the Lead Assign <a href='%s'>Widget</a> to your site.",'lead-assign'),
      admin_url('widgets.php')
    ); ?>
    </p>
    <?php
  }
  ?>
  <?php
}

// prints HTML for settings options section header
function leadassign_settings_options_section() {

}

// prints HTML for settings link section header
function leadassign_settings_links_section() {
  ?>
  <p><a href='<?php echo admin_url('admin.php?page=leadassign-faq'); ?>'><?php _e('FAQ','lead-assign'); ?></a></p>
  <?php
}

function leadassign_setting_surpress_warnings_render() {
  global $leadassign_setting_surpress_warnings_id, $leadassign_setting_surpress_warnings_name;
  $option = get_option($leadassign_setting_surpress_warnings_name);
  // checked attribute on input is inserted if the setting is true
  $checked = checked(1,$option,false);
  echo "<input id='$leadassign_setting_surpress_warnings_id' name='$leadassign_setting_surpress_warnings_name' value='1' type='checkbox' ",
  "$checked>";
  echo "<span>".__('Surpress warnings for Lead Assign','lead-assign')."</span>";
}

function leadassign_setting_do_style_render() {
  global $leadassign_setting_do_style_id,$leadassign_setting_do_style_name;
  $option = get_option($leadassign_setting_do_style_name);
  // checked attribute on input is inserted if the setting is true
  $checked = checked(1,$option,false);
  echo "<input id='$leadassign_setting_do_style_id' name='$leadassign_setting_do_style_name' value='1' type='checkbox' ",
  "$checked>";
  echo "<span>".__('Change style of form to better fit as a widget','lead-assign')."</span>";
}

function leadassign_setting_do_style_sanitize($value) {
  // boolean value defaults to true if it gets messed up
  if ($value!=0 && $value!=1) return 1;
  else return $value;
}
