<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user,$wpscfunction,$wpdb;

if (!($current_user->ID && $current_user->has_cap('wpsc_agent'))) {exit;}

$ticket_id  = isset($_POST['ticket_id']) ? sanitize_text_field($_POST['ticket_id']) : '' ;
$ticket_data = $wpscfunction->get_ticket($ticket_id);
$status_id   	= $ticket_data['ticket_status'];
$priority_id 	= $ticket_data['ticket_priority'];
$category_id  = $ticket_data['ticket_category'];
$wpsc_appearance_modal_window = get_option('wpsc_modal_window');
$wpsc_custom_status_localize   = get_option('wpsc_custom_status_localize');
$wpsc_custom_category_localize = get_option('wpsc_custom_category_localize');
$wpsc_custom_priority_localize = get_option('wpsc_custom_priority_localize');
//PATT BEGIN

include_once( WPPATT_ABSPATH . 'includes/term-ids.php' );

$r3_check = $wpscfunction->get_ticket_meta($ticket_id,'r3_preset');

$customer_name   	= $ticket_data['customer_name'];

$agent_permissions = $wpscfunction->get_current_agent_permissions();
$agent_type = $agent_permissions['label']; 
//PATT END
ob_start();
?>
<!-- PATT BEGIN -->
<div id="comment-alert" class=" alert alert-danger">Please enter a rejection reason.  Saving Disabled.</div>
<!-- PATT END -->


<form id="frm_get_ticket_change_status" method="post">
    <?php
    //PATT BEGIN
    $pre_received_array = array($request_new_request_tag->term_id,$request_initial_review_complete_tag->term_id,$request_initial_review_rejected_tag->term_id,$request_shipped_tag->term_id);

    $post_received_array = array($request_ecms_tag->term_id,$request_in_process_tag->term_id,$request_completed_dispositioned_tag->term_id);

    $shipping_array = array($request_shipped_tag->term_id,$request_received_tag->term_id);
    
    $r3_array = array($request_shipped_tag->term_id,$request_initial_review_complete_tag->term_id);
    
    $is_ext_shipping = Patt_Custom_Func::using_ext_shipping( $ticket_id );
    
    $cancelled_array = array($request_new_request_tag->term_id,$request_tabled_tag->term_id,$request_initial_review_complete_tag->term_id,$request_initial_review_rejected_tag->term_id,$request_shipped_tag->term_id,$request_received_tag->term_id,$request_in_process_tag->term_id,$request_ecms_tag->term_id,$request_sems_tag->term_id,$request_completed_dispositioned_tag->term_id);
    $completed_array = array($request_new_request_tag->term_id,$request_tabled_tag->term_id,$request_initial_review_complete_tag->term_id,$request_initial_review_rejected_tag->term_id,$request_shipped_tag->term_id,$request_received_tag->term_id,$request_in_process_tag->term_id,$request_ecms_tag->term_id,$request_sems_tag->term_id,$request_cancelled_tag->term_id);
    $ecms_sems_array = array($request_new_request_tag->term_id,$request_initial_review_complete_tag->term_id,$request_initial_review_rejected_tag->term_id,$request_shipped_tag->term_id,$request_received_tag->term_id,$request_in_process_tag->term_id,$request_ecms_tag->term_id,$request_sems_tag->term_id,$request_cancelled_tag->term_id);
    $inprocess_array = array($request_new_request_tag->term_id,$request_initial_review_complete_tag->term_id,$request_initial_review_rejected_tag->term_id,$request_shipped_tag->term_id,$request_received_tag->term_id);
    $received_array = array($request_new_request_tag->term_id,$request_initial_review_complete_tag->term_id,$request_initial_review_rejected_tag->term_id,$request_shipped_tag->term_id,$request_ecms_tag->term_id,$request_sems_tag->term_id,);
 
    //Note to do for all external shipping
    $shipped_array = array($request_new_request_tag->term_id,$request_initial_review_complete_tag->term_id,$request_initial_review_rejected_tag->term_id,$request_in_process_tag->term_id,$request_received_tag->term_id,$request_ecms_tag->term_id,$request_sems_tag->term_id,$request_completed_dispositioned_tag->term_id);
    $r3_shipped_array = array($request_new_request_tag->term_id,$request_initial_review_complete_tag->term_id,$request_initial_review_rejected_tag->term_id,$request_in_process_tag->term_id,$request_ecms_tag->term_id,$request_sems_tag->term_id,$request_completed_dispositioned_tag->term_id);
   
    $rejected_array = array($request_new_request_tag->term_id,$request_tabled_tag->term_id,$request_initial_review_complete_tag->term_id,$request_shipped_tag->term_id,$request_received_tag->term_id,$request_in_process_tag->term_id,$request_ecms_tag->term_id,$request_sems_tag->term_id,$request_completed_dispositioned_tag->term_id,$request_cancelled_tag->term_id);
    
    $complete_array = array($request_new_request_tag->term_id,$request_initial_review_complete_tag->term_id,$request_initial_review_rejected_tag->term_id,$request_shipped_tag->term_id,$request_received_tag->term_id,$request_in_process_tag->term_id,$request_ecms_tag->term_id,$request_sems_tag->term_id,$request_completed_dispositioned_tag->term_id);
    $r3_complete_array = array($request_new_request_tag->term_id,$request_initial_review_complete_tag->term_id,$request_initial_review_rejected_tag->term_id,$request_shipped_tag->term_id,$request_in_process_tag->term_id,$request_ecms_tag->term_id,$request_sems_tag->term_id,$request_completed_dispositioned_tag->term_id);
    
    $tabled_array = array($request_new_request_tag->term_id,$request_shipped_tag->term_id,$request_received_tag->term_id,$request_ecms_tag->term_id,$request_sems_tag->term_id,);
    $new_array = array($request_new_request_tag->term_id,$request_shipped_tag->term_id,$request_received_tag->term_id,$request_in_process_tag->term_id,$request_ecms_tag->term_id,$request_sems_tag->term_id,$request_completed_dispositioned_tag->term_id);


    //PATT END
    ?>

