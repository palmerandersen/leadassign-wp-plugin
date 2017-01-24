// this script is loaded on normal pages where this plugin's widgetarea may be found
// create an object to hold anything so as not to pollute the global JS namespace
var floating_widget_area = floating_widget_area || {};
floating_widget_area._construct = function() {

// VARS

// get floating menu, and its respective original values
var floating_menu = document.getElementById("floating-widget-area-menu");
var floating_menuExtended=false;
var floating_menuOriginalWidth = jQuery(floating_menu).css('width');

// get floating icon, and attach a function to its onclick event
var floating_icon = document.getElementById("floating-widget-area-icon");
floating_icon.onclick = floating_animateMenu;

// initial property of menu
var floating_menu_initial_top;

// used with contact form 7. Flashes text boxes red when an alert is displayed
var floating_alert_removal_delay = 0;

// declare it here so nothing goes global
var browserMinWidth;


// FUNCTIONS

// used when a contact form is in the widgetarea, which executes succesfully
function floating_contact_form_success() {
  // iterate through text boxes
  var text_boxes = jQuery("#floating-widget-area-menu").find(".wpcf7-form-control");
  for (var i=0;i<text_boxes.length;i++) {
    floating_make_text_box_green(i,text_boxes[i]);
  }
}
function floating_make_text_box_green(index,text_box) {
  // delay because something is being reset after it is set
  setTimeout(function() {text_box.style.borderColor = 'green';},0);
}

function floating_check_for_alerts() {
  // iterate through alerts
  var alerts = jQuery("#floating-widget-area-menu").find(".floating-hide-my-errors");
  for (var i = 0; i < alerts.length; i++) {
    floating_check_for_alert(i, alerts[i]);
  }
  // also hide the message box that shows up at the bottom
  // setTimeout(function() {jQuery("div.wpcf7-response-output.floating-menu-child").css("display","none");},floating_alert_removal_delay);
}

// this function only executes when alerts exist, so no need for a check in here
function floating_check_for_alert(index, element) {
  // use border instead of text notification to save space
  // check if next sibling is the alert message
  try {
    if (element.nextSibling.className.indexOf("wpcf7-not-valid-tip")!=-1) {
      element.nextSibling.style.display='none';
      element.style.borderColor='red';
    }
    else {
      // next sibling is not alert message
      element.style.borderColor='';
    }
  }
  catch (error) {
    // next sibling is null
    element.style.borderColor='';
  }
}



// returns the rendered width of the floating menu object
function get_floating_menu_width() {
  // the argument in outerWidth determines whether to include margins
  // we will include margins
  return jQuery("#floating-widget-area-menu").outerWidth(true);
}

// adds class to every child, grandchild, etc of a node (CSS styling)
function recursivelyAddClassToChildren(node,className) {
  for (var i=0;i<node.childNodes.length;i++) {
    // get this child of node
    var child = node.childNodes[i];
    // get that recursion going
    recursivelyAddClassToChildren(child,className);
    // add the className
    child.className+=className;
  }
}

// animates the menu open (onto the screen)
function floating_animateMenuOpen() {
  // if the floating menu is not animating
  if (!jQuery("#floating-widget-area-menu").is(":animated")) {
    // set the positon of the floating menu to just off the right side of the screen
    // this is necessary because the floating menu starts far far off the screen in case of resizing
    // by the time this function is called, the menu has surely reached its final size
    floating_menu.style.right = '-'+(get_floating_menu_width())+'px';
  }
  // stop last animation so that this animation will start immediately
  jQuery("#floating-widget-area-menu").stop();
  // animate the floating menu such that its right side will touch the right side of the screen
  // margins are included so there will still be space
  jQuery("#floating-widget-area-menu").animate(
    {
      right:'0px'
    }
  );
  // floating menu is now extended
  floating_menuExtended=true;
}

// animates the menu closed (off the screen)
function floating_animateMenuClosed() {
  var tmp = get_floating_menu_width();
  tmp='-'+tmp+'px';
  // stop last animation so that this animation will start immediately
  jQuery("#floating-widget-area-menu").stop();
  // animate the floating menu just off the side of the page
  jQuery("#floating-widget-area-menu").animate(
    {
      right:tmp
    }
  );
  // the floating menu is no longer extended
  floating_menuExtended=false;
}


// toggles the animation state of the floating menu (open or closed)
function floating_animateMenu() {
  // put menu away
  if (floating_menuExtended) {
    floating_animateMenuClosed();
  }
  // get menu out
  else {
    floating_animateMenuOpen();
  }
  // either way, set width if too wide
  floating_resize();
}

// resizes elements based on browser width
function floating_resize() {
  // pixel value for space taken up in the x axis by margins of the floating menu
  var widthMargins = parseInt(jQuery(floating_menu).css("margin-left")) + parseInt(jQuery(floating_menu).css("margin-right"));
  // if floating menu is wider than window
  if (get_floating_menu_width()>jQuery(window).width()) {
    // create new width value and apply it to floating menu
    var width = jQuery(window).width();
    width-=widthMargins;
    // floating menu will now take up exactly 100% of the page (including margins)
    floating_menu.style.width=width+'px';
  }
  // if floating menu original width with margins would be wider than window
  else if (parseInt(floating_menuOriginalWidth)+widthMargins<=jQuery(window).width()) {
    // restore original width of floating menu
    floating_menu.style.width=floating_menuOriginalWidth;
  }
  // if floating menu is supposed to be hidden, make sure its position is correct
  if (!jQuery("#floating-widget-area-menu").is(":animated") && !floating_menuExtended) {
    var value = "-" + get_floating_menu_width() + "px";
    jQuery("#floating-widget-area-menu").css("right",value);
  }
  // if browser minimum width is greater than the current window size
  if (jQuery(window).width()<browserMinWidth) {
    // hide floating icon
    floating_icon.style.display='none';
  }
  else {
    // show floating icon
    floating_icon.style.display='block';
  }
  // resize menu top maybe
  // check if menu is too tall
  var availableHeight = jQuery(window).height()-parseInt(jQuery(floating_menu).css("bottom"))-parseInt(floating_menu_initial_top);
  if (floating_menu.scrollHeight>availableHeight) {
    // set top property
    floating_menu.style.top = floating_menu_initial_top;
  }
  else {
    // unset top property
    floating_menu.style.top="initial";
  }
}

function floating_set_browser_min_width(value) {
  browserMinWidth = value;
}

// triggered on browser window resize
jQuery(window).resize(floating_resize);

// close menu when clicking off
jQuery(document).click(function(event) {
    // click is not on floating menu or floating icon
    if(!jQuery(event.target).closest('#floating-widget-area-menu').length &&
    !jQuery(event.target).closest('#floating-widget-area-icon').length) {
        // animate menu closed if it is currently visible
        if(jQuery('#floating-widget-area-menu').is(":visible")) {
            floating_animateMenuClosed();
        }
    }
});

// triggered when document is loaded and ready for jQuery
jQuery(document).ready(function() {
  // set class for widget area elements
  recursivelyAddClassToChildren(floating_menu,' floating-menu-child ');
  // get value for css property 'top' of widgetarea
  floating_menu_initial_top = jQuery(floating_menu).css("top");
  // set sizing nice and proper
  floating_resize();
});
// declare anything that should be public
// syntax: this.var_or_function = var_or_function
this.checkForAlerts = floating_check_for_alerts;
this.resize = floating_resize;
this.animateMenu = floating_animateMenu;
this.animateMenuOpen = floating_animateMenuOpen;
this.animateMenuClosed = floating_animateMenuClosed;
this.contactFormSuccess = floating_contact_form_success;
this.menu = floating_menu;
this.icon = floating_icon;
this.setBrowserMinWidth = floating_set_browser_min_width;

// end of unique namespace
};
floating_widget_area._construct();
