<script type="text/javascript">
function toggle_rss_link() {
	$("#rss_link").animate({"height": "toggle"}, { duration: 0 });
}
</script>
<h3>Data handling</h3>	
<div>
	<table>
		<?php $checked = ($cf['mail']['save_data'] == "1" || $cf['mail']['save_data'] == "") ? 'checked="checked"' : "" ?>
		<tr>
			<td width="250">Save to database and email notification</td>
			<td><input type="radio" id="mmf-save_data" name="mmf-save_data" value="1" <?php echo $checked?> /></td>
		</tr>
		<?php $checked = ($cf['mail']['save_data'] == "0") ? 'checked="checked"' : "" ?>
		<tr>
			<td>Email notification only</td>
			<td><input type="radio" id="mmf-send_mail" name="mmf-save_data" <?php echo $checked?> value="0" /></td>
		</tr>
		<?php $checked = ($cf['mail']['save_data'] == "2") ? 'checked="checked"' : "" ?>
		<tr>
			<td>Save to database only</td>
			<td><input type="radio" id="mmf-onlysave_data" name="mmf-save_data" <?php echo $checked?> value="2" /></td>
		</tr>
	</table>
</div>
<h3>List fields</h3>
<div id="list_settings" style="margin-top:20px;">
	<table>
		<tr>
			<td width="250"><?php _e('List fields','mm-forms'); ?></td>
			<td valign="top"><input type="text" id="mmf-list_fields" name="mmf-list_fields" size="50"  value="<?php echo $cf['mmf_list_fields'];?>" /><br /><span style="font:Verdana, Arial, Helvetica, sans-serif; font-size:10px; font-weight:250; color:#999999;">Comma separated list of the fields to show in the overview of the submissions.</span></td>
		</tr>
	</table>
</div>