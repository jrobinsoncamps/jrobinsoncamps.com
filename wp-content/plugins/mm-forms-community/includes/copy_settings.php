<?php

/*
	copy_settings
	
	Created by Tom Belmans on 2009-09-24.
	Copyright (c) 2009 Addwittz - Motionmill. All rights reserved.
*/


?>

<h3>Copy form</h3>
<div class="fieldset">
<div for="something" class="legend">
<?php
if($current == 'new')
		_e('Create new form with the setting of a Form in this list');
else 
		_e('Update Form Settings with the setting of a Form in this list');
?>
 </div>
<?php $frm_arr = $this->get_contactform_option('mmf');?>
<form id="formsetting" name="formsetting" method="post">
	
	<select name="mmf-copy_from" id="mmf-copy_from" >
	<?php 
		foreach($frm_arr as $entry) :
			foreach($entry as  $en=>$val) :	
			if($en == $current) continue;	
			?>
				<option value="<?php echo $en?>"><?php echo $val['title']; ?></option>
			<?php
			endforeach;
		endforeach;	
	  ?>
	</select>	
	<input type="hidden" id="mmf-id" name="mmf-id" value="<?php echo $current; ?>" />
	<input type="button" name="copyfrom" id="copyfrom" value="Go" class="button-primary" onclick="overwrite_option()"  />
</form>	

<div id="dialog" style="width:400px; display:none; border:1px solid #CCCCCC;  margin-left:250px;  height:100px; z-index:444">
	<div style="height:40px; background-color:#C6D9E9; border:1px solid #333333; "> Do you want to overwrite the current settings with existing Forms?</div>
	
	<br />
	<input type="checkbox" name="something" id="something" value="0" onclick="submitForm()" />
	Click here	
	<br />
</div>
