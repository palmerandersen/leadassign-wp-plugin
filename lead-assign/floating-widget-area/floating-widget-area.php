<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

   /*
   Plugin Name: Floating Widget Area
   Plugin URI:
   Description: A nice floating widget area. Summoned by an icon. Built-in support for Contact Form 7
   Version: 1.0
   Author: Palmer Andersen
   Author URI: http://palmerandersen.com/
   License: GPL2
   License URI: https://www.gnu.org/licenses/gpl-2.0.html
   */
   // IMPORT OUR FAVOURITE VARS
   require_once(plugin_dir_path(__FILE__).'vars.php');
   // IMPORT ALL THIS THEME'S FUNCTIONS
   require_once(plugin_dir_path(__FILE__).'functions.php');

   // THIS IS THE MAIN PLUGIN FILE

   // lights camera and many actions
   add_action('wp_enqueue_scripts','floating_load_styles');
   add_action('wp_enqueue_scripts','floating_load_universal_items');
   add_action('admin_enqueue_scripts','floating_load_universal_items');
   add_action('wp_footer','floating_createDiv');

   $plugin = plugin_basename( __FILE__ );
   add_filter( "plugin_action_links_$plugin", 'floating_add_settings_link' );

   add_action('admin_menu','floating_create_menu');
   add_action("admin_init","floating_register_settings");

  // runs when on settings page for this plugin in wordpress backend
  if (isset($_GET['page']) && $_GET['page'] == $floating_settings_name) {
    add_action('admin_print_scripts', 'floating_admin_scripts');
    add_action('admin_print_styles', 'floating_admin_styles');
  }

  floating_clear_blank_value($floating_setting_image_url_name);
  floating_clear_blank_value($floating_setting_min_width_name);
  floating_widget_area_activate(); // move to activation hook if fix found
  // register_activation_hook(__FILE__,'floating_widget_area_activate'); not firing at all, don't know why

  add_action( 'widgets_init', 'floating_register_widget_area' );
  add_action('in_admin_footer','floating_check_for_contact_form');
?>
