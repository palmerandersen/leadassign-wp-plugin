<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// VARS
$floating_settings_name = 'floating-widget-area-settings';
$floating_default_contact_form_name='Floating Widget Area Default Contact Form';
$floating_default_contact_form_form=
'<label> Your Name (required)
 [text* your-name class:floating-max-width-item class:floating-hide-my-errors] </label>

<label> Your Email (required)
 [email* your-email class:floating-max-width-item class:floating-hide-my-errors] </label>

<label> Subject
 [text your-subject class:floating-max-width-item] </label>

<label> Your Message
 [textarea your-message class:floating-max-width-item x5] </label>

[submit "Send"]';
$floating_default_contact_form_mail=array (
'subject' => 'Your Site "[your-subject]"',
'sender' => '[your-name] <example@example.com>',
'body' => 'From: [your-name] <[your-email]>
Subject: [your-subject]

Message Body:
[your-message]

--
This e-mail was sent from a contact form on Your Site (http://example.com)',
'recipient' => 'example@example.com',
'additional_headers' => 'Reply-To: [your-email]',
'attachments' => '',
'use_html' => false,
'exclude_blank' => false,
);
$floating_default_contact_form_mail_2=
array (
'active' => false,
'subject' => 'Your Site "[your-subject]"',
'sender' => 'Your Site <example@example.com>',
'body' => 'Message Body:
[your-message]

--
This e-mail was sent from a contact form on Your Site (http://example.com)',
'recipient' => '[your-email]',
'additional_headers' => 'Reply-To: example@example.com',
'attachments' => '',
'use_html' => false,
'exclude_blank' => false,
);
$floating_default_contact_form_messages=array (
'mail_sent_ok' => 'Thank you for your message. It has been sent.',
'mail_sent_ng' => 'There was an error trying to send your message. Please try again later.',
'validation_error' => 'One or more fields have an error. Please check and try again.',
'spam' => 'There was an error trying to send your message. Please try again later.',
'accept_terms' => 'You must accept the terms and conditions before sending your message.',
'invalid_required' => 'The field is required.',
'invalid_too_long' => 'The field is too long.',
'invalid_too_short' => 'The field is too short.',
'invalid_date' => 'The date format is incorrect.',
'date_too_early' => 'The date is before the earliest one allowed.',
'date_too_late' => 'The date is after the latest one allowed.',
'upload_failed' => 'There was an unknown error uploading the file.',
'upload_file_type_invalid' => 'You are not allowed to upload files of this type.',
'upload_file_too_large' => 'The file is too big.',
'upload_failed_php_error' => 'There was an error uploading the file.',
'invalid_number' => 'The number format is invalid.',
'number_too_small' => 'The number is smaller than the minimum allowed.',
'number_too_large' => 'The number is larger than the maximum allowed.',
'quiz_answer_not_correct' => 'The answer to the quiz is incorrect.',
'captcha_not_match' => 'Your entered code is incorrect.',
'invalid_email' => 'The e-mail address entered is invalid.',
'invalid_url' => 'The URL is invalid.',
'invalid_tel' => 'The telephone number is invalid.',
);
$floating_default_contact_form_additional_settings='on_submit: "floating_widget_area.checkForAlerts();"';
$floating_default_contact_form_locale='en_US';

// INDIVIDUAL SETTINGS
$floating_setting_min_width_id = "min-width";
$floating_setting_min_width_name = 'floating-widget-area-min-width';

$floating_setting_image_url_id = "icon-image-url";
$floating_setting_image_url_name = 'floating-widget-area-image-url';
