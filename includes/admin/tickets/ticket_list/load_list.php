<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb, $current_user, $wpscfunction;

$general_appearance = get_option('wpsc_appearance_general_settings');

$create_ticket_btn_css       = 'background-color:'.$general_appearance['wpsc_crt_ticket_btn_action_bar_bg_color'].' !important;color:'.$general_appearance['wpsc_crt_ticket_btn_action_bar_text_color'].' !important;';
$action_default_btn_css      = 'background-color:'.$general_appearance['wpsc_default_btn_action_bar_bg_color'].' !important;color:'.$general_appearance['wpsc_default_btn_action_bar_text_color'].' !important;';
$logout_btn_css              = 'background-color:'.$general_appearance['wpsc_sign_out_bg_color'].' !important;color:'.$general_appearance['wpsc_sign_out_text_color'].' !important;';
$wpsc_show_and_hide_filters  = get_option('wpsc_show_and_hide_filters');
$wpsc_appearance_ticket_list = get_option('wpsc_appearance_ticket_list');

$wpsc_on_and_off_auto_refresh = get_option('wpsc_on_and_off_auto_refresh');
$agent_permissions = $wpscfunction->get_current_agent_permissions();
//include WPSC_ABSPATH.'includes/admin/tickets/ticket_list/filters/get_label_count.php';

include WPPATT_ABSPATH.'includes/admin/pages/scripts/request_cleanup.php';

?>
<style>

div.dataTables_processing { 
    z-index: 1; 
}

div.dataTables_wrapper {
        width: 100%;
        margin: 0;
    }

.bootstrap-iso label {
    margin-top: 5px;
}
.datatable_header {
background-color: rgb(66, 73, 73) !important; 
color: rgb(255, 255, 255) !important; 
}

.bootstrap-tagsinput {
   width: 100%;
  }

#searchGeneric {
    padding: 0 30px !important;
}

.update-plugins {
    display: inline-block;
    vertical-align: top;
    box-sizing: border-box;
    margin: 1px 0 -1px 2px;
    padding: 0 5px;
    min-width: 18px;
    height: 18px;
    border-radius: 9px;
    background-color: #ca4a1f;
    color: #fff;
    font-size: 11px;
    line-height: 1.6;
    text-align: center;
    z-index: 26;
}
.remove-user {
    padding-left:5px;
}

.staff-badge {
	padding: 3px 3px 3px 5px;
	font-size:1.0em !important;
	vertical-align: middle;
}

.staff-close {
	margin-left: 3px;
	margin-right: 3px;
}

.large-tooltip .tooltip-inner {
    max-width: 325px !important;
}

@media only screen and (max-width: 768px) {
#btn_location_scanner_desktop {
  display: none !important;
}
}

@media screen and (min-width: 769px) {
#btn_location_scanner_mobile {
  display: none !important;
}  
}

</style>
<div class="row wpsc_tl_action_bar" style="background-color:<?php echo $general_appearance['wpsc_action_bar_color']?> !important;">
  <div class="col-sm-12">
  	        <button type="button" id="wpsc_load_new_create_ticket_btn" onclick="wpsc_get_create_ticket();" class="btn btn-sm wpsc_create_ticket_btn" style="<?php echo $create_ticket_btn_css?>"><i class="fa fa-plus" aria-hidden="true" title="New Request"></i><span class="sr-only">New Request</span> <?php _e('New Ticket','supportcandy')?></button>
        <button type="button" id="wpsc_individual_ticket_list_btn" onclick="location.href='admin.php?page=wpsc-tickets';" class="btn btn-sm wpsc_action_btn" style="<?php echo $action_default_btn_css?>"><i class="fa fa-list-ul" aria-hidden="true" title="Request List"></i><span class="sr-only">Request List</span> <?php _e('Ticket List','supportcandy')?> <a href="#" data-toggle="tooltip" data-placement="right" data-html="true" title="<?php echo Patt_Custom_Func::helptext_tooltip('help-request-list-button'); ?>" aria-label="Request Help"><i class="far fa-question-circle" aria-hidden="true" title="Help"></i><span class="sr-only">Help</span></a></button>
		<button type="button" class="btn btn-sm wpsc_action_btn" id="wpsc_individual_refresh_btn" style="<?php echo $action_default_btn_css?> margin-right: 30px !important;"><i class="fas fa-retweet" aria-hidden="true" title="Reset Filters"></i><span class="sr-only">Reset Filters</span> <?php _e('Reset Filters','supportcandy')?></button>