<div class="form-group">
		<label for="wpsc_default_ticket_status"><?php _e('Ticket Status','supportcandy');?></label>
		<!--PATT BEGIN -->
		<select class="form-control" name="edit_status" id="edit_status" aria-label="Edit Status">
		<!--PATT END -->
			<?php
			//PATT BEGIN
			$hidden_status = '';
			$sems_check = $wpscfunction->get_ticket_meta($ticket_id,'super_fund');
                
            if(in_array("true", $sems_check)) {
            $statuses = get_terms([
				'taxonomy'   => 'wpsc_statuses',
				'hide_empty' => false,
				'orderby'    => 'meta_value_num',
				'order'    	 => 'ASC',
				'exclude'    => $request_ecms_tag->term_id,
				'meta_query' => array('order_clause' => array('key' => 'wpsc_status_load_order')),
			]);
            
            } else {
            $statuses = get_terms([
				'taxonomy'   => 'wpsc_statuses',
				'hide_empty' => false,
				'orderby'    => 'meta_value_num',
				'order'    	 => 'ASC',
				'exclude'    => $request_sems_tag->term_id,
				'meta_query' => array('order_clause' => array('key' => 'wpsc_status_load_order')),
			]);
			
            }
			//PATT END          
      foreach ( $statuses as $status ) :
				$selected = $status_id == $status->term_id ? 'selected="selected"' : '';
				//PATT BEGIN
                $disabled = '';
                $hidden_status = '<input type="hidden" name="status" id="status" value="'.$status_id.'" />';

                if ( in_array($status->term_id, array($request_new_request_tag->term_id)) ) {
                    $disabled = 'disabled';
                } 
