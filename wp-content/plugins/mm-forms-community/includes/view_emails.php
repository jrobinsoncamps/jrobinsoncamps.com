<?php
	$action = $_REQUEST['action'];

	// check if there are other fields to show
	global $mmf;
	$xyz = $mmf->contact_forms;
	if ($_REQUEST['action'] == "view") {
		$get_id = $id ;
	} else {
		$get_id = $_REQUEST['form_id'];
	}
?>
<script type="text/javascript">
$(document).ready(function(){

 $("#checkboxall").click(function()
  {
   var checked_status = this.checked;
	if (checked_status == true) {
		$("#delete_selection").animate({"height": "show"}, { duration: 0 });
		$("#delete_tab").animate({"height": "hide"}, { duration: 0 });
		$("#delete_selection_button").animate({"height": "show"}, { duration: 0 });
		$("#delete_selection").attr({"class": "current"});
		$("#view_tab").attr({"class": ""});
	} else {
		$("#delete_selection").animate({"height": "hide"}, { duration: 0 });
		$("#delete_tab").animate({"height": "show"}, { duration: 0 });
		$("#delete_selection_button").animate({"height": "hide"}, { duration: 0 });		
		$("#delete_selection").attr({"class": ""});
		$("#view_tab").attr({"class": "current"});
	}

   $("input[class=delete_checkbox]").each(function()
   {
    this.checked = checked_status;
   });

  });

});

$(document).ready(function() {
$(".delete_checkbox").click(function()
 {
	var checked_status = this.checked;
	if (checked_status == true) {
		$("#delete_selection").animate({"height": "show"}, { duration: 0 });
		$("#delete_tab").animate({"height": "hide"}, { duration: 0 });
		$("#delete_selection_button").animate({"height": "show"}, { duration: 0 });
		$("#delete_selection").attr({"class": "current"});
		$("#view_tab").attr({"class": ""});
	} else {
		var disable_tab = true;
		$(".delete_checkbox").each(
			function( intIndex ){
			if (this.checked == true) {
				disable_tab = false ;
			}
			 }
		);
		if (disable_tab == true) {
			$("#delete_selection").animate({"height": "hide"}, { duration: 0 });
			$("#delete_tab").animate({"height": "show"}, { duration: 0 });
			$("#delete_selection_button").animate({"height": "hide"}, { duration: 0 });		
			$("#delete_selection").attr({"class": ""});
			$("#view_tab").attr({"class": "current"});
		}
	}
	});
});

</script>
<div class="wrap" style="margin-top:16px;">

	<ul id="form_tab_container">
		<li id="home_tab"<a href="<?php echo $base_url . '?page=' . $page; ?>">Home</a></li>
		<li id="view_tab" class="current"><a href="<?php echo $base_url . '?page=' . $page . '&action=view&id=' . $get_id ?>">View submissions</a></li>
		<li id="delete_selection"><a href="#">Delete selection</a></li>
		<li id="delete_tab"><a onclick="return confirm('Are you sure you want to delete all records')" style="width:130px;" href="<?php echo $base_url . '?page=' . $page . '&action=deleteAllEmails&id='.$id ?>"><?php _e('Delete All Records') ?></a></li>
	</ul>

<div style="margin-left:0px;margin-top:20px">
<form method="post" name="frm_emails" id="frm_emails" action="<?php echo $base_url.'?page=' . CONTACTFORM . '/mm-forms.php&action=deleteselection&form_id=' . $get_id ; ?>">

	<p id="delete_selection_button">
	<input type="submit" class="link_button" onclick="return confirm('Are you sure you want to delete the selected records ?')" value="Delete selection" />
	</p>
<table class="widefat">
  <thead>
	<tr>
		<th width="20px">
			<input type="checkbox" name="checkboxall" id="checkboxall" />
		</th>
