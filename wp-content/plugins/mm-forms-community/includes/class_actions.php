<?php
class actions
{
	var $view_form_columns;
	var $form_info;
	var $contact_forms;
	var $view_emails_columns;
	var $emails;
	var $form_fields;
	var $detail_info_columns;
	var $view_all;
	var $add_edit;
	var $view_emails;
	var $view_form_details;
	var $id;
	
	var $url;
	var $records_per_page;
	var $current_page;
	var $total_records;
	
	var $cvsFileData;
	var $form_id;

	function actions($action, $forms , $id, $form_id,$current_page=0,$records_per_page)
	{

		$this->form_id = $id;
		$this->id = $id;
		
		$this->records_per_page = $records_per_page;
		$this->current_page = $current_page;
		
		switch($action)
		{
			case "view":
				$this->view_emails($id);
				break;
			case "viewDetail":
				$this->view_email_detail($id);
				break;
			case "deletemail":
				$this->delete_email($id);
				$this->view_emails($form_id);
				$this->view_emails = true;
				break;
			case "deleteAllEmails":
				$this->delete_all_emails($id);
				$this->view_emails($form_id);
				$this->view_emails = true;
				break;				
			case "deleteselection":
				$this->delete_selection($id);
				$this->view_emails($form_id);
				$this->view_emails = true;
				break;				
			case "deleteform":
				$this->delete_contactform($id);
				$this->view_all_forms($forms);
				break;
			case "export":
				$this->export_to_csv($id);
				break;				
			default:
				$this->view_all_forms($forms);
				break;				
		}
	}
	//deleting form from the table
	function delete_contactform($id)
	{
		global $wpdb;

		$wpdb->query("delete from ".$wpdb->prefix . 'contactform'." where form_id = '$id'");
		
		$sql = "select distinct(id) from ".$wpdb->prefix . 'contactform_submit'." where fk_form_id = '$id'";
		$res = $wpdb->get_results($sql);
	
		foreach ($res as $r) {
			$joiner_id = $r->id ;
			$wpdb->query("delete from ".$wpdb->prefix . "contactform_submit_data where fk_form_joiner_id = '" . $joiner_id . "'");
		}
		
		$wpdb->query("delete from ".$wpdb->prefix . 'contactform_submit'." where fk_form_id = '$id'");

	}
	function view_all_forms($forms)
	{
       // echo "<br> view_all_forms";
		$this->contact_forms = $forms;
		$this->view_form_columns = array(
		"form_name" =>   "Form Name",
		"form_id"   =>   "Form Tag",
		"view"      =>   "View",
		"Edit"      =>   "Edit",
		"Delete"    =>   "Delete");	
		if($forms)
		{
			foreach($forms as $form_id=>$form)
			{
				$this->form_info[$form_id] = array(
				'num_of_emails' => $this->get_num_of_emails($form_id),
				'num_of_unread_emails' => $this->get_unread_emails($form_id),
				'formFields' => $this->userformFields($form_id));				
			}
		}
		$this->view_all = true;	
	}
	function get_num_of_emails($form_id)
	{
		global $wpdb;
		$sql = "SELECT count(*) as num_of_emails FROM " . $wpdb->prefix."contactform_submit" . " where fk_form_id = '".$form_id."' limit 1 ";
		return $num_of_emails = $wpdb->get_var($sql);
		
	}
	function get_unread_emails($form_id)
	{
		global $wpdb;
		$sql = "SELECT count(*) as unread_emails FROM " . $wpdb->prefix."contactform_submit" . " where fk_form_id = '".$form_id."' and read_flag = 0 limit 1 ";
		return $num_of_unread_emails = $wpdb->get_var($sql);		
	}
	
