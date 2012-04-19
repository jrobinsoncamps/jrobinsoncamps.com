<script type="text/javascript">
var xmlhttp = false; // variable xmlhttp
//Checking whether browser is the latest version of IE i.e > IE6
try {
//If yes, create object of XMLHttpRequest
xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
//alert("You are using the latest IE browser");
} catch(e) {
//IF not latest browser i.e < IE6
//Checking if browser is a old version of IE
try {
//If yes, create object of XMLHttpRequest
xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
//alert("You are using an old version of IE browser");
} catch(e) {
//If not both, then it is a NON-IE Browser
xmlhttp=false;
}
}
//Checking if browser is a non IE browser
if(!xmlhttp && typeof XMLHttpRequest != 'undefined') {
//Creating an object of XMLHttpRequest in non-IE browsers
xmlhttp = new XMLHttpRequest();
//alert("You are using non IE Browser");
}

function ajaxRequest(scriptPage, elementID, resulturl) {
	document.getElementById(elementID).style.display = "block";
	var obj = document.getElementById(elementID);
	var title_obj = document.getElementById('Download');
	xmlhttp.open("GET", scriptPage); //Opens connection to the scriptpage using GET response method
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			obj.innerHTML = "<a href='" + resulturl + "'>download now</a>";
			title_obj.innerHTML = "Download CSV";
		}
	}
	xmlhttp.send(null);
}

</script>

<?php if (!$cf) : ?>
	<div class="wrap relative" style="margin-top:15px;">
	<ul id="form_tab_container">
		<li id="home_tab" class="current"><a href="<?php echo $base_url . '?page=' . $page; ?>">Home</a></li>
		<li id="new_tab"><a href="<?php echo $base_url . '?page=' . $page . '&contactform=new&tab=fo'; ?>"><?php _e('Create New form') ?></a></li>
	</ul>
	</div>
	<div style="margin-left:5px;margin-top:20px">
	<table class="widefat">
	  <thead>
	  <tr>
	<?php foreach($obj_actions->view_form_columns as $class => $column_display_name) {
		$class = ' class="'.$class.'" ';
	?>
		<th scope="col"<?php echo $class; ?> id="<?php echo $column_display_name ; ?>"><?php echo $column_display_name; ?></th>
	<?php } ?>
	  </tr>
	 </thead>
	<?php foreach ($obj_actions->contact_forms as $k => $v) : ?>
	

		<?php $class = ($obj_actions->form_info[$k]['num_of_unread_emails'] > 0) ? 'new_email' : ''; ?>
		
		<tr class="<?php echo $class ?>">
			<th style="font-weight:normal;" scope="col"><?php if ($k == $current) echo '&raquo; '; ?>
				<a href="<?php echo $base_url . '?page=' . $page . '&action=view&id=' . $k ?>"><?php echo '<b>'.$v['title'].'</b> ('.$obj_actions->form_info[$k]['num_of_unread_emails'].' New / '.$obj_actions->form_info[$k]['num_of_emails'].' Total)'; ?>
				</a> </th>
			<th style="font-weight:normal;" scope="col"><?php if ($k == $current) echo '&raquo; '; ?><?php echo '[form '.$k.' "'.$v['title'].'"]' ?></th>
			<th style="font-weight:normal;" scope="col"><?php if ($k == $current) echo '&raquo; '; ?>
				<a href="<?php echo $base_url . '?page=' . $page . '&action=view&id=' . $k ?>">
					<img src="<?php echo $image_path.'view.png'?>" />
				</a></th>
				<th style="font-weight:normal;" scope="col"><?php if ($k == $current) echo '&raquo; '; ?>
					<a href="<?php echo $base_url . '?page=' . $page . '&contactform=' . $k.'&tab=fo'; ?>">
						<img src="<?php echo $image_path.'pencil.png'?>" />
					</a></th>				

			
			<th style="font-weight:normal;" scope="col"><?php if ($k == $current) echo '&raquo; '; ?>
				<a href="<?php echo $base_url . '?page=' . $page . '&action=deleteform&id=' . $k ?>">
					<img onclick="return confirm('Are you sure you want to delete this form')" src="<?php echo $image_path.'delete.png'?>" />
				</a></th>	
	   </tr>
	<?php endforeach; ?>
	  </tbody>
	</table>
	<p>&nbsp;</p>
		<h3>Support MM Forms Community</h3>
		<p>
			MM Forms Community plugin is developed by <a href="http://motionmill.com">Motionmill</a>.</p>
		<p>
			Motionmill has a lot of experience in building websites using Wordpress.<br />
	When we encounter issues, we try to solve them. This is translated in the creation of new plugins or modification of existing plugins.<br />
	For <a href="http://plugins.motionmill.com/mm-forms/">MM Forms Community</a> we were inspired by Contact Form 7.  Nevertheless this was and still is a great plugin, we found a lot of gaps.<br />
	So we decided to build a new plugin with all necessary specifications.
	</p>
		<p>If you like this plugin consider donating some money.</p>
		<p>
		<p>
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
			<input type="hidden" name="cmd" value="_s-xclick">
			<input type="hidden" name="hosted_button_id" value="6044941">
			<input type="image" src="https://www.paypal.com/en_US/BE/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
			<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
			</form>
		</p>
	<br />
	<p><bold>Click on the icon to download the MM Forms Community manual</bold></p>
	<p><a href="<?php echo get_option('siteurl') . '/wp-content/plugins/mm-forms-community/manual/mm-forms_tutorial.pdf'; ?>"><img src="<?php echo get_option('siteurl') . '/wp-content/plugins/mm-forms-community/images/pdf_icon.gif'; ?>" /></a></p>
	<p>&nbsp;</p>
		<p><button onclick="showUninstallBlock();">Uninstall MM Forms Community</button></p>
		<div id="uninstall_block">
        <form method="post" action = "<?php echo $base_url . '?page=' . $page . '&contactform=uninstall'; ?>">
	<label for="uninstall"><?php _e('Type "Uninstall" in the box if you want to completely remove MM Forms plugin.  This includes deleting all
 your data and database tables.','mm-forms'); ?><br /></label>
                <label style="color:#fff;text-decoration:bold;">Warning : this action cannot be undone!  So please be careful when using this.</label>
				<br /><br />
            <input type="text" name="uninstall" id="uninstall"> <input type="submit" name="Uninstall" value="<?php _e("Remove MM Forms Community"); ?>">
        </form>
		</div>
	</div>
<?php endif; ?>	
<script language="javascript">
	
	$("#uninstall_block").animate({"height": "hide"}, { duration: 0 });

	//<![CDATA[
	function showUninstallBlock(){
	$("#uninstall_block").animate({"height": "toggle"}, { duration: 1000 });
	}
	//]]>
	

	var allPageTags = new Array(); 

	var allPageTags=document.getElementsByTagName("*");
	for (i=0; i<allPageTags.length; i++) {
		if (allPageTags[i].className=="ajax_resultdiv") {
			allPageTags[i].style.display='none';
		}
	}

	$("#mmf_message").animate({"height": "hide"}, { duration: 0 });

</script>
