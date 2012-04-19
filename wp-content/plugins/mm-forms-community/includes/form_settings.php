<?php wp_nonce_field('mmf-save_' . $current); ?>
<input type="hidden" id="mmf-id" name="mmf-id" value="<?php echo $current; ?>" />
<h3>Form Name</h3>
<input type="text" id="mmf-title" name="mmf-title" size="40" value="<?php echo htmlspecialchars($cf['title']); ?>" />  Click name to edit


<h3>Post and page tags</h3>
	<?php if (! $unsaved) : ?>
		<p class="tagcode" style="margin-top:0px;">
		<?php _e('To display the form in a post or page copy paste the following into your post or page.', 'mmf'); ?><br />
		<input type="text" id="form-anchor-text" onfocus="this.select();" readonly="readonly" size="30" /><br /></p>
	<?php endif; ?>

	<h3>Edit form</h3>
	<div class="fieldset" id="form-content-fieldset">
		<textarea id="mmf-form" name="mmf-form" cols="100" rows="16"><?php echo htmlspecialchars($cf['form']); ?></textarea>
	</div>