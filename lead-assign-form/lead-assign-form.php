<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/*
Plugin Name: Lead Assign
Plugin URI:
Description: Send leads from your website straight to Lead Assign
Version: 1.0.2
Author: Lead Assign
Author URI: https://leadassign.com/
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: lead-assign
*/

// import vars
require_once(plugin_dir_path(__FILE__).'vars.php');
// import functions
require_once(plugin_dir_path(__FILE__).'functions.php');
// import widget
require_once(plugin_dir_path(__FILE__).'widget.php');
// import settings
require_once(plugin_dir_path(__FILE__).'settings.php');
// import contact form values
require_once(plugin_dir_path(__FILE__).'values.php');

// activate plugin
function leadassign_activate() {
 // create options
 global $leadassign_setting_do_style_name;
 global $leadassign_option_admin_notice_shown_name;
 add_option($leadassign_setting_do_style_name,1);
 add_option($leadassign_setting_surpress_warnings_name,0);
 add_option($leadassign_option_admin_notice_shown_name,False);
}
register_activation_hook(__FILE__,'leadassign_activate');

// deactivate plugin
function leadassign_deactivate() {
 // remove temporary options
 global $leadassign_option_admin_notice_shown_name;
 delete_option($leadassign_option_admin_notice_shown_name);
}
register_deactivation_hook(__FILE__,'leadassign_deactivate');

// run actions
include(plugin_dir_path(__FILE__).'actions.php');

// setup FAQ
include(plugin_dir_path(__FILE__).'faq.php');

// include modules
include(plugin_dir_path(__FILE__).'floating-widget-area/floating-widget-area.php');
