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
    
    $box_cancelled_tag = get_term_by('slug', 'cancelled', 'wpsc_box_statuses');
    
    $new_tag = get_term_by('slug', 'open', 'wpsc_statuses');
    $tabled_tag = get_term_by('slug', 'tabled', 'wpsc_statuses');
    $complete_tag = get_term_by('slug', 'awaiting-customer-reply', 'wpsc_statuses');
    $rejected_tag = get_term_by('slug', 'initial-review-rejected', 'wpsc_statuses');
    $shipped_tag = get_term_by('slug', 'awaiting-agent-reply', 'wpsc_statuses');   
    
    $pre_received_array = array($new_tag->term_id,$complete_tag->term_id,$rejected_tag->term_id,$shipped_tag->term_id);

    $received_tag = get_term_by('slug', 'received', 'wpsc_statuses');

    $ecms_tag = get_term_by('slug', 'ecms', 'wpsc_statuses');
    $sems_tag = get_term_by('slug', 'sems', 'wpsc_statuses');
    $inprocess_tag = get_term_by('slug', 'in-process', 'wpsc_statuses');
    $completed_tag = get_term_by('slug', 'completed-dispositioned', 'wpsc_statuses');
    $cancelled_tag = get_term_by('slug', 'destroyed', 'wpsc_statuses');
    
    $post_received_array = array($ecms_tag->term_id,$inprocess_tag->term_id,$completed_tag->term_id);

    $shipping_array = array($shipped_tag->term_id,$received_tag->term_id);
    
    $r3_array = array($shipped_tag->term_id,$complete_tag->term_id);
    
    $is_ext_shipping = Patt_Custom_Func::using_ext_shipping( $ticket_id );
    
    $cancelled_array = array($new_tag->term_id,$tabled_tag->term_id,$complete_tag->term_id,$rejected_tag->term_id,$shipped_tag->term_id,$received_tag->term_id,$inprocess_tag->term_id,$ecms_tag->term_id,$sems_tag->term_id,$completed_tag->term_id);
    $completed_array = array($new_tag->term_id,$tabled_tag->term_id,$complete_tag->term_id,$rejected_tag->term_id,$shipped_tag->term_id,$received_tag->term_id,$inprocess_tag->term_id,$ecms_tag->term_id,$sems_tag->term_id,$cancelled_tag->term_id);
    $ecms_sems_array = array($new_tag->term_id,$complete_tag->term_id,$rejected_tag->term_id,$shipped_tag->term_id,$received_tag->term_id,$inprocess_tag->term_id,$ecms_tag->term_id,$sems_tag->term_id,$cancelled_tag->term_id);
    $inprocess_array = array($new_tag->term_id,$complete_tag->term_id,$rejected_tag->term_id,$shipped_tag->term_id,$received_tag->term_id);
    $received_array = array($new_tag->term_id,$complete_tag->term_id,$rejected_tag->term_id,$shipped_tag->term_id,$ecms_tag->term_id,$sems_tag->term_id,);
 
    //Note to do for all external shipping
    $shipped_array = array($new_tag->term_id,$complete_tag->term_id,$rejected_tag->term_id,$inprocess_tag->term_id,$received_tag->term_id,$ecms_tag->term_id,$sems_tag->term_id,$completed_tag->term_id);
    $r3_shipped_array = array($new_tag->term_id,$complete_tag->term_id,$rejected_tag->term_id,$inprocess_tag->term_id,$ecms_tag->term_id,$sems_tag->term_id,$completed_tag->term_id);
   
    $rejected_array = array($new_tag->term_id,$complete_tag->term_id,$shipped_tag->term_id,$received_tag->term_id,$inprocess_tag->term_id,$ecms_tag->term_id,$sems_tag->term_id,$completed_tag->term_id);
    
    $complete_array = array($new_tag->term_id,$complete_tag->term_id,$rejected_tag->term_id,$shipped_tag->term_id,$received_tag->term_id,$inprocess_tag->term_id,$ecms_tag->term_id,$sems_tag->term_id,$completed_tag->term_id);
    $r3_complete_array = array($new_tag->term_id,$complete_tag->term_id,$rejected_tag->term_id,$shipped_tag->term_id,$inprocess_tag->term_id,$ecms_tag->term_id,$sems_tag->term_id,$completed_tag->term_id);
    
    $tabled_array = array($new_tag->term_id,$shipped_tag->term_id,$received_tag->term_id,$ecms_tag->term_id,$sems_tag->term_id,);
    $new_array = array($new_tag->term_id,$shipped_tag->term_id,$received_tag->term_id,$inprocess_tag->term_id,$ecms_tag->term_id,$sems_tag->term_id,$completed_tag->term_id);


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
				'exclude'    => $ecms_tag->term_id,
				'meta_query' => array('order_clause' => array('key' => 'wpsc_status_load_order')),
			]);
            
            } else {
            $statuses = get_terms([
				'taxonomy'   => 'wpsc_statuses',
				'hide_empty' => false,
				'orderby'    => 'meta_value_num',
				'order'    	 => 'ASC',
				'exclude'    => $sems_tag->term_id,
				'meta_query' => array('order_clause' => array('key' => 'wpsc_status_load_order')),
			]);
			
            }
			//PATT END          
      foreach ( $statuses as $status ) :
				$selected = $status_id == $status->term_id ? 'selected="selected"' : '';
				//PATT BEGIN
                $disabled = '';
                $hidden_status = '<input type="hidden" name="status" id="status" value="'.$status_id.'" />';

                if ( in_array($status->term_id, array($new_tag->term_id)) ) {
                    $disabled = 'disabled';
                } 
                
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


                if ($status_id == $cancelled_tag->term_id && in_array($status->term_id, $cancelled_array)) {
                    $disabled = 'disabled';
                }
                
                if ($status_id == $completed_tag->term_id && in_array($status->term_id, $completed_array)) {
                    $disabled = 'disabled';
                }
                
                if (($status_id == $sems_tag->term_id || $status_id == $ecms_tag->term_id) && in_array($status->term_id, $ecms_sems_array)) {
                    $disabled = 'disabled';
                }
                
                if ($status_id == $inprocess_tag->term_id && in_array($status->term_id, $inprocess_array)) {
                    $disabled = 'disabled';
                }
                
                if ($status_id == $received_tag->term_id && in_array($status->term_id, $received_array)) {
                    $disabled = 'disabled';
                }
                
                if(in_array("1", $r3_check)) {
                if ($status_id == $shipped_tag->term_id && in_array($status->term_id, $shipped_array)) {
                    $disabled = 'disabled';
                }         
                } else {
                if ($status_id == $shipped_tag->term_id && in_array($status->term_id, $r3_shipped_array)) {
                    $disabled = 'disabled';
                } 
                
                }
                

                if ($status_id == $rejected_tag->term_id && in_array($status->term_id, $rejected_array)) {
                    $disabled = 'disabled';
                }     
                
                
                if(in_array("1", $r3_check)) {
                if ($status_id == $complete_tag->term_id && in_array($status->term_id, $r3_complete_array)) {
                    $disabled = 'disabled';
                }            
                } else {
                if ($status_id == $complete_tag->term_id && in_array($status->term_id, $complete_array)) {
                    $disabled = 'disabled';
                } 
                
                }
                
                if ($status_id == $tabled_tag->term_id && in_array($status->term_id, $tabled_array)) {
                    $disabled = 'disabled';
                }  
                
                if ($status_id == $new_tag->term_id && in_array($status->term_id, $new_array)) {
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

$not_assigned_tag = get_term_by('slug', 'not-assigned', 'wpsc_priorities');

if (count(array_keys($dc_array, $not_assigned_tag->term_id)) == count($dc_array) && !in_array('-99999', $pl_array) && !in_array(6, $pl_array)) {
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
$not_assigned_tag = get_term_by('slug', 'not-assigned-digi-center', 'wpsc_categories');
$east_tag = get_term_by('slug', 'e', 'wpsc_categories');
$east_cui_tag = get_term_by('ecui', 'medium', 'wpsc_categories');
$west_tag = get_term_by('slug', 'w', 'wpsc_categories');
$west_cui_tag = get_term_by('slug', 'wcui', 'wpsc_categories');
?>
<option value="<?php echo $not_assigned_tag->term_id; ?>">Not Assigned</option>
<option selected="selected" value="<?php echo $east_tag->term_id; ?>">East</option>
<option value="<?php echo $east_cui_tag->term_id; ?>" disabled>East CUI</option>
<option value="<?php echo $west_tag->term_id; ?>" disabled>West</option>
<option value="<?php echo $west_cui_tag->term_id; ?>" disabled>West CUI</option>
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
<button type="button" class="btn wpsc_popup_action" style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_text_color']?> !important;" onclick="wpsc_set_change_ticket_status(<?php echo htmlentities($ticket_id)?>);wpsc_open_ticket(<?php echo htmlentities($ticket_id)?>);"><?php _e('Save','supportcandy');?></button>
<script>
// PATT BEGIN
jQuery(document).ready(function() {
 
jQuery("#edit_status").change(function() {

    var value = jQuery(this).val();

    jQuery('#status').val(value);

});

       jQuery("#comment-alert").hide();
       
var request_status = jQuery('[name=status]').val();

        <?php
        $new_request_tag = get_term_by('slug', 'open', 'wpsc_statuses');
        $tabled_request_tag = get_term_by('slug', 'tabled', 'wpsc_statuses');
        $initial_review_rejected_tag = get_term_by('slug', 'initial-review-rejected', 'wpsc_statuses');
        $cancelled_tag = get_term_by('slug', 'destroyed', 'wpsc_statuses');
        ?>
       if(request_status == <?php echo $initial_review_rejected_tag->term_id; ?>) {
       jQuery('#reject_comment').show();
       } else {
       jQuery('#reject_comment').hide(); 
       }
       
jQuery('#reject_comment').bind('input propertychange', function() {

        jQuery(".wpsc_popup_action").hide();
        jQuery("#comment-alert").show();
        
      if(request_status == <?php echo $initial_review_rejected_tag->term_id; ?> && this.value.length){
        jQuery(".wpsc_popup_action").show();
        jQuery("#comment-alert").hide();
      }
});


jQuery('[name=edit_status]').on('change', function() {
request_status = jQuery('[name=status]').val();
       
       if(request_status == <?php echo $initial_review_rejected_tag->term_id; ?>) {
       jQuery('#reject_comment').show();
       jQuery(".wpsc_popup_action").hide();
       jQuery("#comment-alert").show();
       
var reject_comment = '<?php $rejected_comment_check = $wpscfunction->get_ticket_meta($ticket_id,'rejected_comment'); $rejected_comment = implode(" ",$rejected_comment_check); echo $rejected_comment;?>';

jQuery('#reject_comment').bind('input propertychange', function() {

        jQuery(".wpsc_popup_action").hide();
        jQuery("#comment-alert").show();
        
      if(request_status == <?php echo $initial_review_rejected_tag->term_id; ?> && this.value.length){
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


jQuery(".wpsc_popup_action").click(function () {

if(request_status == <?php echo $new_request_tag->term_id; ?> || request_status == <?php echo $tabled_request_tag->term_id; ?>) {
jQuery.post(
'<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/dc_assignment.php',{
postvartktid: '<?php echo $ticket_id ?>',
postvardcname: jQuery("[name=category]").val()
},
function (response) {
});
}

if(request_status == <?php echo $new_request_tag->term_id; ?> || request_status == <?php echo $tabled_request_tag->term_id; ?> || request_status == <?php echo $initial_review_rejected_tag->term_id; ?> || request_status == <?php echo $cancelled_tag->term_id; ?>) {
    //alert('No automatic shelf assignments made.');
} else {
jQuery.post(
'<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/auto_assignment.php',{
postvartktid: '<?php echo $ticket_id ?>',
postvardcname: jQuery("[name=category]").val()
},
function (response) {

		let data = {
			action: 'wppatt_loc_instant',
			dc_id : jQuery("[name=category]").val()
		}
		
		jQuery.ajax({
			type: "POST",
			url: wpsc_admin.ajax_url,
			data: data,
			success: function( response ){
				console.log('update location done');
				console.log( response );	
			}
		
		});		

alert(response);
//if(jQuery("[name='category']").val()) {
//alert(response);
//}
});
}

jQuery.post(
'<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/rejected_processing.php',{
postvartktid: '<?php echo $ticket_id ?>',
postvarstatus: jQuery("[name=status]").val(),
postvarcomment: jQuery("[name=reject_comment]").val()
},
function (response) {
});

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
