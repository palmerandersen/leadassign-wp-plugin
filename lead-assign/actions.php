<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
// add settings to plugin in plugins menu
$plugin = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$plugin", 'leadassign_add_settings_link' );

// add settings to settings menu
add_action('admin_menu','leadassign_create_menu');
// register settings
add_action("admin_init","leadassign_register_settings");

// runs when on settings page for this plugin in wordpress backend
if (isset($_GET['page']) && $_GET['page'] == $leadassign_settings_name) {
 add_action('admin_print_scripts', 'leadassign_admin_scripts');
 add_action('admin_print_styles', 'leadassign_admin_styles');
 add_action('plugins_loaded', 'leadassign_check_dependencies');
}

// register widget if widget is ready
add_action('widgets_init','leadassign_register_widget');

// if admin needs to be displated, and it hasn't yet
add_action('plugins_loaded',function() {
  global $leadassign_setting_surpress_warnings_name;
  if (!leadassign_widget_is_ready()
  && !get_option($leadassign_setting_surpress_warnings_name)) {
    // display a notice
    add_action('admin_notices','leadassign_display_dependencies_notice');
  }
});

// hook into sidebar setup so that we can delete unneeded forms
add_action( 'sidebar_admin_setup', 'leadassign_cleanup_widget' );

// translations
add_action('init',function() {
  load_plugin_textdomain('lead-assign', false, dirname(plugin_basename(__FILE__)) . '/languages' );
});