/*
                if (Patt_Custom_Func::check_initial_review_complete( $ticket_id ) == 1) {
                if ( in_array($status->term_id, array($request_tabled_tag->term_id)) ) {
                    $disabled = 'disabled';
                }
                }  
*/
if($agent_type == 'Manager') {

/*
                if( !$is_ext_shipping ) {
                  if (in_array($status->term_id, $shipping_array)) {
                      $disabled = 'disabled';
                  }
                }
                
                if(in_array("1", $r3_check)) {
                  if (in_array($status->term_id, $r3_array)) {
                      $disabled = 'disabled';
                  }
                }
                
                if (in_array($status_id, $pre_received_array) && in_array($status->term_id, $post_received_array)) {
                    $disabled = 'disabled';
                }
*/


                if ($status_id == $request_cancelled_tag->term_id && in_array($status->term_id, $cancelled_array)) {
                    $disabled = 'disabled';
                }
                
                if ($status_id == $request_completed_dispositioned_tag->term_id && in_array($status->term_id, $completed_array)) {
                    $disabled = 'disabled';
                }
                
                if (($status_id == $request_sems_tag->term_id || $status_id == $request_ecms_tag->term_id) && in_array($status->term_id, $ecms_sems_array)) {
                    $disabled = 'disabled';
                }
                
                if ($status_id == $request_in_process_tag->term_id && in_array($status->term_id, $inprocess_array)) {
                    $disabled = 'disabled';
                }
                
                if ($status_id == $request_received_tag->term_id && in_array($status->term_id, $received_array)) {
                    $disabled = 'disabled';
                }
                
                if(in_array("1", $r3_check)) {
                if ($status_id == $request_shipped_tag->term_id && in_array($status->term_id, $shipped_array)) {
                    $disabled = 'disabled';
                }         
                } else {
                if ($status_id == $request_shipped_tag->term_id && in_array($status->term_id, $r3_shipped_array)) {
                    $disabled = 'disabled';
                } 
                
                }
                

                if ($status_id == $request_initial_review_rejected_tag->term_id && in_array($status->term_id, $rejected_array)) {
                    $disabled = 'disabled';
                }     
                
                
                if(in_array("1", $r3_check)) {
                if ($status_id == $request_initial_review_complete_tag->term_id && in_array($status->term_id, $r3_complete_array)) {
                    $disabled = 'disabled';
                }            
                } else {
                if ($status_id == $request_initial_review_complete_tag->term_id && in_array($status->term_id, $complete_array)) {
                    $disabled = 'disabled';
                } 
                
                }
                
                if ($status_id == $request_tabled_tag->term_id && in_array($status->term_id, $tabled_array)) {
                    $disabled = 'disabled';
                }  
                
                if ($status_id == $request_new_request_tag->term_id && in_array($status->term_id, $new_array)) {
                    $disabled = 'disabled';
                }  
                
}
                echo '<option '.$selected.' value="'.$status->term_id.'" '.$disabled.'>'.$wpsc_custom_status_localize['custom_status_'.$status->term_id].'</option>';
                
                //PATT END
			endforeach;
			?>
		</select>
		<?php echo $hidden_status; ?>
	</div>
<?php
$rejected_comment_check = $wpscfunction->get_ticket_meta($ticket_id,'rejected_comment');
$rejected_comment = implode(" ",$rejected_comment_check);
?>
<textarea style="width: 100%; max-width: 100%;" name="reject_comment" id="reject_comment" placeholder="<?php if(empty($rejected_comment_check)) { echo 'Enter reject reason here...'; } ?>">
<?php
if(!empty($rejected_comment_check)) {
echo $rejected_comment;
}
?>
</textarea>

<?php
//PATT BEGIN
$box_details = $wpdb->get_results(
"SELECT wpqa_terms.term_id as digitization_center, location_status_id as location
FROM wpqa_wpsc_epa_boxinfo
INNER JOIN wpqa_wpsc_epa_storage_location ON wpqa_wpsc_epa_boxinfo.storage_location_id = wpqa_wpsc_epa_storage_location.id
INNER JOIN wpqa_terms ON  wpqa_terms.term_id = wpqa_wpsc_epa_storage_location.digitization_center
WHERE wpqa_wpsc_epa_boxinfo.ticket_id = '" . $ticket_id . "'"
			);
$dc_array = array();
$pl_array = array();
foreach ($box_details as $info) {
$dc_details = $info->digitization_center;
$physical_location = $info->location;
array_push($dc_array, $dc_details);
array_push($pl_array, $physical_location);
}