<?php		
if (($agent_permissions['label'] == 'Administrator') || ($agent_permissions['label'] == 'Agent') || ($agent_permissions['label'] == 'Manager'))
{
?>
<button type="button" class="btn btn-sm wpsc_btn_bulk_action wpsc_action_btn checkbox_depend" id="btn_delete_tickets" style="<?php echo $action_default_btn_css?>"><i class="fa fa-trash" aria-hidden="true" title="Archive"></i><span class="sr-only">Archive</span> <?php _e('Archive','supportcandy')?></button>
<button type="button" class="btn btn-sm wpsc_btn_bulk_action wpsc_action_btn checkbox_depend" id="btn_location_scanner_mobile" style="<?php echo $action_default_btn_css?>" onclick="window.location.href = '<?php echo WP_HOME . '/barcode-location';?>'"><i class="fas fa-barcode" aria-hidden="true" title="Barcode Scanner"></i><span class="sr-only">Barcode Scanner</span> Barcode Scanner</button>
<button type="button" class="btn btn-sm wpsc_btn_bulk_action wpsc_action_btn checkbox_depend" id="btn_location_scanner_desktop" style="<?php echo $action_default_btn_css?>" onclick="window.location.href = '<?php echo WP_HOME . '/barcode-manual-location';?>'"><i class="fas fa-barcode" aria-hidden="true" title="Barcode Scanner"></i><span class="sr-only">Barcode Scanner Manual</span> Barcode Scanner</button>
<?php
}
?>		
  </div>
</div>

<div class="row" style="background-color:<?php echo $general_appearance['wpsc_bg_color']?> !important;color:<?php echo $general_appearance['wpsc_text_color']?> !important;">

	<div class="col-sm-4 col-md-3 wpsc_sidebar individual_ticket_widget">

							<div class="row" id="wpsc_status_widget" style="background-color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_bg_color']?> !important;color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_text_color']?> !important;border-color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_border_color']?> !important;">
					      <h4 class="widget_header"><i class="fa fa-filter" aria-hidden="true" title="Filters"></i><span class="sr-only">Filters</span> Filters <a href="#" data-toggle="tooltip" data-placement="right" data-html="true" title="<?php echo Patt_Custom_Func::helptext_tooltip('help-filters'); ?>" aria-label="Help Filters"><i class="far fa-question-circle" aria-hidden="true" title="Help"></i><span class="sr-only">Help</span></a>
								</h4>
								<hr class="widget_divider">

	                            <div class="wpsp_sidebar_labels">
Enter one or more Request IDs:<br />
         <input type='text' id='searchByRequestID' class="form-control" data-role="tagsinput" aria-label="Search by Request ID">
<br />

<?php
//Request statuses
$new_request_tag = get_term_by('slug', 'open', 'wpsc_statuses');
$tabled_tag = get_term_by('slug', 'tabled', 'wpsc_statuses');
$initial_review_complete_tag = get_term_by('slug', 'awaiting-customer-reply', 'wpsc_statuses');
$initial_review_rejected_tag = get_term_by('slug', 'initial-review-rejected', 'wpsc_statuses');
$shipped_tag = get_term_by('slug', 'awaiting-agent-reply', 'wpsc_statuses');
$received_tag = get_term_by('slug', 'received', 'wpsc_statuses');
$in_progress_tag = get_term_by('slug', 'in-process', 'wpsc_statuses');
$ecms_tag = get_term_by('slug', 'ecms', 'wpsc_statuses');
$sems_tag = get_term_by('slug', 'sems', 'wpsc_statuses');
$cancelled_tag = get_term_by('slug', 'destroyed', 'wpsc_statuses');
$completed_dispositioned_tag = get_term_by('slug', 'completed-dispositioned', 'wpsc_statuses');