	function view_emails($id)
	{
            global $wpdb,$mmf;
			$current_page = $this->current_page;
			if($current_page==1){
					$offset = 0;
			}
			else{
					$current_page = $current_page-1;
					$offset = ($current_page) * 50;
			}
   
			$limit = " LIMIT $offset,50";
		   $xyz = $mmf->contact_forms;
		   $list_fields = explode(',',$xyz[$id]['mail']['mmf_list_fields']);

		
			$sql = "SELECT fk_form_id, submit_date, id, client_ip, request_url, read_flag FROM " . $wpdb->prefix."contactform_submit" . " where fk_form_id = '".$id."' order by submit_date DESC $limit";
		   
			$this->emails = $wpdb->get_results($sql);       
		
			if ($xyz[$id]['mail']['mmf_list_fields']) {
				$this->view_emails_columns = array(
				"submit_date" => "Submit Date");
			} else {
				$this->view_emails_columns = array(
				"submit_date" => "Submit Date",
				"client_ip"   => "Client IP",
				"request_url" => "Request URL");
			}
			$this->view_emails = true;
			
			$this->total_records = count($wpdb->get_results("SELECT fk_form_id, submit_date, id, client_ip, request_url, read_flag FROM " . $wpdb->prefix."contactform_submit" . " where fk_form_id = '".$id."' order by submit_date DESC"));
		   
			return $this->emails;

	}
	
	function view_email_detail($id)
	{
		global $wpdb;
		$sql = "SELECT form_key, value FROM " . $wpdb->prefix."contactform_submit_data" . " where fk_form_joiner_id = '".$id."' ";
		$this->form_fields = $wpdb->get_results($sql);
		
		$this->detail_info_columns = array(
		"form_field"  =>  "Form Fields",
		"form_value"  =>  "Values");			
		
		$this->update_email_status($id);
		$this->view_form_details = true;
	}
	
	function update_email_status($id)
	{
		global $wpdb;
		$where = array("id" => $id);
		$values_contactform_submit['read_flag'] = 1;
		
		$where = array("id" => $id);
		$wpdb->update($wpdb->prefix."contactform_submit",$values_contactform_submit,$where);
	}
	
	function delete_email($id)
	{
		global $wpdb;
		$wpdb->query("delete from ".$wpdb->prefix . 'contactform_submit'." where id = '$id'");
		$wpdb->query("delete from ".$wpdb->prefix . 'contactform_submit_data'." where fk_form_joiner_id = '$id'");		
	}
	
	function delete_all_emails($id)
	{
		global $wpdb;
		$all_emails = $this->view_emails($id);
		foreach($all_emails as $key=>$mail)
		{
			$this->delete_email($mail->id);
		}
	}
	
	function delete_selection($id) {
		global $wpdb;
		foreach ($_REQUEST as $key => $value) {
			if (substr_count($key,'checkall_') == 1) {
				$del_id = substr($key, 9);
				$this->delete_email($del_id);
			}
		}
	}
	
	function get_pagination($current=1, $records_per_page=4)
	{
		$total_records = $this->total_records;
		$rec_per_page = 50;
		
		if (!$this->id) {
			$the_Id = $_GET['id'];
		} else {
			$the_Id = $this->id;
		}
		$no_of_pages = ceil((int)$total_records/(int)$rec_per_page);
		
		$current = $this->current_page;
		
		$str = '<th>Showing '.$current.' of '.$no_of_pages.'</th>';
		
		$base_url  = get_option('siteurl'). '?page='.CONTACTFORM.'/mm-forms.php&action=view&id=' . $this->id;
		
		$url = $this->url.'?page='.CONTACTFORM.'/mm-forms.php&action=view&id=' . $the_Id.'&rec_per_pg='.$rec_per_page;
		
		$previous_page = $current - 1;
		
		if($previous_page < 1){
			$str .= '<th>Previous</th>';
		}
		else{
			
			$url_previous = $url.'&pg='.$previous_page;
			$str .= '<th><a href="'.$url_previous.'">Prev</a></th>';
		}		
		
		if($current >= $no_of_pages){
			$str .= '<th>Next</th>';
		}
		else{
			$next_page = $current + 1;
			$url_next = $url.'&pg='.$next_page;
			$str .= '<th><a href="'.$url_next.'">Next</a></th>';
		}
		
		return $str;
	}
	
	function getFormName($formId) {
		global $wpdb;
		$sql = "SELECT form_name FROM " . $wpdb->prefix."contactform WHERE form_id = '$formId'";
		
		$r = $wpdb->get_row($sql);
		return sanitize_file_name($r->form_name);
	}

