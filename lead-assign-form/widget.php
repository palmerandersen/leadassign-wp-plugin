<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class Lead_Assign_Widget extends WP_Widget {

  public static function get_default_slug() {
    return '';
  }
  public static function get_tag_prefix() {
    return __('Lead Assign Tags:','lead-assign');
  }

  public static $script_loaded = false;

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		$widget_ops = array(
			'classname' => 'lead_assign_widget',
			'description' => __('Send leads straight to leadassign','lead-assign'),
		);
		parent::__construct( 'lead_assign_widget', __('Lead Assign Widget','lead-assign'), $widget_ops );
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
    global $leadassign_setting_do_style_name;
    // load necessary style if allowed
    if (get_option($leadassign_setting_do_style_name)) {
      wp_enqueue_style('leadassign-form-style',plugins_url('/css/form-style.css',__FILE__));
    }

		// outputs the content of the widget
    $post_id = $instance['form_id'];
    $title = $instance['title'];
    // generate shortcode
    $content = "[contact-form-7 id='$post_id' title ='$title' ]";
    // process shortcode
    $content = do_shortcode($content);
    echo "<div class='widget widget_leadassign' >";
    // only output title stuff is title exists and is non blank
    if (!empty($title)) {
      echo "<h3 class='widget-title'>$title</h3>";
      echo "<hr>";
    }
    echo $content;
    echo "</div>";
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
    global $leadassign_dependencies_loaded;
    global $leadassign_setting_surpress_warnings_name;
    // if var is set false or null/undefined
    if (!leadassign_widget_is_ready()
    // and surpress warnings is false
    && !get_option($leadassign_setting_surpress_warnings_name)) {
      ?>
      <p>
        <?php
        printf(
          __('Warning: Lead Assign configuration is <a href="%s">incomplete</a>','lead-assign'),
          admin_url('options-general.php?page=lead-assign-settings')
        );
        ?>
      </p>
      <?php
    }
		// outputs the options form on admin

    // generate value, id, name for each option
    $title = array_key_exists('title',$instance) ? $instance['title'] : esc_html__( 'Contact Us', 'lead-assign' );
    $title_id = $this->get_field_id('title');
    $title_name = $this->get_field_name('title');

    $slug = ! empty( $instance['slug'] ) ? $instance['slug'] : esc_html( self::get_default_slug(), 'lead-assign' );
    $slug_id = $this->get_field_id('slug');
    $slug_name = $this->get_field_name('slug');

    $post_id = ! empty( $instance['form_id']) ? $instance['form_id'] : esc_html__(0,'lead-assign');
    $post_id_id = $this->get_field_id('form_id');
    $post_id_name = $this->get_field_name('form_id');

    // we're setting tags to empty, so different check is necessary
    $tags = array_key_exists('tags',$instance) ? $instance['tags'] : esc_html__('','lead-assign');
    $tags_id = $this->get_field_id('tags');
    $tags_name = $this->get_field_name('tags');

    self::echo_hidden_input($post_id_id, $post_id_name, $post_id);
    self::echo_text_input(__("Title (optional):",'lead-assign'), $title_id, $title_name, $title);
    self::echo_text_input(__("Company Slug (required): ",'lead-assign'), $slug_id, $slug_name, $slug);
    self::echo_textarea_input(__("Tags: ",'lead-assign'), $tags_id, $tags_name, $tags);

    // if the js script hasn't already been loaded, load it
    if (!self::$script_loaded) {
      self::echo_widget_shortcode_scripts();
      self::$script_loaded=true;
    }
    self::echo_widget_custom_controls($title, $post_id);
    }

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance ) {
    $new_instance = self::filter_fields($new_instance);
		// processes widget options to be saved

    // if the form for this widget has been deleted, create a new one
    global $wpdb;
    $table_name = $wpdb->prefix.'posts';
    $post_id = $new_instance['form_id'];
    // try to find the linked contact form
    $results = $wpdb->get_results("SELECT 1 FROM $table_name WHERE post_type='wpcf7_contact_form' AND ID=$post_id");
    // contact form has been deleted, set form id to blank such that another will be created
    if (count($results)==0) $new_instance['form_id']=0;
    // no form is connected to this widget
    // don't create form for default slug
    if ($new_instance['form_id']==0 && $new_instance['slug']!=self::get_default_slug()) {
      self::generate_new_form($new_instance);
    }
    // a form is connected to this widget
    else {
      self::update_form($new_instance, $old_instance);
    }
    return $new_instance;
	}
  public static function filter_fields($instance) {
    // form id is numbers only
    if (!is_numeric($instance['form_id'])) {
      // something's wrong; form id isn't a number
      // reset it
      $instance['form_id']=0;
    }
    // make sure slug is allowed
    // slug rules: lowercase, numbers, and single hyphens only
    // convert uppercase to lowercase
    $instance['slug'] = strtolower($instance['slug']);
    // if there's an @ (email) get everything before it
    if (preg_match('/.*(?=@)/',$instance['slug'],$matches)) {
      // set the slug to the match
      $instance['slug'] = $matches[0];
    }
    // keep only valid characters (alphanumeric and hyphen)
    $matches = array();
    preg_match_all('/([a-z]|[0-9]|-)/',$instance['slug'],$matches);
    $instance['slug'] = implode('',$matches[0]);

    // keep the tags safe
    $instance['tags'] = sanitize_text_field($instance['tags']);
    // Put filler on tags so that find and replace doesn't go crazy
    // actually, this will go somewhere else
    // $instance['tags'] = self::get_tag_prefix().$instance['tags'];

    // keep title safe
    $instance['title'] = sanitize_text_field($instance['title']);

    return $instance;
  }
  public static function generate_new_form(&$new_instance) {
      // create one
      $post_id = wp_insert_post(
        array(
          'post_title'=>leadassign_default_contact_form_name_get($new_instance['title']),
          'post_type'=>'wpcf7_contact_form',
          'post_status'=>'publish',
          'post_name'=>'contact-form-1'
        )
      );
      // put meta in the post; contact form 7 uses meta for all the values
      // if post was created succesfully
      if ($post_id!=0) {
        // set id on new instance
        $new_instance['form_id'] = $post_id;
        // add all this data and link it to the post
        // each of these meta values can only be set once per contact form;
        // they are unique
        $data_is_unique = True;
        add_post_meta($post_id,'_form',leadassign_default_contact_form_form_get());

        // only properly send email when slug is non-default
        if ($new_instance['slug']!=self::get_default_slug()) {
          $recipient_email = $new_instance['slug'];
          $recipient_email = $recipient_email . '@md.leadassign.com';
        }
        // uh oh, slug is default
        else {
          // use blank email
          $recipient_email = '';
        }

        add_post_meta($post_id,'_mail',leadassign_default_contact_form_mail_get($recipient_email,$new_instance['tags']));
        // mail 2 doesn't really matter because we won't be using it
        // add_post_meta($post_id,'_mail_2',leadassign_default_contact_form_mail_2_get());
        add_post_meta($post_id,'_messages',leadassign_default_contact_form_messages_get());
        add_post_meta($post_id,'_additional_settings',leadassign_default_contact_form_additional_settings_get());
        add_post_meta($post_id,'_locale',leadassign_default_contact_form_locale_get());
      }
  }
  public static function echo_text_input($title, $id, $name, $value) {
    ?>
    <p>
      <label for="<?php esc_attr_e($id)?>">
        <?php esc_attr_e($title,'text_domain')?>
      </label>
      <input class='widefat' id="<?php esc_attr_e($id)?>" name="<?php esc_attr_e($name)?>"
        type="text" value = "<?php esc_attr_e($value)?>"
      >
    </p>
    <?php
  }
  public static function echo_hidden_input($id, $name, $value) {
    ?>
    <input type="hidden" id="<?php esc_attr_e($id)?>" name="<?php esc_attr_e($name)?>"
      value="<?php esc_attr_e($value)?>"
    >
    <?php
  }
  public static function echo_textarea_input($title, $id, $name, $value) {
    ?>
    <p>
      <label for="<?php esc_attr_e($id)?>">
        <?php esc_attr_e($title,'text_domain')?>
      </label>
      <textarea class='widefat' id="<?php esc_attr_e($id)?>" name="<?php esc_attr_e($name)?>"><?php echo $value ?></textarea>
    </p>
    <?php
  }
  public static function echo_widget_shortcode_scripts() {
    ?>
    <script>
      function leadAssignToggleCopyBox(elem) {
        if (elem.type=="hidden") {
          elem.type="text";
          elem.select();
        }
        else {
          elem.type="hidden";
        }
      }
      function leadAssignToggleShortcodeTitle(elem) {
        if (elem.innerHTML == "Show Shortcode") {
          elem.innerHTML = "Hide Shortcode";
        }
        else {
          elem.innerHTML = "Show Shortcode";
        }
      }
    </script>
    <?php
  }
  public static function echo_widget_custom_controls($title, $post_id) {
    ?>
    <p>
      <a href='<?php echo admin_url("admin.php?page=wpcf7&post=$post_id") ?>'>Edit form</a>
    | <a
        href="javavascript:void(0)"
        onclick="leadAssignToggleCopyBox(this.parentNode.nextElementSibling);leadAssignToggleShortcodeTitle(this);"
     >Show Shortcode</a>
   </p>

   <input type="hidden" readonly name="hi" id="leadassign-widget-copy-box" class="widefat"
   style="width:100%;height:20px;border:1px solid #ddd;margin-bottom:5px"
   value = "<?php echo "[contact-form-7 id='$post_id' title='$title']" ?>"
   >
     <?php
  }
  public static function update_form(&$new_instance, &$old_instance) {
    $post_id = $new_instance['form_id'];

    // diferent sections could edit meta as they see fit
    $mail_meta = get_post_meta($post_id,'_mail',True);
    // if slug has changed
    if ($new_instance['slug']!=$old_instance['slug']) {
      // update recipient email
      $recipient_email = $new_instance['slug'];
      $recipient_email = $recipient_email . '@md.leadassign.com';

      // update just one field of the meta
      $mail_meta['recipient'] = $recipient_email;
    }
    // if tags have changed
    if ($new_instance['tags']!=$old_instance['tags']) {
      // add prefix to new and old instance
      $old_with_prefix = self::get_tag_prefix().' '.$old_instance['tags'];
      $new_with_prefix = self::get_tag_prefix().' '.$new_instance['tags'];

      // find old tags and overwrite them
      // find last match of old tag (because it's placed at the end)
      $old_tag_pos = strrpos($mail_meta['body'], $old_with_prefix);

      // try second, better method
      // search for prefix to end of line, then replace that selection using
      // ... $new_with_prefix

      // find last match of just prefix
      $last_prefix_pos = strrpos($mail_meta['body'], self::get_tag_prefix());
      // try to extend replace to end of line, and ignore tag contents
      $matches = Array();
      // find all matches for leadassign tags to end of line starting from last match for
      $match = preg_match('/\bLead Assign Tags:.*/m',$mail_meta['body'],$matches,PREG_OFFSET_CAPTURE,$last_prefix_pos);
      // if there were any matches
      if ($match) {
        // use last match as string for replace
        $old_with_prefix = $matches[count($matches)-1][0];
        // use index of last match as stringpos for replace
        $old_tag_pos = $matches[count($matches)-1][1];
      }

      if ($old_tag_pos!==False) {
        // mixed substr_replace ( mixed $string , mixed $replacement , mixed $start [, mixed $length ] )
        // find and replace on the mail meta body
        $mail_meta['body'] = substr_replace($mail_meta['body'],$new_with_prefix,$old_tag_pos,strlen($old_with_prefix));
      }
      // couldn't find last tag for some reason
      else {
        // just throw the tags onto the end
        $mail_meta['body'].="\n".self::get_tag_prefix().$new_instance['tags'];
      }
    }
    // if title has changed
    if ($new_instance['title']!=$old_instance['title']) {
      $updated_post = array(
        'ID' => $post_id,
        'post_title' => leadassign_default_contact_form_name_get($new_instance['title']),
      );
      wp_update_post($updated_post);
    }
    update_post_meta($post_id,'_mail',$mail_meta);
  }
}