if (count(array_keys($dc_array, $priority_not_assigned_tag->term_id)) == count($dc_array) && !in_array('-99999', $pl_array) && !in_array(6, $pl_array)) {
//PATT END
?>
<!--
	<div class="form-group">
		<label for="wpsc_default_ticket_category"><?php _e('Ticket Category','supportcandy');?></label>
		<select class="form-control" name="category" >
			<?php
			$categories = get_terms([
				'taxonomy'   => 'wpsc_categories',
				'hide_empty' => false,
				'orderby'    => 'meta_value_num',
				'order'    	 => 'ASC',
				'meta_query' => array('order_clause' => array('key' => 'wpsc_category_load_order')),
			]);
			foreach ( $categories as $category ) :
				//PATT
				$selected = Patt_Custom_Func::get_default_digitization_center($ticket_id) == $category->term_id ? 'selected="selected"' : '';
				echo '<option '.$selected.' value="'.$category->term_id.'">'.$wpsc_custom_category_localize['custom_category_'.$category->term_id].'</option>';
			endforeach;
			?>
		</select>
	</div>

	<div class="form-group">
		<label for="wpsc_default_ticket_category"><?php _e('Ticket Category','supportcandy');?></label>
		<select class="form-control" name="category" >
<?php
//Digitization centers
?>
<option value="<?php echo $dc_not_assigned_tag->term_id; ?>">Not Assigned</option>
<option selected="selected" value="<?php echo $dc_east_tag->term_id; ?>">East</option>
<option value="<?php echo $dc_east_cui_tag->term_id; ?>" disabled>East CUI</option>
<option value="<?php echo $dc_west_tag->term_id; ?>" disabled>West</option>
<option value="<?php echo $dc_west_cui_tag->term_id; ?>" disabled>West CUI</option>
		</select>
	</div>
-->
<?php
//PATT BEGIN
} else {
//PATT END

//CHANGE WHEN PATT DB SETUP TO SUPPORT DIGTIZATION CENTER WEST 
echo '<input type="hidden" name="category" value="'.Patt_Custom_Func::get_default_digitization_center($ticket_id).'">';
//echo '<input type="hidden" name="category" value="62">';

//PATT BEGIN
}
//PATT END
?>

	<div class="form-group">
		<label for="wpsc_default_ticket_priority"><?php _e('Ticket priority','supportcandy');?></label>
		<!--PATT BEGIN -->
		<select class="form-control" name="priority" aria-label="Request Priority">
		<!--PATT END -->
			<?php
			$priorities = get_terms([
				'taxonomy'   => 'wpsc_priorities',
				'hide_empty' => false,
				'orderby'    => 'meta_value_num',
				'order'    	 => 'ASC',
				'meta_query' => array('order_clause' => array('key' => 'wpsc_priority_load_order')),
			]);
			
			//PATT BEGIN
			$key = array_search('Critical', array_column($priorities, 'name'));
			if ($agent_type == 'Agent')
            {
                 unset($priorities[$key]);
            }
			//PATT END
			
			foreach ( $priorities as $priority ) :
				$selected = $priority_id == $priority->term_id ? 'selected="selected"' : '';
				//PATT BEGIN
				if ($priority->term_id == 9) {
				echo '<option '.$selected.' style="background: #FFD0C2; font-weight: bold;" value="'.$priority->term_id.'">'.$wpsc_custom_priority_localize['custom_priority_'.$priority->term_id].'</option>';
				} else {
				echo '<option '.$selected.' value="'.$priority->term_id.'">'.$wpsc_custom_priority_localize['custom_priority_'.$priority->term_id].'</option>';				
				}
				//PATT END
			endforeach;
			?>
		</select>
	</div>
	<?php do_action('wpsc_after_edit_change_ticket_status',$ticket_id);?>
  <input type="hidden" name="action" value="wpsc_tickets" />
	<input type="hidden" name="setting_action" value="set_change_ticket_status" />
  <input type="hidden" id="wpsc_post_id" name="ticket_id" value="<?php echo htmlentities($ticket_id) ?>" />
	

</form>
<?php
$body = ob_get_clean();

ob_start();
?>
<button type="button" class="btn wpsc_popup_close"  style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_text_color']?> !important;"    onclick="wpsc_modal_close();"><?php _e('Close','supportcandy');?></button>
<button type="button" id="save-button" class="btn wpsc_popup_action" style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_text_color']?> !important;"><?php _e('Save','supportcandy');?></button>
<div id="sending_notification" style="display:none;"><img id="loading-gif" src="<?php echo WPSC_PLUGIN_URL.'asset/images/ajax-loader@2x.gif'; ?>"></div>
<script>
// PATT BEGIN

