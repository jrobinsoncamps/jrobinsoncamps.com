<script language="javascript" type="text/javascript">
function CompareDates()
{	var strMonthArray = new Array(12);
	strMonthArray[0] = "Jan"; strMonthArray[1] = "Feb"; strMonthArray[2] = "Mar"; strMonthArray[3] = "Apr";
	strMonthArray[4] = "May"; strMonthArray[5] = "Jun"; strMonthArray[6] = "Jul"; strMonthArray[7] = "Aug";
	strMonthArray[8] = "Sep"; strMonthArray[9] = "Oct"; strMonthArray[10] = "Nov"; strMonthArray[11] = "Dec";	

 var a1=  document.getElementById('datefrompicker').value;
 var a2=  document.getElementById('datetopicker').value;
//  alert(a1.value);  
  myDateParts = a1.split("/");  
  var mydate=myDateParts[2];
  for(i=0;i<11;i++)
  {	  if(i==myDateParts[1])
	  {	MonthStr=strMonthArray[i-1];  }
  }
  var FullDate= MonthStr + mydate +", "+ myDateParts[0];
  var fromdate = new Date(FullDate);  
  myDateParts = a2.split("/");  
  var mydate=myDateParts[2];
  for(i=0;i<11;i++)  {
	  if(i==myDateParts[1])	  {
		MonthStr=strMonthArray[i-1];	  }
  }
  var FullDate= MonthStr + mydate +", "+ myDateParts[0];
  var todate= new Date(FullDate);
   if(fromdate >= todate)
   {  alert("To date cannot be greater than from date");
	  document.form1[0].todate.focus();
      return false;
   }   

}


function submitForm(){
		if(confirm('Are you sure to overwrite the existing settings'))  {		
			document.formsetting.submit();
	} else {
//			document.getElementById('dialog').style.display = 'none';
//			document.getElementById('something').checked=  false;
		}
}
	
function overwrite_option(id){
	//window.open("message.php", 1, "");
	if(id == 0) {
		//document.getElementById('dialog').style.display = 'none';
		//document.getElementById('something').checked = false;
		return false;
	}
	
//	document.getElementById('dialog').style.display = 'block';
}
</script>


<div class="wrap relative" style="margin-top:15px;">
	<ul id="form_tab_container">
		<li id="home_tab"><a href="<?php echo $base_url . '?page=' . $page; ?>">Home</a></li>
		<li id="form_fields_tab" class="<?php echo ($tab == 'fo') ? 'current' : '' ?>"><a href="#" onclick="show_tab('form_fields_tab','form_tab_container','fo_tab','eo_tab,mo_tab,dt_tab,ms_tab,adv_tab')" title="<?php _e('form') ?>"><?php if ($_REQUEST['contactform'] == "new") { echo "Create new " ; } else { echo "Edit " ;} ?><?php _e('form') ?></a></li>
		<li id="mail_options_tab"><a href="#" onclick="show_tab('mail_options_tab','form_tab_container','mo_tab','fo_tab,eo_tab,ms_tab,adv_tab,dt_tab')" title="<?php _e('mail') ?>"><?php _e('mail') ?></a></li>
		<li id="export_options_tab" class="<?php echo ($tab == 'eo') ? 'current' : '' ?>"><a href="#" onclick="show_tab('export_options_tab','form_tab_container','eo_tab','fo_tab,mo_tab,dt_tab,ms_tab,adv_tab')" title="<?php _e('advanced') ?>"><?php _e('advanced') ?></a></li>
	</ul>
	
	<div style="width:98.7%;padding-left:10px;float:left;margin-top:0px;" class="settingsblock">
		<form name="editmmform" method="post" action="<?php echo $base_url . '?page=' . $page . '&contactform=' . $current; ?>" id="mmf-admin-form-element">
			<input type="hidden" id="mmf-options-recipient" name="mmf-options-recipient" value="<?php echo htmlspecialchars($cf['options']['recipient']); ?>" />
			
			<!-- form settings -->
			<div id="fo_tab" class="<?php echo ($tab=='fo') ? 'current_tab' : 'inactive_tab'?>">				
	      		<p class="submit" style="border:none;">
					<input type="submit" class="button-primary" style="float:right;" id="mmf-save-top"  name="mmf-save" value="<?php _e('Save changes', 'mmf'); ?>" />
				</p>
				<?php require_once $includes.'form_settings.php'; ?>
			</div>
			<!-- end form settings -->
			
			<!-- mail settings -->	
			<div id="mo_tab" style="display:none;border:1px solid #FFFFFF;">
     				<p class="submit" style="border:none;">
					<input type="submit" class="button-primary" style="float:right;" id="mmf-save-top"  name="mmf-save" value="<?php _e('Save changes', 'mmf'); ?>" />
				</p>
				<?php require_once $includes.'mail_settings.php'; ?>
			</div>
			<!-- end mail settings -->
		
			<div id="eo_tab" class="<?php echo ($tab =='eo') ? 'current_tab' : 'inactive_tab'?>">
				<p class="submit" style="border:none;">
				<input type="submit" class="button-primary" style="float:right;" id="mmf-save-top"  name="mmf-save" value="<?php _e('Save changes', 'mmf'); ?>" />
				</p>
					<?php require_once $includes.'advanced_settings.php'; ?>
			</div>

			<div id="dt_tab" class="<?php echo ($tab =='dt') ? 'current_tab' : 'inactive_tab'?>">
		  		<p class="submit" style="border:none;">
					<input type="submit" class="button-primary" style="float:right;" id="mmf-save-top"  name="mmf-save" value="<?php _e('Save changes', 'mmf'); ?>" />
				</p>
				<?php require_once $includes.'frontend_settings.php'; ?>
			</div>
       
		    <div id="ms_tab" class="<?php echo ($tab =='dt') ? 'current_tab' : 'inactive_tab'?>">
				<p class="submit" style="border:none;">
				<input type="submit" class="button-primary" style="float:right;" id="mmf-save-top"  name="mmf-save" value="<?php _e('Save changes', 'mmf'); ?>" />
				</p>
   				<?php require_once $includes.'notification_settings.php'; ?>		
		    </div>

			<p class="submit" style="border:none;">
				<input type="submit" class="button-primary" style="float:right;" id="mmf-save"  name="mmf-save" value="<?php _e('Save changes', 'mmf'); ?>" />
			</p>
		</form>
	
		<div id="adv_tab" class="<?php echo ($tab =='adv') ? 'current_tab' : 'inactive_tab'?>" >
		</div>	
	</div>
</div>
<script type="text/javascript">
$("#mmf_message").animate({"height": "hide"}, { duration: 0 });
</script>
