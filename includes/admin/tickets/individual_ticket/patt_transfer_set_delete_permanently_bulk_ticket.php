<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpscfunction,$wpdb;

$ticket_id_data  = isset($_POST['ticket_id']) ? (sanitize_text_field($_POST['ticket_id'])) : '' ;
$ticket_ids = explode(',', $ticket_id_data);

foreach ($ticket_ids as $ticket_id ) {
	
//PATT BEGIN

$patt_transfer_folderdocinfo_file_array = array();


//BEGIN Deleting DATA FROM ARCHIVE
$get_related_patt_transfer_folderdocinfo = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "epa_patt_arms_logs_archive WHERE folderdocinfofile_id = '".$ticket_id."'");


foreach ($get_related_patt_transfer_folderdocinfo as $patt_transfer_folderdocinfofile) {

   //Remove from epa_patt_arms_logs_archive
	$wpdb->delete( $wpdb->prefix . 'epa_patt_arms_logs_archive', array( 'folderdocinfofile_id' => $patt_transfer_folderdocinfofile->folderdocinfofile_id) );


//PATT END
}
	
//PATT END
	
}