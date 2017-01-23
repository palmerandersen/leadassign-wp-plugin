<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// add link to the page on wordpress
function leadassign_add_faq() {
  global $leadassign_settings_name;
  /*add_menu_page(
    'Page Title',
    'Menu Title',
    'read', // required permissions
    'leadassign-faq', // slug
    'leadassign_faq_render' // render function
  );
  remove_menu_page('leadassign-faq');*/
  add_submenu_page(
    'fhayhjqwfa', // random hash used so that no menu is associated
    __('Lead Assign FAQ','lead-assign'),
    __('FAQ','lead-assign'),
    'read',
    'leadassign-faq',
    'leadassign_faq_render'
  );
}

// outputs the HTML for the FAQ page when called
function leadassign_faq_render() {
  ?>
  <div style="width:80%">
    <p><a href="<?php echo admin_url() ?>options-general.php?page=lead-assign-settings"><?php _e('Go back','lead-assign'); ?></a></p>

    <h1><?php _e('FAQ','lead-assign') ?></h1>
    <hr>

    <h4><?php _e('How do I add the Lead Assign widget to my site?','lead-assign'); ?></h4>
    <p><?php
    printf(
      /* translators note: %s = dynamic widgets.php link */
      __('Click <a href="%s">this link</a> to see all your widgets. The Lead Assign widget is among these.
      Add it to any valid area, set your company slug, and the widget will just work!','lead-assign'),
      admin_url().'widgets.php'
    ); ?>
    </p>
    <hr>

    <h4><?php _e('How can I alter a Lead Assign Widget?','lead-assign'); ?></h4>
    <p><?php _e("All of the widgets are linked to a contact form created through Contact Form 7.
      You can edit this form by clicking the 'edit form' button on your Lead Assign widget.
      If you break the form, create another Lead Assign Widget.",'lead-assign'); ?>
    </p>
    <hr>

    <h4><?php _e('How do I add the Lead Assign widget to a page in my site?','lead-assign'); ?></h4>
    <p><?php _e("Find the contact form linked to your widget by clicking the 'edit form' button on your Lead Assign widget.
      The contact form linked here has a shortcode that can be embedded anywhere on your site.",'lead-assign'); ?></p>
    <hr>

    <h4><?php _e('Where do I get a Lead Assign slug?','lead-assign'); ?></h4>
    <p><?php _e('Sign up for an account on <a href="https://leadassign.com/">Lead Assign</a>. The first 30 days are free!','lead-assign'); ?></p>
    <p><?php _e("Navigate to your company in Lead Assign and click on 'company settings'. Copy the first half of the address titled 'Email Endpoints'.
       For example, if your email endpoint was 'hoffmanlawyers@md.leadassign.com', your slug would be 'hoffmanlawyers'.",'lead-assign'); ?>
    </p>
    <hr>

    <h4><?php _e('My form says [recaptcha] near the bottom. How can I fix this?','lead-assign'); ?></h4>
    <p><?php _e('reCAPTCHA is highly recommended for use with Lead Assign, as spam can be costly. The solution is to setup
      reCAPTCHA for use with Contact Form 7 and Lead Assign. In the Lead Assign settings, there is a link that
      will send you on the right track','lead-assign'); ?>
    </p>
    <p><?php _e("If you absolutely can't use reCAPTCHA, it is possible to remove the field through the 'edit form' button on the
      Lead Assign widget.",'lead-assign'); ?></p>
    <hr>

    <h4><?php _e('Help! My question is still unanswered.','lead-assign'); ?></h4>
    <p><?php _e('Visit the <a href="https://wordpress.org/support/plugin/lead-assign">Support Forums!</a>','lead-assign'); ?></p>
  </div>
  <?php
}

// add faq page
add_action('admin_menu','leadassign_add_faq');