<?php foreach($obj_actions->view_emails_columns as $class => $column_display_name) {
	$class = ' class="'.$class.'" ';
?>
	<th scope="col"<?php echo $class; ?>><?php echo $column_display_name; ?></th>
<?php } ?>
	<?php
		$list_fields = explode(',',$xyz[$get_id]['mail']['mmf_list_fields']);
		if ($xyz[$get_id]['mail']['mmf_list_fields']) {
			for ( $i = 0 ; $i < count($list_fields) ; $i++ ) {
				echo "<th scope='col' class='" . $list_fields[$i] . "'>" . $list_fields[$i] . "</th>";
			}
		}
	?>
	<th scope="col" class="edit" width="20px">Edit</th>
	<th scope="col" class="view" width="20px">View</th>
	<!--<th scope="col" class="Delete" >Delete</th> -->
  </tr>
  </thead>
  <tbody class="mail_data">
  <?php //page_rows($posts);
  	if($obj_actions->emails){
		foreach($obj_actions->emails as $mail)
		{

			$class = ($mail->read_flag == 0) ? 'row_unread_email' : '';
			?>
				<tr class="<?php echo $class ?>">
					<th scope="col"><input type="checkbox" class="delete_checkbox" id="checkall_<?php echo $mail->id ?>" name="checkall_<?php echo $mail->id ?>" /></th>
					<th scope="col"><a href="<?php echo $base_url . '?page=' . $page . '&action=viewDetail&id=' . $mail->id . '&form_id=' . $get_id ?>"><?php echo $mail->submit_date; ?></a></th>
					
					<?php if ($xyz[$get_id]['mail']['mmf_list_fields']) {
						for ( $i = 0 ; $i < count($list_fields) ; $i++ ) {
							echo '<th style="font-weight:normal;" scope="col">' . get_field_value($mail->id,$list_fields[$i]) . '</th>';
						}
					} else { ?>
						<th style="font-weight:normal;" scope="col"><?php echo $mail->client_ip; ?></th>
						<th style="font-weight:normal;" scope="col"><?php echo $mail->request_url; ?></th>
					<?php } ?>
					<th style="font-weight:normal;" scope="col"><a href="<?php echo $base_url . '?page=' . $page . '&action=viewDetail&t=edit&id=' . $mail->id . '&form_id=' . $get_id ?>"><img src="../wp-content/plugins/mm-forms-community/images/pencil.png"/></a></th>
					<th style="font-weight:normal;" scope="col"><a href="<?php echo $base_url . '?page=' . $page . '&action=viewDetail&id=' . $mail->id . '&form_id=' . $get_id ?>"><img src="<?php echo $image_path?>view.png" /></a></th>
<!--					<th style="font-weight:normal;text-align:center;" scope="col">
						<a href="<?php echo $base_url . '?page=' . $page . '&action=deletemail&form_id='.$mail->fk_form_id.'&id=' . $mail->id ?>">
							<img onclick="return confirm('Are you sure you want to delete this record')" src="<?php echo $image_path.'delete.png'?>" />
						</a>
					</th>	-->				
				</tr>
			<?php
		}
		?>
		
		<?php
	}
	else
	{
		?>
			<tr><th colspan="3">No records found</th></tr>
		<?php
	}
   ?>
  </tbody>
</table>

<div>
<table>
<tbody>
<tr><th>
		</th>
		<th><?php echo $obj_actions->get_pagination();?></th></tr>
</tbody>
</table>
<script type="text/javascript">
$("#delete_selection").animate({"height": "hide"}, { duration: 0 });
$("#delete_selection_button").animate({"height": "hide"}, { duration: 0 });
function set(id,val)
{
	val = (!val) ? 20 : val;
	ctrl = document.getElementById(id);
	len = ctrl.length;
	
	for(i=0;i<len;i++)
	{
		if(ctrl.options[i].value == val)
		{
			ctrl.options[i].selected = true;
			break;
		}
	}
}
set('records_per_page','<?php echo $records_per_page?>');


<?php 


if ($action == "deletemail" || $action == "deleteselection" || $action == "deleteAllEmails") { 

	echo '$("#mmf_msg	").text("Submissions deleted.");';
	echo 'setTimeout(function() { $("#mmf_message").fadeOut(); }, 5000);';


} else {
	
	echo '	$("#mmf_message").animate({"height": "hide"}, { duration: 0 });';

}
?>

</script>
</div>
</form>
</div>
</div>
<?php
	
	function get_field_value($id,$field) {
			global $wpdb;
			$sql = "SELECT value FROM " . $wpdb->prefix."contactform_submit_data" . " where fk_form_joiner_id = '".$id."' AND form_key = '" . $field . "' ";
			$res = $wpdb->get_results($sql);

			foreach ($res as $result) 
			{
				$value = $result->value;
			}
			return $value ;
	}
?>