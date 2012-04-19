<?php

/*
	mail_settings
	
	Created by Tom Belmans on 2009-09-24.
	Copyright (c) 2009 Addwittz - Motionmill. All rights reserved.
*/


?>

	<h3>Mail format</h3>
	<div>
		<table>
			<tr>
				<td width="250">Send mails in html</td>
				<td><?php $checked = ($cf['mail']['mail_format'] == 'html') ? "checked" : "" ?><input type="checkbox" id="mmf-mail_format" name="mmf-mail_format" <?php echo $checked?> value="html" /></td>
			</tr>
		</table>
	</div>
	
	<h3>Mail 1</h3>
	<div>
	<table>
		<tr>
			<td width="250">
				<label for="mmf-mail-recipient"><?php _e('To:', 'mmf'); ?></label>
			</td>
			<td>
				<input type="text" id="mmf-mail-recipient" name="mmf-mail-recipient" size="50" value="<?php echo htmlspecialchars($cf['mail']['recipient']); ?>" />
			</td>
		</tr>
		<tr>
			<td>
				<label for="mmf-mail-sender"><?php _e('From:', 'mmf'); ?></label>
			</td>
			<td>
				<input type="text" id="mmf-mail-sender" name="mmf-mail-sender" size="50" value="<?php echo htmlspecialchars($cf['mail']['sender']); ?>" />
			</td>
		</tr>
		<tr>
			<td>
				<label for="mmf-mail-subject"><?php _e('Subject:', 'mmf'); ?></label>
			</td>
			<td>
				<input type="text" id="mmf-mail-subject" name="mmf-mail-subject" size="50" value="<?php echo htmlspecialchars($cf['mail']['subject']); ?>" />
			</td>
		</tr>
		<tr>
			<td>
				<label for="mmf-mail-attachment"><?php _e('Attach submitted files', 'mmf'); ?></label>
			</td>
			<td>
				<input type="checkbox" id="mmf_mail_attachment" name="mmf_mail_attachment" <?php echo $cf['mmf_mail_attachment'] == 1 ? 'checked' : ""; ?> />
			</td>
		</tr>
		<tr>
			<td>
				<label for="mmf-mail-body"><?php _e('Message body:', 'mmf'); ?></label>
			</td>
			<td>
				<textarea id="mmf-mail-body" name="mmf-mail-body" cols="70" rows="16"><?php echo htmlspecialchars($cf['mail']['body']); ?></textarea>
			</td>
		</tr>
		
	</table>
	</div>

<h3>Mail 2</h3>
<div>
<table>
	<tr>
		<td width="250">
			<label for="mmf-mail-2-active"><?php _e('Mail 2 active', 'mmf'); ?></label>
		</td>
		<td>
			<input type="checkbox" id="mmf-mail-2-active" name="mmf-mail-2-active" value="1"<?php echo ($cf['mail_2']['active']) ? ' checked="checked"' : ''; ?> />
		</td>
	</tr>
	<tr>
		<td>
			<label for="mmf-mail-2-recipient"><?php _e('To:', 'mmf'); ?></label>
		</td>
		<td>
			<input type="text" id="mmf-mail-2-recipient" name="mmf-mail-2-recipient" size="50" value="<?php echo htmlspecialchars($cf['mail_2']['recipient']); ?>" />
		</td>
	</tr>
	<tr>
		<td>
			<label for="mmf-mail-2-bcc"><?php _e('Bcc:', 'mmf'); ?></label><br />
		</td>
		<td>
			<input type="text" id="mmf-mail-2-bcc" name="mmf-mail-2-bcc" size="50" value="<?php echo htmlspecialchars($cf['mail_2']['bcc']); ?>" />
		</td>
	</tr>
	<tr>
		<td>
			<label for="mmf-mail-2-sender"><?php _e('From:', 'mmf'); ?></label>
		</td>
		<td>
			<input type="text" id="mmf-mail-2-sender" name="mmf-mail-2-sender" size="50" value="<?php echo htmlspecialchars($cf['mail_2']['sender']); ?>" />
		</td>
	</tr>
	<tr>
		<td>
				<label for="mmf-mail-2-subject"><?php _e('Subject:', 'mmf'); ?></label>
		</td>
		<td>
			<input type="text" id="mmf-mail-2-subject" name="mmf-mail-2-subject" size="50" value="<?php echo htmlspecialchars($cf['mail_2']['subject']); ?>" />
		</td>
	</tr>
	<tr>
		<td>
			<label for="mmf-mail-2-attachment"><?php _e('Attach submitted files', 'mmf');  ?></label>
		</td>
		<td>
			<input type="checkbox" id="mmf_mail2_attachment" name="mmf_mail2_attachment" <?php echo $cf['mmf_mail2_attachment'] == 1 ? 'checked' : ""; ?> />
		</td>
	</tr>
	<tr>
		<td>
			<label for="mmf-mail-2-body"><?php _e('Message body:', 'mmf'); ?></label>
		</td>
		<td>
			<textarea id="mmf-mail-2-body" name="mmf-mail-2-body" cols="70" rows="16"><?php echo htmlspecialchars($cf['mail_2']['body']); ?></textarea>
		</td>
	</tr>
</table> 
</div>