jQuery(document).ready(function() {

jQuery("#save-button").click(function () {

var request_status = jQuery('[name=status]').val();

if(request_status == <?php echo $request_initial_review_complete_tag->term_id; ?>) {

jQuery("#sending_notification").css('display', 'inline-block');
jQuery("#loading-gif").css('width', '25px');
jQuery("#loading-gif").css('height', 'auto');
jQuery("#save-button").html( 'Processing...' );

jQuery.ajax({
    url: '<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/auto_assignment.php',
    type: "POST",
    data: { 
        "postvartktid": '<?php echo $ticket_id ?>',
        "postvardcname": jQuery("[name=category]").val()
    },
    success: function(response) {
    //alert(response);
    wpsc_set_change_ticket_status(<?php echo htmlentities($ticket_id)?>);
    jQuery("#sending_notification").css('display', 'none');
    //wpsc_open_ticket('<?php echo $ticket_id ?>');
    },
    error: function(xhr) {
    console.log('auto assignment processing error');  
    }
});
} else if (request_status == <?php echo $request_initial_review_rejected_tag->term_id; ?>) {

jQuery("#sending_notification").css('display', 'inline-block');
jQuery("#loading-gif").css('width', '25px');
jQuery("#loading-gif").css('height', 'auto');
jQuery("#save-button").html( 'Processing...' );

jQuery.ajax({
    url: '<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/rejected_processing.php',
    type: "POST",
    data: { 
        "postvartktid": '<?php echo $ticket_id ?>', 
        "postvarcomment": jQuery("[name=reject_comment]").val(), 
        "postvardcname": jQuery("[name=category]").val()
    },
    success: function(response) {
    //alert(response);
    wpsc_set_change_ticket_status(<?php echo htmlentities($ticket_id)?>);
    jQuery("#sending_notification").css('display', 'none');
    //wpsc_open_ticket('<?php echo $ticket_id ?>');
    },
    error: function(xhr) {
    console.log('rejected processing error');  
    }
});
} else if (request_status == <?php echo $request_cancelled_tag->term_id; ?>) {

jQuery("#sending_notification").css('display', 'inline-block');
jQuery("#loading-gif").css('width', '25px');
jQuery("#loading-gif").css('height', 'auto');
jQuery("#save-button").html( 'Processing...' );

jQuery.ajax({
    url: '<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/cancelled_processing.php',
    type: "POST",
    data: { 
        "postvartktid": '<?php echo $ticket_id ?>', 
        "postvarcomment": jQuery("[name=reject_comment]").val(), 
        "postvardcname": jQuery("[name=category]").val()
    },
    success: function(response) {
    //alert(response);
    wpsc_set_change_ticket_status(<?php echo htmlentities($ticket_id)?>);
    jQuery("#sending_notification").css('display', 'none');
    //wpsc_open_ticket('<?php echo $ticket_id ?>');
    },
    error: function(xhr) {
    console.log('cancelled processing error');  
    }
});
} else {
    wpsc_set_change_ticket_status(<?php echo htmlentities($ticket_id)?>);
    //wpsc_open_ticket(<?php echo htmlentities($ticket_id)?>);
    jQuery("#sending_notification").css('display', 'none');
}

});


jQuery("#edit_status").change(function() {

    var value = jQuery(this).val();

    jQuery('#status').val(value);

});

       jQuery("#comment-alert").hide();
       
        var request_status = jQuery('[name=status]').val();

       if(request_status == <?php echo $request_initial_review_rejected_tag->term_id; ?>) {
       jQuery('#reject_comment').show();
       } else {
       jQuery('#reject_comment').hide(); 
       }
       
jQuery('#reject_comment').bind('input propertychange', function() {

        jQuery(".wpsc_popup_action").hide();
        jQuery("#comment-alert").show();
        
      if(request_status == <?php echo $request_initial_review_rejected_tag->term_id; ?> && this.value.length){
        jQuery(".wpsc_popup_action").show();
        jQuery("#comment-alert").hide();
      }
});


jQuery('[name=edit_status]').on('change', function() {
request_status = jQuery('[name=status]').val();
       
       if(request_status == <?php echo $request_initial_review_rejected_tag->term_id; ?>) {
       jQuery('#reject_comment').show();
       jQuery(".wpsc_popup_action").hide();
       jQuery("#comment-alert").show();
       
var reject_comment = '<?php $rejected_comment_check = $wpscfunction->get_ticket_meta($ticket_id,'rejected_comment'); $rejected_comment = implode(" ",$rejected_comment_check); echo $rejected_comment;?>';

jQuery('#reject_comment').bind('input propertychange', function() {

        jQuery(".wpsc_popup_action").hide();
        jQuery("#comment-alert").show();
        
      if(request_status == <?php echo $request_initial_review_rejected_tag->term_id; ?> && this.value.length){
        jQuery(".wpsc_popup_action").show();
        jQuery("#comment-alert").hide();
      }
});

       } else {
       jQuery('#reject_comment').hide();
       jQuery(".wpsc_popup_action").show();
       jQuery("#comment-alert").hide();
       }
});


});
// PATT END
</script>

<?php
$footer = ob_get_clean();

$output = array(
  'body'   => $body,
  'footer' => $footer
);

echo json_encode($output);
