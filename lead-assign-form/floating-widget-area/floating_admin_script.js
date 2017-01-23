// this script is loaded in the settings page for this plugin

jQuery(document).ready(function() {

// triggered when WP upload button is clicked
jQuery('#upload_image_button').click(function() {
  // summon the holy WP media selector
  tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
  return false;
});

// triggered when an image has been selected through the WP upload button
window.send_to_editor = function(html) {
  // get url of image
  imgurl = jQuery.parseHTML(jQuery('img',html).context)[0].src;
  // // dont strip protocol actually
  // newimgurl = imgurl.match(/:.*/)[0];
  // newimgurl = newimgurl.substr(1,newimgurl.length);
  // if (newimgurl!==null && newimgurl!=="") {
  //  imgurl=newimgurl;
  // }
  // fill in text box with imgurl
  jQuery('#icon-image-url').val(imgurl);
  // update image preview src
  document.getElementById("floating-widget-area-image-preview").src=imgurl;
  // remove the WP media popup from the page
  tb_remove();
};

}); // end of the giant anonymous function
