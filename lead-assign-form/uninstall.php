<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
// this file runs the uninstall process when the plugin is deleted

// if uninstall.php is not called by WordPress, die
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

require_once(plugin_dir_path(__FILE__).'vars.php');

// delete options created  by the plugin
delete_option($leadassign_setting_do_style_name);

// done, forward the uninstall
include(plugin_dir_path(__FILE__).'floating-widget-area/uninstall.php');