//Priorities
$not_assigned_tag = get_term_by('slug', 'not-assigned', 'wpsc_priorities');
$normal_tag = get_term_by('slug', 'low', 'wpsc_priorities');
$high_tag = get_term_by('slug', 'medium', 'wpsc_priorities');
$critical_tag = get_term_by('slug', 'high', 'wpsc_priorities');
?>

        <select id='searchByStatus' aria-label="Search by Status">
           <option value=''>-- Select Status --</option>
			<option value="<?php echo $new_request_tag->term_id; ?>">New Request</option>
			<option value="<?php echo $tabled_tag->term_id; ?>">Tabled</option>
			<option value="<?php echo $initial_review_complete_tag->term_id; ?>">Initial Review Complete</option>
			<option value="<?php echo $initial_review_rejected_tag->term_id; ?>">Initial Review Rejected</option>
			<option value="<?php echo $shipped_tag->term_id; ?>">Shipped</option>
			<option value="<?php echo $received_tag->term_id; ?>">Received</option>
			<option value="<?php echo $in_progress_tag->term_id; ?>">In Process</option>
			<option value="<?php echo $ecms_tag->term_id; ?>">ARMS</option>
			<option value="<?php echo $sems_tag->term_id; ?>">SEMS</option>
			<option value="<?php echo $completed_dispositioned_tag->term_id; ?>">Completed/Dispositioned</option>
			<option value="<?php echo $cancelled_tag->term_id; ?>">Cancelled</option>
         </select>
<br /><br />
        <select id='searchByPriority' aria-label="Search by Priority">
           <option value=''>-- Select Priority --</option>
			<option value="<?php echo $not_assigned_tag->term_id; ?>">Not Assigned</option>
			<option value="<?php echo $normal_tag->term_id; ?>">Normal</option>
			<option value="<?php echo $high_tag->term_id; ?>">High</option>
			<option value="<?php echo $critical_tag->term_id; ?>">Critical</option>
         </select>
<br /><br />

<?php	
$user_digitization_center = get_user_meta( $current_user->ID, 'user_digization_center',true);

if ( !empty($user_digitization_center) && $user_digitization_center == 'East' && $agent_permissions['label'] == 'Agent') { 
?>
<input type="hidden" id="searchByDigitizationCenter" value="East" />
<?php 
} 
?>

<?php
if ( !empty($user_digitization_center) && $user_digitization_center == 'West' && $agent_permissions['label'] == 'Agent') { 
?>
<input type="hidden" id="searchByDigitizationCenter" value="West" />
<?php 
} 
?>

<?php
if ( !empty($user_digitization_center) && (($agent_permissions['label'] == 'Administrator') || ($agent_permissions['label'] == 'Manager'))) { 
?>
				<select id='searchByDigitizationCenter' aria-label="Search by Digitization Center">
					<option value=''>-- Select Digitization Center --</option>
					<option value='East' <?php if(!empty($user_digitization_center) && $user_digitization_center == 'East'){ echo 'selected'; } ?>>East</option>
					<option value='West' <?php if(!empty($user_digitization_center) && $user_digitization_center == 'West'){ echo 'selected'; } ?>>West</option>
					<option value='Not Assigned'>Not Assigned</option>
				</select>
    <br /><br />
<?php 
} elseif(($agent_permissions['label'] == 'Requester') || ($agent_permissions['label'] == 'Requester Pallet')) {
?>
				<select id='searchByDigitizationCenter' aria-label="Search by Digitization Center">
					<option value=''>-- Select Digitization Center --</option>
					<option value='East'>East</option>
					<option value='West'>West</option>
					<option value='Not Assigned'>Not Assigned</option>
				</select>
    <br /><br />
<?php
}
?>	

        <select id='searchByRecallDecline' aria-label='Search by Recall or Decline'>
               <option value=''>-- Select Recall or Decline --</option>
               <option value='Recall'>Recall</option>
               <option value='Decline'>Decline</option>
             </select>
