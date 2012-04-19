<?php

if ($_REQUEST['t'] == "edit") {
	$subt = "Edit detail";
} else {
	$subt = "View detail";
}

?>

<div class="wrap" style="margin-top:0px;margin-left:0px">
	<div class="wrap relative" style="margin-top:16px;">
	<ul id="form_tab_container">
		<li id="home_tab"><a href="<?php echo $base_url . '?page=' . $page . '&action=view&id=' . $_REQUEST['form_id'] ?>">Back</a></li>
		<li id="new_tab" class="current"><a href="#"><?php echo $subt ?></a></li>
	</ul>
	</div>
<div style="margin-left:5px;margin-top:20px">
<table class="widefat" style="width:600px;">
  <thead>
  <tr>
<?php foreach($obj_actions->detail_info_columns as $class => $column_display_name) {
	$class = ' class="'.$class.'" ';
?>
	<th scope="col"<?php echo $class; ?>><?php echo $column_display_name; ?></th>
<?php } ?>
  </tr>
  </thead>
  <form name="frmEditDetail" method="post" action="<?php echo get_option('siteurl'); ?>/wp-content/plugins/<?php echo CONTACTFORM ; ?>/includes/edit_details.php">
  <input type="hidden" name="ID" value="<?php echo $_REQUEST['id']?>" />
  <input type="hidden" name="form_id" value="<?php echo $_REQUEST['form_id']?>" />
  <style type="text/css">
textarea {width:100%;}
.textarea-resizer {height:4px; background:#EEE; cursor:s-resize;}
</style>

<script type="text/javascript" src="<?php echo get_option('siteurl'); ?>/wp-content/plugins/<?php echo CONTACTFORM ?>/js/TextAreaResize.js"></script>
<link rel="scriptsheet" type="text/javascript" href="<?php echo get_option('siteurl'); ?>/wp-content/plugins/<?php echo CONTACTFORM ?>/js/TextAreaResize.js#TextAreaResizer" title="default">
  <tbody class="mail_data" >
  <?php //page_rows($posts);

  	if($obj_actions->form_fields){
		foreach($obj_actions->form_fields as $field)
		{
			?>
				<tr>
					<th scope="col" valign="middle"><?php echo $field->form_key ?></th>
					<th>
				
					<?php 
								if($_REQUEST['t']=='edit'){									
									echo "<textarea  name='".$field->form_key."'>".$field->value."</textarea>";
									
								}else{
								
									if(eregi("uploaded-file-", $field->form_key)) {
										list($t, $name) = explode("-", $field->value);

										$filePath = ABSPATH . PLUGINDIR . "/".CONTACTFORM."/upload/form-upload/";
										$extension = strtolower(substr($field->value,-3)) ;
										if ($extension == "jpg" || $extension == "jpeg" || $extension == "png" || $extension == "gif") {
											$thumbnail = createThumb ($filePath.$field->value,"150","150",get_option('siteurl'));
										}
							
										echo "<a href='".get_option('siteurl')."/wp-content/plugins/".CONTACTFORM."/upload/form-upload/".$field->value."'><img src='". $thumbnail. "' /><br>" . $name ."</a>";
									} else	{
										
										echo $field->value;
									}	
								}
					?>
					</th>
				</tr>
			<?
		}
	}
	else
	{
		?>
			<tr><th>No Record Found</th></tr>
		<?
	}
   ?>
  </tbody>
  </form>
</table>
<div class="mmf" style="margin-left:0px" >
	<div class="link_button"><?php if($_REQUEST['t']=='edit'){?><a href="javascript:document.frmEditDetail.submit();"><?php _e('Save') ?></a><?php } ?></div>
</div>

</div>
</div>
<script type="text/javascript">
	$("#mmf_message").animate({"height": "hide"}, { duration: 0 });
</script>