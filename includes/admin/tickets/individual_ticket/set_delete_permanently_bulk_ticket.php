<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpscfunction,$wpdb;

$ticket_id_data  = isset($_POST['ticket_id']) ? (sanitize_text_field($_POST['ticket_id'])) : '' ;
$ticket_ids = explode(',', $ticket_id_data);

foreach ($ticket_ids as $ticket_id ) {
	
//PATT BEGIN
$get_associated_boxes = $wpdb->get_results("
SELECT id, storage_location_id FROM " . $wpdb->prefix . "wpsc_epa_boxinfo 
WHERE ticket_id = '" . $ticket_id . "'
");

foreach ($get_associated_boxes as $info) {
		$associated_box_ids = $info->id;
		$associated_storage_ids = $info->storage_location_id;

//DELETING FOLDERFILE REPORTING
$table_timestamp_folderfile = $wpdb->prefix . 'wpsc_epa_timestamps_folderfile';
$get_folderfile_timestamp = $wpdb->get_results("select a.id 
from " . $table_timestamp_folderfile . " a
INNER JOIN " . $wpdb->prefix . "wpsc_epa_folderdocinfo_files_archive b ON b.id = a.folderdocinfofile_id
where b.box_id = '".$associated_box_ids."'");

foreach($get_folderfile_timestamp as $folderfile_timestamp) {
    $folderfile_timestamp_id = $folderfile_timestamp->id;
    // Delete previous value
    if( !empty($folderfile_timestamp_id) ) {
      $wpdb->delete( $table_timestamp_folderfile, array( 'id' => $folderfile_timestamp_id ) );
    }
}

		$box_details = $wpdb->get_row(
"SELECT 
digitization_center,
aisle,
bay,
shelf,
position
FROM " . $wpdb->prefix . "wpsc_epa_storage_location
WHERE id = '" . $associated_storage_ids . "'"
			);
			
			$box_storage_digitization_center = $box_details->digitization_center;
			$box_storage_aisle = $box_details->aisle;
			$box_storage_bay = $box_details->bay;
			$box_storage_shelf = $box_details->shelf;
			$box_storage_shelf_id = $box_storage_aisle . '_' . $box_storage_bay . '_' . $box_storage_shelf;
    
        Patt_Custom_Func::update_remaining_occupied($box_storage_digitization_center,array($box_storage_shelf_id));

		$wpdb->delete($wpdb->prefix.'wpsc_epa_storage_location', array( 'id' => $associated_storage_ids));
		$wpdb->delete($wpdb->prefix.'wpsc_epa_shipping_tracking', array( 'ticket_id' => $ticket_id));

// DELETE Associated Recalls
$get_recall_request_id = $wpdb->get_row(
"SELECT 
id
FROM " . $wpdb->prefix . "wpsc_epa_recallrequest
WHERE box_id = '" . $associated_box_ids . "'"
			);
$recall_request_id = $get_recall_request_id->id;

//delete from timestamp recall reporting
$wpdb->delete($wpdb->prefix.'wpsc_epa_timestamps_recall', array( 'recall_id' => $recall_request_id));
$wpdb->delete($wpdb->prefix.'wpsc_epa_recallrequest_users', array( 'recallrequest_id' => $recall_request_id));	

$get_shipping_recall_id = $wpdb->get_row(
"SELECT 
id
FROM " . $wpdb->prefix . "wpsc_epa_shipping_tracking
WHERE recallrequest_id = '" . $recall_request_id . "'"
			);
$shipping_recall_id = $get_shipping_recall_id->id;

$wpdb->update($wpdb->prefix .'wpsc_epa_shipping_tracking', array('recallrequest_id' => '-99999'), array('id' => $shipping_recall_id));
$wpdb->delete($wpdb->prefix.'wpsc_epa_recallrequest', array( 'box_id' => $associated_box_ids));
$wpdb->delete($wpdb->prefix .'wpsc_epa_shipping_tracking', array( 'recallrequest_id' => $recall_request_id));

// DELETE Associated Returns
$get_return_request_id = $wpdb->get_row(
"SELECT 
return_id
FROM " . $wpdb->prefix . "wpsc_epa_return_items
WHERE box_id = '" . $associated_box_ids . "'"
			);
$return_request_id = $get_return_request_id->return_id;

//delete from timestamp decline reporting
$wpdb->delete($wpdb->prefix.'wpsc_epa_timestamps_decline', array( 'decline_id' => $return_request_id));
$wpdb->delete($wpdb->prefix.'wpsc_epa_return_users', array( 'return_id' => $return_request_id));

$get_shipping_return_id = $wpdb->get_row(
"SELECT 
id
FROM " . $wpdb->prefix . "wpsc_epa_shipping_tracking
WHERE return_id = '" . $return_request_id . "'"
			);
$shipping_return_id = $get_shipping_return_id->id;

$wpdb->update($wpdb->prefix .'wpsc_epa_shipping_tracking', array('return_id' => '-99999'), array('id' => $shipping_return_id));

$wpdb->delete($wpdb->prefix.'wpsc_epa_return_items', array( 'return_id' => $return_request_id));
$wpdb->delete($wpdb->prefix.'wpsc_epa_return', array( 'id' => $return_request_id));	
$wpdb->delete($wpdb->prefix.'wpsc_epa_shipping_tracking', array( 'return_id' => $return_request_id));

// DELETE Files and Box
		$wpdb->delete($wpdb->prefix.'wpsc_epa_folderdocinfo_files_archive', array( 'box_id' => $associated_box_ids));
		$wpdb->delete($wpdb->prefix.'wpsc_epa_boxinfo', array( 'id' => $associated_box_ids));

$wpdb->delete($wpdb->prefix.'wpsc_ticket', array( 'id' => $ticket_id));
$wpdb->delete($wpdb->prefix.'wpsc_ticketmeta', array('ticket_id' => $ticket_id));

//DELETING REQUEST REPORTING
$table_timestamp_request = $wpdb->prefix . 'wpsc_epa_timestamps_request';
$get_request_timestamp = $wpdb->get_results("select id from " . $table_timestamp_request . " where request_id = '".$ticket_id."'");

foreach($get_request_timestamp as $request_timestamp) {
    $request_timestamp_id = $request_timestamp->id;
    // Delete previous value
    if( !empty($request_timestamp_id) ) {
      $wpdb->delete( $table_timestamp_request, array( 'id' => $request_timestamp_id ) );
    }
}

//DELETING BOX REPORTING
$table_timestamp_box = $wpdb->prefix . 'wpsc_epa_timestamps_box';
$get_box_timestamp = $wpdb->get_results("select id from " . $table_timestamp_box . " where box_id = '".$associated_box_ids."'");

foreach($get_box_timestamp as $box_timestamp) {
    $box_timestamp_id = $box_timestamp->id;
    // Delete previous value
    if( !empty($box_timestamp_id) ) {
      $wpdb->delete( $table_timestamp_box, array( 'id' => $box_timestamp_id ) );
    }
}

//PATT END
}
	
//PATT END
	
//	$wpdb->delete($wpdb->prefix.'wpsc_ticket', array( 'id' => $ticket_id));
//	$wpdb->delete($wpdb->prefix.'wpsc_ticketmeta', array('ticket_id' => $ticket_id));
	
	$args = array(
		'post_type'      => 'wpsc_ticket_thread',
		'post_status'    => array('publish','trash'),
		'posts_per_page' => -1,
		'meta_query'     => array(
			 array(
				'key'     => 'ticket_id',
				'value'   => $ticket_id,
				'compare' => '='
			),
		),
	);
	$ticket_threads = get_posts($args);
	if($ticket_threads) {
		foreach ($ticket_threads as $ticket_thread ) {
			 wp_delete_post($ticket_thread->ID,true);
		}
	}
}