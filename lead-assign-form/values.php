<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
// get contact form stuff

// returns title for contact form with input for name given by user
function leadassign_default_contact_form_name_get($widget_name) {
  return sprintf(
    /* translators note: %s = widget instance name */
    __('Lead Assign Contact Form: %s','lead-assign'),
    $widget_name
  );
}

// returns the 'form' meta for the default contact form
function leadassign_default_contact_form_form_get() {
  return __('<label> Your Name (required)
   [text* your-name class:leadassign] </label>

  <label> Your Email (required)
   [email* your-email class:leadassign] </label>

  <label> Your Phone Number
   [text your-phone class:leadassign] </label>

  <label> Your Message
   [textarea your-message class:leadassign x5] </label>
  [recaptcha]
  [submit "Send"]','lead-assign');
}

// returns the 'mail' meta for the default contact form
// args:
// recipient_email: Lead Assign email endpoint
// tags: all tags to be included as a string
function leadassign_default_contact_form_mail_get($recipient_email='',$tags='') {
  // if tags are non-empty, put the prefix in front of them
  //if ($tags!='') // actually just always put the prefix there
  $tags = Lead_Assign_Widget::get_tag_prefix().$tags;
  // get site url with http in front
  $site_url = get_site_url(null,'','http');
  // strip http from site url
  $site_url_stripped = preg_replace('/^http:\/\//','',$site_url);
  return array (
  'subject' => sprintf(
    /* translators note: %s = text from name field */
    __("New Lead from %s",'lead-assign'),
    '[your-name]'
  ),
  'sender' => sprintf(
    __('%1$s %2$s','lead-assign'),
    '[your-name]',
    "leadassign@$site_url_stripped"
  ),

  'body' =>
sprintf(
  /* translators notes:
  %1$s = text from name field
  %2$s = text from email field
  %3$s = text from phone field
  */
  __(
'Name: %1$s
Email: %2$s
Phone: %3$s

[your-message]

','lead-assign'),
'[your-name]',
'[your-email]',
'[your-phone]').
$tags,

'recipient' => $recipient_email,

'additional_headers' => sprintf(
  /* translators note: %s = text from email field */
  __('Reply-To: %s','lead-assign'),'[your-email]'
),

'attachments' => '',

'use_html' => false,

'exclude_blank' => false,
  );
}

// @deprecated, only the 'mail 2' contact form meta is not used.
// see leadassign_default_contact_form_mail_get
function leadassign_default_contact_form_mail_2_get() {
  return aray(); // don't use mail 2
}

// returns the default reponse messages for the contact form (seen by user)
function leadassign_default_contact_form_messages_get() {
  if (defined('WPCF7_PLUGIN')) return WPCF7_ContactFormTemplate::get_default('messages');
  else return array();
}

// returns the 'additional settings' meta field for the contact form
function leadassign_default_contact_form_additional_settings_get() {
  return '';
}

// returns the default locale of the contact form
function leadassign_default_contact_form_locale_get() {
  $locale = get_locale();
  if (empty($locale)) $locale = 'en_US';
  return $locale;
}
