<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $leadassign_setting_do_style_name;
global $leadassign_option_admin_notice_shown_name;
// Vars
$leadassign_settings_name = 'lead-assign-settings';

// Dependency status
$leadassign_contact_form_status = __('UNKNOWN','lead-assign');
$leadassign_recaptcha_status = __('UNKNOWN','lead-assign');

$leadassign_dependencies_loaded = False;

// Individual settings
$leadassign_setting_do_style_id = 'leadassign_setting_do_style';
$leadassign_setting_do_style_name = 'leadassign_setting_do_style';

$leadassign_setting_surpress_warnings_id = 'leadassign_setting_surpress_warnings';
$leadassign_setting_surpress_warnings_name = 'leadassign_setting_surpress_warnings';

// Other options
$leadassign_option_admin_notice_shown_name = 'leadassign_option_admin_notice_shown';