<br /><br />
<!-- ECMS has been updated to be called ARMS instead -->
<select id='searchByECMSSEMS' aria-label='Search by ARMS or SEMS'>
               <option value=''>-- Select ARMS or SEMS --</option>
               <option value='ECMS'>ARMS</option>
               <option value='SEMS'>SEMS</option>
             </select>
<br /><br />
<?php		
if (($agent_permissions['label'] == 'Administrator') || ($agent_permissions['label'] == 'Agent') || ($agent_permissions['label'] == 'Manager'))
{
?>
				<select id='searchByUser' aria-label="Search by User">
					<option value=''>-- Select User --</option>
					<option value='mine'>Mine</option>
					<option value='not assigned'>All Requests</option>
					<option value='search for user'>Search for User</option>
				</select>

	<br /><br />				
				<form id="frm_get_ticket_assign_agent">
					<div id="assigned_agent">
						<div class="form-group wpsc_display_assign_agent ">
						    <input class="form-control  wpsc_assign_agents ui-autocomplete-input "  aria-label="Search digitization staff" name="assigned_agent"  type="text" autocomplete="off" placeholder="<?php _e('Search agent ...','supportcandy')?>" />
							<ui class="wpsp_filter_display_container"></ui>
						</div>
					</div>
					<div id="assigned_agents" class="form-group col-md-12 ">
						<?php
						    if($is_single_item) {
							    foreach ( $assigned_agents as $agent ) {
									$agent_name = get_term_meta( $agent, 'label', true);
									 	
										if($agent && $agent_name):
						?>
												<div class="form-group wpsp_filter_display_element wpsc_assign_agents ">
<!-- 													<div class="flex-container searched-user " style="padding:5px;font-size:1.0em;">  -->
													<div class="flex-container searched-user staff-badge" style=""> 
														<?php echo htmlentities($agent_name); ?><span class="remove-user staff-close"><i class="fa fa-times" aria-hidden="true" title="Remove User"></i><span class="sr-only">Remove User</span></span>
														  <input type="hidden" name="assigned_agent[]" value="<?php echo htmlentities($agent) ?>" />
					<!-- 									  <input type="hidden" name="new_requestor" value="<?php echo htmlentities($agent) ?>" /> -->
													</div>
												</div>
						<?php
										endif;
								}
							}
						?>
				  </div>
						<input type="hidden" name="action" value="wpsc_tickets" />
						<input type="hidden" name="setting_action" value="set_change_assign_agent" />
				</form>	

<br /><?php		

$get_pending_delete_count = $wpdb->get_row(
"SELECT count(id) as count
FROM wpqa_wpsc_ticket
WHERE active = 0 AND id <> -99999"
			);

$pending_delete_count = $get_pending_delete_count->count;

?>
<h4 class="widget_header"><i class="far fa-trash-alt" aria-hidden="true" title="Archive"></i><span class="sr-only">Archive</span> <a href="admin.php?page=request_delete" style="text-decoration: underline;">Archive</a> <?php if ($pending_delete_count > 0) { ?><span class="update-plugins count-<?php echo $pending_delete_count ?>"><span class="update-count"><?php echo $pending_delete_count ?></span></span><?php }?></span>  
<div class="large-tooltip" style="display:inline; padding-left:5px; width: 325px; position: absolute;"><a href="#" id="recycletooltip" data-toggle="tooltip" data-placement="right" data-html="true" title="<?php echo Patt_Custom_Func::helptext_tooltip('help-recycle-bin'); ?>" aria-label="Archive Help"><i class="far fa-question-circle" aria-hidden="true" title="Help"></i><span class="sr-only">Help</span></a></div>

<hr class="widget_divider">
<?php		
} else {
?>
<input type="hidden" id="searchByUser" name="searchByUser" value="mine">
<?php		
}
?>	
<input type="hidden" id="current_user" name="current_user" value="<?php wp_get_current_user(); echo $current_user->display_name; ?>">
<input type="hidden" id="user_search" name="user_search" value="">
	                            </div>
			    		</div>
	
	</div>

	
  <div class="col-sm-8 col-md-9 wpsc_it_body">
