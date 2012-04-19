<?php
   	require_once('../../../../wp-load.php');
	global $wpdb;
	$mydata;
	$sql = "SELECT form_id,form_name FROM " . $wpdb->prefix. "contactform";
		$results =  $wpdb->get_results($sql);
		if($results=="")
		{
			return 'Error in css';
			break;
		}
		foreach ($results as $result)
		{
			 $mydata.='^[form '.$result->form_id.' "'.$result->form_name.'"]';
		}
	echo $mydata;
?>