	function formFields($form_id){
		global $wpdb;
		$sql = "SELECT form_fields, csv_separator FROM " . $wpdb->prefix."contactform" . " WHERE form_id = '$form_id'";
        $frm_data = $wpdb->get_row($sql);
        $export_field = $frm_data->form_fields;
        $csv_separator = $frm_data->csv_separator;
    
    	if($export_field == "") {
			$sql = "SELECT distinct(sd.form_key) FROM " . $wpdb->prefix."contactform_submit s ";
			$sql .= "LEFT JOIN " . $wpdb->prefix."contactform_submit_data AS sd ON s.id = sd.fk_form_joiner_id ";
			$sql .= "WHERE	fk_form_id = " . $form_id ;

           $arr = $wpdb->get_results($sql);
           $exportFields = array();
			foreach($arr  as $entry){
                //$export_field .= $csv_separator.$entry->form_key;
					
				if (!in_array($entry->form_key, $exportFields)) {
					array_push($exportFields,$entry->form_key);					
				}
            }
			$export_field = implode($csv_separator, $exportFields);
        }
		return $export_field;
	}
	
	function userformFields($form_id) {
			global $wpdb;
			$sql = "SELECT form_fields FROM " . $wpdb->prefix."contactform" . " WHERE form_id = '$form_id'";
	        $frm_data = $wpdb->get_row($sql);
	        $export_fields = $frm_data->form_fields;
			return $export_fields;
	}
	
    function export_to_csv($form_id) {
        global $wpdb;

		$sql = "SELECT form_fields, csv_separator, export_form_ids FROM " . $wpdb->prefix."contactform" . " WHERE form_id = '$form_id'";
    	$frm_data = $wpdb->get_row($sql);
    	$csv_separator = $frm_data->csv_separator;
        $export_form_ids = $frm_data->export_form_ids;

        $file_name = $this->getFormName($form_id) . ".csv";
        $fh = fopen(ABSPATH . PLUGINDIR . '/' . CONTACTFORM . '/exports/' . $file_name,'w');

        if ($export_form_ids)
        {
            $export_fields = "id" . $csv_separator . "submit_date" . $csv_separator . "referer" . $csv_separator . "client_ip" . $csv_separator . $this->formFields($form_id);
        }
        else
        {
            $export_fields = $this->formFields($form_id);
        }

        $file_data = $export_fields . "\n" ;

        // get all contactform_submit records
        $sql = "SELECT * FROM " . $wpdb->prefix."contactform_submit" . " WHERE fk_form_id = '" . $form_id . "'";
        $res = mysql_query($sql);

        while($data = mysql_fetch_assoc($res))
        {
                $id = $data[id];
				$referer = $data['request_url'];
				$submit_date = $data['submit_date'];
				$client_ip = $data['client_ip'];

                $sql2 = "SELECT * FROM " . $wpdb->prefix."contactform_submit_data" . " WHERE fk_form_joiner_id = '" . $id . "' " ;
                $res2 = mysql_query($sql2);
		$submit_data = "";
                while($data2 = mysql_fetch_assoc($res2))
                {
                        $form_key = $data2[form_key];
                        $value = $data2[value];
                        $submit_data[$form_key] =  $value ;

                }
		$line = "";
                if ( $export_form_ids )
                        $line = "\"" . $id . "\"". $csv_separator . "\"" . $submit_date . "\"". $csv_separator . "\"" . $referer . "\"". $csv_separator . "\"" . $client_ip . "\"". $csv_separator;

                $fields = explode($csv_separator, $this->formFields($form_id)) ;

                for ( $i = 0 ; $i < count($fields) ; $i++) {
                        $line .= "\"" . str_replace("\"","\"\"",stripslashes($submit_data[$fields[$i]])) . "\"" . $csv_separator ;
                }
                $file_data .= substr($line,0,-1) . "\n";

        }
        fwrite($fh,$file_data);
        fclose($fh);
        return get_option('home') . "/" . PLUGINDIR . '/' . CONTACTFORM . '/exports/' . $file_name ;
    }
	
	
	
}

?>