<div class="table-responsive" style="overflow-x:auto;">
<input type="text" id="searchGeneric" class="form-control" name="custom_filter[s]" value="" autocomplete="off" placeholder="Search..." aria-label="Search">
<i class="fa fa-search wpsc_search_btn wpsc_search_btn_sarch" aria-hidden="true" title="Search"></i><span class="sr-only">Search</span>
<br /><br />
<table id="tbl_templates_requests" class="display nowrap" cellspacing="5" cellpadding="5" width="100%">
        <thead>
            <tr>
<?php		
if (($agent_permissions['label'] == 'Administrator') || ($agent_permissions['label'] == 'Agent') || ($agent_permissions['label'] == 'Manager'))
{
?>
                <th class="datatable_header" id="selectall" scope="col"></th>
<?php
}
?>
                <th class="datatable_header" scope="col">Request ID</th>
                <th class="datatable_header" scope="col">Priority</th>
                <th class="datatable_header" scope="col">Status</th>
                <th class="datatable_header" scope="col">Name</th></th>
                <th class="datatable_header" scope="col">Location</th>
                <th class="datatable_header" scope="col">Last Updated</th>
            </tr>
        </thead>
    </table>
<br /><br />
</div>


<script>

jQuery(document).ready(function(){
  jQuery('[data-toggle="tooltip"]').tooltip();

  var dataTable = jQuery('#tbl_templates_requests').DataTable({
    'autoWidth': true,
    'processing': true,
    'drawCallback': function( settings ) {

jQuery('[data-toggle="tooltip"]').tooltip();

},
    'processing': true,
    'serverSide': true,
    'stateSave': true,
    'scrollX': true,
    'initComplete': function (settings, json) {
		    jQuery('#selectall').append('<span class="sr-only">Select All</span>');
		},
    'paging' : true,
    'stateSaveParams': function(settings, data) {
      data.ss = jQuery('#searchByStatus').val();
      data.sp = jQuery('#searchByPriority').val();
      data.sg = jQuery('#searchGeneric').val();
      data.rid = jQuery('#searchByRequestID').val();
      data.po = jQuery('#searchByProgramOffice').val();
			<?php
			if (($agent_permissions['label'] == 'Requester') || ($agent_permissions['label'] == 'Requester Pallet'))
            {
			?>      
      data.dc = jQuery('#searchByDigitizationCenter').val();
   			<?php
            }
			?>   
      data.rd = jQuery('#searchByRecallDecline').val();
      data.es = jQuery('#searchByECMSSEMS').val();
      data.sbu = jQuery('#searchByUser').val(); 
	  data.aaVal = jQuery("input[name='assigned_agent[]']").map(function(){return jQuery(this).val();}).get();     
	  data.aaName = jQuery(".searched-user").map(function(){return jQuery(this).text();}).get();  
      
    },
    		'stateLoadParams': function(settings, data) {
      jQuery('#searchByStatus').val(data.ss);
      jQuery('#searchByPriority').val(data.sp);
      jQuery('#searchGeneric').val(data.sg);
      jQuery('#searchByRequestID').val(data.rid);
      jQuery('#searchByProgramOffice').val(data.po);
			<?php
			if (($agent_permissions['label'] == 'Requester') || ($agent_permissions['label'] == 'Requester Pallet'))
            {
			?>      
      jQuery('#searchByDigitizationCenter').val(data.dc);
			<?php
            }
			?>      
      jQuery('#searchByRecallDecline').val(data.rd);
      jQuery('#searchByECMSSEMS').val(data.es);
<?php		
if (($agent_permissions['label'] == 'Administrator') || ($agent_permissions['label'] == 'Agent') || ($agent_permissions['label'] == 'Manager'))
{
?>
			jQuery('#searchByUser').val(data.sbu);
			jQuery('#user_search').val(data.aaName);
			
			// If data values aren't defined then set them as blank arrays.
			if( typeof data.aaVal == 'undefined' ) {
				//console.log('undefined!!!');
				data.aaVal = [];
				data.aaName = [];				
				//console.log(data);
			}
			
			data.aaVal.forEach( function(val, key) {
				let html_str = get_display_user_html(data.aaName[key], val); 
				jQuery('#assigned_agents').append(html_str);
			});
<?php } ?>
		},
    'serverMethod': 'post',
    'searching': false, // Remove default Search Control
    'ajax': {
       'url':'<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/request_processing.php',
       'data': function(data){
          // Read values
		  var sbu = jQuery('#searchByUser').val();  
		  var aaVal = jQuery("input[name='assigned_agent[]']").map(function(){return jQuery(this).val();}).get();     
		  var aaName = jQuery("#user_search").val();	 
          var rs_user = jQuery('#current_user').val();
          var ss = jQuery('#searchByStatus').val();
          var sp = jQuery('#searchByPriority').val();
          var sg = jQuery('#searchGeneric').val();
          var requestid = jQuery('#searchByRequestID').val();
          var dc = jQuery('#searchByDigitizationCenter').val();
          var rd = jQuery('#searchByRecallDecline').val();
          var es = jQuery('#searchByECMSSEMS').val();
          
          console.log('Names:');
          console.log(aaName);
          //console.log('Val:');
          //console.log(aaVal);
          
          // Append to data
          data.searchGeneric = sg;
          data.searchByRequestID = requestid;
          data.searchByStatus = ss;
          data.searchByPriority = sp;
          data.searchByDigitizationCenter = dc;
          data.searchByRecallDecline = rd;
          data.searchByECMSSEMS = es;
          data.currentUser = rs_user;
          data.searchByUser = sbu;
		  data.searchByUserAAVal = aaVal;
		  data.searchByUserAAName = aaName;
       }
    },
    'lengthMenu': [[10, 25, 50, 100], [10, 25, 50, 100]],
    'fixedColumns': true,
<?php		
if (($agent_permissions['label'] == 'Administrator') || ($agent_permissions['label'] == 'Agent') || ($agent_permissions['label'] == 'Manager'))
{
?>
    	    'columnDefs': [	
         {	
            'width' : 0,
            'targets': 0,	
            'checkboxes': {	
               'selectRow': true	
            },	
         },
         { 'width': 100, 'targets': 4 },
      ],
      'select': {	
         'style': 'multi'	
      },
        'drawCallback': function( settings ) {
        jQuery('[data-toggle="tooltip"]').tooltip();
        	var response = settings.json;
	        console.log(response);
     },
      'order': [[1, 'asc']],
<?php
}
?>
    'columns': [
<?php		
if (($agent_permissions['label'] == 'Administrator') || ($agent_permissions['label'] == 'Agent') || ($agent_permissions['label'] == 'Manager'))
{
?>
       { data: 'request_id', 'title': 'Select All Checkbox' }, 
<?php
}
?>
       { data: 'request_id_flag', 'class' : 'text_highlight' },
       { data: 'ticket_priority' },
       { data: 'ticket_status' },
       { data: 'customer_name' },
       { data: 'location' },
       //{ data: 'ticket_priority' },
       { data: 'date_updated' },
    ]
  });
    
  jQuery( window ).unload(function() {
  dataTable.column(0).checkboxes.deselectAll();
});

  jQuery(document).on('keypress',function(e) {
    if(e.which == 13) {
        //prevents page redirect on enter
        e.preventDefault();
        dataTable.state.save();
        dataTable.draw();
    }
});

	jQuery("#searchByUser").change(function(){
		dataTable.state.save();
		dataTable.draw();
	});

jQuery("#searchByStatus").change(function(){
    dataTable.state.save();
    dataTable.draw();
});

  jQuery("#searchByPriority").change(function(){
    dataTable.state.save();
    dataTable.draw();
});

  jQuery("#searchByDigitizationCenter").change(function(){
    dataTable.state.save();
    dataTable.draw();
});

jQuery("#searchByRecallDecline").change(function(){
    dataTable.state.save();
    dataTable.draw();
});

// ECMS has been updated to be called ARMS instead
jQuery("#searchByECMSSEMS").change(function(){
    dataTable.state.save();
    dataTable.draw();
});

//jQuery('#searchGeneric').on('input keyup paste', function () {
//            dataTable.state.save();
//            dataTable.draw();
//});


function onAddTag(tag) {
    dataTable.state.save();
    dataTable.draw();

    var target = jQuery("#searchByRequestID");
    var tags = (tag).match(/id=(\d+)/);

    if (tags != null) {
        if (!target.tagExist(tags[1])) {
            target.addTag(tags[1]);
            target.removeTag(tag);
        }
    }
    
    
}

function onRemoveTag(tag) {
    dataTable.state.save();
    dataTable.draw();
}

jQuery("#searchByRequestID").tagsInput({
   'defaultText':'',
   'onAddTag': onAddTag,
   'onRemoveTag': onRemoveTag,
   'width':'100%'
});

jQuery("#searchByRequestID_tag").on('paste',function(e){
    var element=this;
    setTimeout(function () {
        var text = jQuery(element).val();
        var target=jQuery("#searchByRequestID");
        var tags = (text).split(/[ ,]+/);
        for (var i = 0, z = tags.length; i<z; i++) {
              var tag = jQuery.trim(tags[i]);
              if (!target.tagExist(tag)) {
                    target.addTag(tag);
              }
              else
              {
                  jQuery("#searchByRequestID_tag").val('');
              }
                
         }
    }, 0);
});


jQuery('#wpsc_individual_refresh_btn').on('click', function(e){
    jQuery('#searchByUser').val('');
    jQuery('#searchByStatus').val('');
    jQuery('#searchByPriority').val('');
    jQuery('#searchGeneric').val('');
    jQuery('#searchByProgramOffice').val('');
    jQuery('#searchByDigitizationCenter').val('');
    jQuery('#searchByRecallDecline').val('');
    jQuery('#searchByECMSSEMS').val('');
    jQuery('#searchByBoxID').importTags('');
    dataTable.column(0).checkboxes.deselectAll();
	dataTable.state.clear();
	dataTable.destroy();
	location.reload();
});

//delete button
jQuery('#btn_delete_tickets').on('click', function(e){
     var form = this;
     var rows_selected = dataTable.column(0).checkboxes.selected();
		   jQuery.post(
   '<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/delete_request.php',{
postvarsrequest_id : rows_selected.join(",")
}, 
   function (response) {
      //if(!alert(response)){
      
       wpsc_modal_open('Delete Request');
		  var data = {
		    action: 'wpsc_delete_request',
		    response_data: response
		  };
		  jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
		    var response = JSON.parse(response_str);
		    jQuery('#wpsc_popup_body').html(response.body);
		    jQuery('#wpsc_popup_footer').html(response.footer);
		    jQuery('#wpsc_cat_name').focus();
		  }); 
		  
          wpsc_get_ticket_list();
          dataTable.column(0).checkboxes.deselectAll();
      //}
   });
});

	// User Search
	jQuery('#frm_get_ticket_assign_agent').hide();
	
	jQuery('#searchByUser').change( function() {
		if(jQuery(this).val() == 'search for user') {
			jQuery('#frm_get_ticket_assign_agent').show();
		} else {
			jQuery('#frm_get_ticket_assign_agent').hide();
		}
	});
	
	// Show search box on page load - from save state
	if( jQuery('#searchByUser').val() == 'search for user' ) {
		jQuery('#frm_get_ticket_assign_agent').show();
	}

	// Autocomplete for user search
	jQuery( ".wpsc_assign_agents" ).autocomplete({
		minLength: 0,
		appendTo: jQuery('.wpsc_assign_agents').parent(),
		source: function( request, response ) {
			var term = request.term;
			console.log('term: ');
			console.log(term);
			request = {
				action: 'wpsc_tickets',
				setting_action : 'filter_autocomplete',
				term : term,
				field : 'assigned_agent',
			}
			jQuery.getJSON( wpsc_admin.ajax_url, request, function( data, status, xhr ) {
				response(data);
			});
		},
		select: function (event, ui) {
			console.log('label: '+ui.item.label+' flag_val: '+ui.item.flag_val); 							
			html_str = get_display_user_html(ui.item.label, ui.item.flag_val);
// 			jQuery('#assigned_agents').append(html_str);	
			
			// when adding new item, event listener functon must be added. 
			jQuery('#assigned_agents').append(html_str).on('click','.remove-user',function(){	
				console.log('This click worked.');
				wpsc_remove_filter(this);
				jQuery('#user_search').val(jQuery(".searched-user").map(function(){return jQuery(this).text();}).get());
				dataTable.state.save();
				dataTable.draw();
			});
		    jQuery('#user_search').val(jQuery(".searched-user").map(function(){return jQuery(this).text();}).get());
			dataTable.state.save();
			dataTable.draw();
			// ADD CODE to go through every status and make sure that there is at least one name per, and if so, show SAVE.
			
			jQuery("#button_agent_submit").show();
		    jQuery(this).val(''); return false;
		}
	}).focus(function() {
			jQuery(this).autocomplete("search", "");
	});
	
	


	jQuery('.searched-user').on('click','.remove-user', function(e){
		console.log('Removed a user 1');
		wpsc_remove_filter(this);
		jQuery('#user_search').val(jQuery(".searched-user").map(function(){return jQuery(this).text();}).get());
		dataTable.state.save();
		dataTable.draw();
	}); 

