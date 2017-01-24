<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
// This file includes the functions. Nice!

// add settings link on plugins page
// this is the link that is found on this plugin's entry in the list of plugins
function leadassign_add_settings_link( $links ) {
  global $leadassign_settings_name;
  // create local link using plugin name
  $settings_link = '<a href="options-general.php?page='.$leadassign_settings_name.'">' . __( 'Settings', 'lead-assign' ) . '</a>';
  // add said link to the array of links for this plugin's actions
  array_push( $links, $settings_link );
  return $links;
}
// adds menu for this plugin to main settings menu in admin panel
function leadassign_create_menu() {
  global $leadassign_settings_name;
  // add_options_page( $page_title, $menu_title, $capability, $menu_slug, $function);
  add_options_page(
  __("Lead Assign Settings",'lead-assign'),
  __("Lead Assign",'lead-assign'),
  "manage_options",
  $leadassign_settings_name,
  "leadassign_settings_page");
}

// loads scripts used on this plugin's admin page
function leadassign_admin_scripts() {

}

// loads styles used on this plugin's admin page
function leadassign_admin_styles() {
  wp_enqueue_style('leadassign-settings-style',plugins_url('/css/settings-style.css',__FILE__));
}
// register the leadassign widget
function leadassign_register_widget() {
  register_widget('lead_assign_widget');
}
function leadassign_check_dependencies() {
  global $leadassign_contact_form_status,$leadassign_recaptcha_status,$leadassign_dependencies_loaded;
  // if contact form 7 loaded
  if (defined('WPCF7_PLUGIN')) {
    $leadassign_contact_form_status =
    "<span class=leadassign-success>".
    __("ACTIVE",'lead-assign')
    ."</span>"
    ;
    // if recaptcha configured
    if (WPCF7_RECAPTCHA::get_instance()->is_active()) {
      $leadassign_recaptcha_status =
      "<span class=leadassign-success>".
      __("CONFIGURED",'lead-assign')
      ."</span>"
      ;

      // All dependencies are loaded and ready
      $leadassign_dependencies_loaded = True;
    }
    else {
      $admin_url = admin_url();
      $leadassign_recaptcha_status =
      sprintf(
        /* translators note: %1$s = failure classname, %2$s = recaptcha confiruation url */
        __('<span class="%1$s">NOT CONFIGURED.</span> Configure reCAPTCHA <a href="%2$s">here</a>','lead-assign'),
        'leadassign-failure',
        "${admin_url}admin.php?page=wpcf7-integration"
      );
    }
  }
  else {
    $leadassign_contact_form_status =
    sprintf(
      /* translators note: %1$s = classname for red text. %2$s = dynamic link to wpcf7 */
      __("<span class='%1\$s'>NOT ACTIVE.</span> Get it <a href='%2\$s'>here</a>.",'lead-assign'),
      'leadassign-failure',
	  admin_url('plugin-install.php?s=contact+form+7&tab=search&type=term')
    );
    // contact form 7 isn't installed, so recaptcha can't be configured, either
    $leadassign_recaptcha_status =
    "<span class=leadassign-failure>".
    __("NOT CONFIGURED",'lead-assign')
    ."</span>"
    ;
  }
}
function leadassign_widget_is_ready() {
  return (defined('WPCF7_PLUGIN') && WPCF7_RECAPTCHA::get_instance()->is_active());
}
function leadassign_display_dependencies_notice() {
  ?>
  <div class="notice notice-warning">
    <p><?php
    /* translators note: %s = config link */
    printf(
      __('Lead Assign\'s dependencies require <a href="%s">configuration</a>.','lead-assign'),
      admin_url().'options-general.php?page=lead-assign-settings'
    );
    ?></p>
  </div>
  <?php
}
function leadassign_cleanup_widget() {
  if (
    strtolower( $_SERVER['REQUEST_METHOD'] ) == 'post'  && // POST, not GET
    isset( $_POST['delete_widget']) && // ...
    1 === (int) $_POST['delete_widget'] // AND a widget is being deleted
  )
  {
    $widget_id = $_POST['widget-id'];
    // Widget deleted; do something
    $exploded = explode('-',$widget_id);
    $option_name = 'widget_'.$exploded[0];
    $instance_index = intval($exploded[1]);
    // we only do stuff on our own widgets
    if ($option_name!='widget_lead_assign_widget') return;
    // get widget instance
    $instances = get_option('widget_lead_assign_widget');
    $instance = $instances[$instance_index];
    // delete related form
    wp_delete_post(intval($instance['form_id']));
  }
}
