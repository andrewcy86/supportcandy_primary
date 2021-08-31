jQuery(document).ready(function(){

jQuery(document).on('keydown', function(e) {
    /*
    var tabbable = jQuery("#wpsc_popup_body").find('select, input, textarea, button, a').filter(':visible');
    
    var firstTabbable = tabbable.first();

    firstTabbable.focus();
    */
    
    var target = e.target;
    var shiftPressed = e.shiftKey;
    // If TAB key pressed
    if (e.keyCode == 9) {
        // If inside a Modal dialog (determined by attribute id=wpsc_popup)
        if (jQuery(target).parents('[id=wpsc_popup]').length) {                            
            // Find first or last input element in the dialog parent (depending on whether Shift was pressed). 
            // Input elements must be visible, and can be Input/Select/Button/Textarea.
            var borderElem = shiftPressed ?
                                jQuery(target).closest('[id=wpsc_popup]').find('a:visible,input:visible,select:visible,button:visible,textarea:visible').first() 
                             :
                                jQuery(target).closest('[id=wpsc_popup]').find('a:visible,input:visible,select:visible,button:visible,textarea:visible').last();
            if (jQuery(borderElem).length) {
                if (jQuery(target).is(jQuery(borderElem))) {
                    return false;
                } else {
                    return true;
                }
            }
        }
    }
    
        if (e.keyCode == 13) {
            //alert('enter');
                    if (jQuery(target).parents('[id=wpsc_popup_footer]').length) { 
                    e.preventDefault();
                    document.activeElement.click();
                    }
    }
    
    return true;
});
  
  jQuery('#wpsc_popup_background,.wpsc_popup_close').click(function(){
    //wpsc_modal_close();
  });
  
  jQuery(document).keyup(function(e){
    if (e.keyCode == 27) { 
      //wpsc_modal_close();
    }
  });
  
});

function wpsc_modal_open(title){
  jQuery('#wpsc_popup_title h3').text(title);
  jQuery('#wpsc_popup_body').html(wpsc_admin.loading_html);
  jQuery('.wpsc_popup_action').hide();
  jQuery('#wpsc_popup_container,#wpsc_popup_background').show();
  jQuery("#wpsc_popup_container").attr("tabindex",-1).focus();

jQuery("#wpsc_popup_container").click();

    var tabbable = jQuery("#wpsc_popup_body").find('select, input, textarea, button, a').filter(':visible');
    
    var firstTabbable = tabbable.first();
    /*set focus on first input*/
    firstTabbable.focus();

}

function wpsc_modal_close(){
  jQuery('#wpsc_popup_container,#wpsc_popup_background').hide();
}

function wpsc_modal_close_thread(tinymce_toolbar){
  
  jQuery('#wpsc_popup_container,#wpsc_popup_background').hide();
  var is_tinymce = (typeof tinyMCE != "undefined") && tinyMCE.activeEditor && !tinyMCE.activeEditor.isHidden();
  if(is_tinymce){
    tinymce.init({
      selector:'#wpsc_reply_box',
      body_id: 'wpsc_reply_box',
      menubar: false,
      statusbar: false,
      autoresize_min_height: 150,
      wp_autoresize_on: true,
      plugins: [
          'wpautoresize lists link image directionality'
      ],
      toolbar:  tinymce_toolbar.join() +' | wpsc_templates',
      branding: false,
      autoresize_bottom_margin: 20,
      browser_spellcheck : true,
      relative_urls : false,
      remove_script_host : false,
      convert_urls : true
    });
  }
}