// Code block for toggling edit buttons on/off when checkboxes are set
	jQuery('#tbl_templates_requests tbody').on('click', 'input', function () {        
	// 	console.log('checked');
		setTimeout(toggle_button_display, 1); //delay otherwise 
	});
	
	jQuery('.dt-checkboxes-select-all').on('click', 'input', function () {        
	 	console.log('checked');
		setTimeout(toggle_button_display, 1); //delay otherwise 
	});
	
	jQuery('#btn_delete_tickets').attr('disabled', 'disabled');
	
	function toggle_button_display() {
	//	var form = this;
		var rows_selected = dataTable.column(0).checkboxes.selected();
		if(rows_selected.count() > 0) {
			jQuery('#btn_delete_tickets').removeAttr('disabled');		
	  	} else {
	    	jQuery('#btn_delete_tickets').attr('disabled', 'disabled');    	
	  	}
	}
  
});


function get_display_user_html(user_name, termmeta_user_val) {
	//console.log("in display_user");
// 	var requestor_list = jQuery("input[name='assigned_agent[]']").map(function(){return jQuery(this).val();}).get();
	var requestor_list = jQuery("input[name='assigned_agent[]']").map(function(){return jQuery(this).val();}).get();
	
	if( requestor_list.indexOf(termmeta_user_val.toString()) >= 0 ) {
		console.log('termmeta_user_val: '+termmeta_user_val+' is already listed');
		html_str = '';
	} else {

		var html_str = '<div class="form-group wpsp_filter_display_element wpsc_assign_agents ">'
// 						+'<div class="flex-container searched-user" style="padding:5px;font-size:1.0em;">'
						+'<div class="flex-container searched-user staff-badge" style="">'
							+user_name
							+'<span  class="remove-user staff-close" ><i class="fa fa-times" aria-hidden="true" title="Remove User"></i><span class="sr-only">Remove User</span></span>' 
						+'<input type="hidden" name="assigned_agent[]" value="'+termmeta_user_val+'" />'
						+'</div>'
					+'</div>';	

	}
			
	return html_str;		

}


function wpsc_remove_filterX(x) {
	setTimeout(wpsc_remove_filter(x), 10);
}


function remove_user() {
	//if zero users remove save
	//if more than 1 user show save
	var requestor_list = jQuery("input[name='assigned_agent[]']").map(function(){return jQuery(this).val();}).get();
	let is_single_item = <?php echo json_encode($is_single_item); ?>;
	//console.log('Remove user');
	console.log(requestor_list);
	console.log('length: '+requestor_list.length);
	console.log('single item? '+is_single_item);
	
	if( is_single_item ) {
		console.log('doing single item stuff');
		if( requestor_list.length > 0 ) {
			jQuery("#button_agent_submit").show();
		} else {
			jQuery("#button_agent_submit").hide();
		}
	}
}
</script>