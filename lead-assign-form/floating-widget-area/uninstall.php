<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
// if uninstall.php is not called by WordPress, die
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}
// this file runs the uninstall process when the plugin is deleted

// delete options created  by the plugin
delete_option('floating-widget-area-min-width');
delete_option('floating-widget-area-image-url');
