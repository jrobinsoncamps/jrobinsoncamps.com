<?php 
/*
*	Plugin Name: mm-forms
*	 
*	File Description: This file is opened in popup when the user clicks on 
*				 mm_forms button in editor.
*	Author: Abdul Shahid
*	Date: 30 July 2008
*	Version: 1.0
*	Author URI: http://www.motionmill.com
*   Copyright © 2008-2009, Motionmill, All rights reserved.
*/
include ('../../../../../../wp-config.php'); 
if (!defined ('ABSPATH')) die ('No direct access allowed'); 

require_once ('../../../mm-forms.php');

global $wpdb;
$contact_forms = new mm_forms();
$mm_forms = $contact_forms->contact_forms();


/*
echo "condtions".$condtions;
echo "<pre>";
print_r($mm_forms);
echo "</pre>";
*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<script type="text/javascript" src="../../../../../../wp-includes/js/tinymce/tiny_mce_popup.js"></script>
	<script type="text/javascript" src="js/dialog.js"></script>
	<title>Insert MM FormData tag</title>	
<script type="text/javascript"> 

var arr = new Array();
<?php
foreach($mm_forms as $key=>$mm_form){
	$c = str_replace("\r\n", "<br>", $mm_form[mmf_form_conditions]);
?>		
	arr[<?php echo $key?>] = "<?php echo $c ?> ";
<?php
}
?>

function setConditions(id) {
	 
	 frmid = id.split(" ")[1];	 
	 var allConditions = arr[frmid].split("<br>");
	 
	 var conditionBox = document.getElementById("mmf_condtions");

	 conditionBox.options.length=0;
	 conditionBox.options[0] =new Option("", "");
	 j = 1;
	 for(i = 0; i < allConditions.length; i++) { 
	 	if(allConditions[i] != "")
		 	conditionBox.options[j] =new Option(allConditions[i],allConditions[i]);
		 j++;
	 }
}

</script>	

</head>
<body>
<form onsubmit="MM_FromsDialog.insert();return false;" action="#">
<input type="hidden" name="formdataGUI"  value="yes"/>
	<!--
	<p>Here is a MM Form dialog.</p>
	<p>Selected text: <input id="someval" name="someval" type="text" class="text" /></p>
	<p>Custom arg: <input id="somearg" name="somearg" type="text" class="text" /></p>
	--->
	<div class="tabs">
		<ul>
			<li class="current" id="general_tab">
				<span>
					<a onmousedown="return false;" href="#">
						FormData tags
					</a>
				</span>
			</li>
		</ul>
	</div>
	
	<div class="panel_wrapper">
		<div class="current" style="height:45px" id="general_panel">
			<table width="279" border="0" cellpadding="4" cellspacing="0">
			  <tbody>
			  
				<tr>
				  <td width="190"><label for="mm_form" id="mm_form_label">Select MM FromsData tag</label></td>
					<td width="73">
			
					<select name="mm_form" id="mm_form" onchange="setConditions(this.value)">	
						<?php
						foreach($mm_forms as $key=>$mm_form){

						?>
							<option value='[formdata <?php echo $key .' "'. $mm_form[title].'"' ?>]'><?php echo $mm_form[title]?></option>
						<?php
						}
						?>
					</select>
				  </td>
				</tr>
				
				<tr>
					<td><label for="mm_form" id="mm_form_label">Put Condition on Data</label></td>
					<td>
			
					<select name="mmf_condtions" id="mmf_condtions" style="width:110px">	
						
					</select>
						
					</td>
				</tr>
			 </tbody>
		  </table>
		</div>
	</div>
	<div class="mceActionPanel">
		<div style="float: left">
			<input type="button" id="cancel" name="cancel" value="{#cancel}" onclick="tinyMCEPopup.close();" />
		</div>
		<div style="float: right">
			<input type="button" id="insert" name="insert" value="{#insert}" onclick="MM_FromsDialog.insert();" />
		</div>
	</div>
</form>

</body>
</html>