<div id="message_setting">  
   <h3>Notifications</h3>
  <div>    
   	<label  for="mmf-failure"><?php _e('Enable custom notifications', 'mmf'); ?></label>        
		<?php $checked = ($cf['mmf_display_message'] == "1" ? 'checked="checked"' : "") ?>
      <input name="mmf-display_message" type="checkbox" id="mmf-display_message" <?php echo $checked?> style="width:10px;" value="1"/>
    </div>
    <br />
	<br />
    <div>
    	<label  for="mmf-failure"><?php _e('Error occured in saving form data.', 'mmf'); ?></label>
	    <input type="text" id="failure_message" name="failure_message" value="<?php echo $cf['mmf_failure_message'];?>" onfocus="this.select();" size="80" />
    </div>  <br />
    <div>
    	<label  for="mmf-success"><?php _e('Form data saved successfully. Thanks for submitting!', 'mmf'); ?></label>
        <input type="text" id="success_message" name="success_message" value="<?php echo $cf['mmf_success_message'];?>" onfocus="this.select();" size="80" />
    </div>  <br />
    <div>	    		
	    <label  for="mmf-required"><?php _e('Please fill the required field.', 'mmf'); ?></label>
        <input type="text" id="required_message" name="required_message" value="<?php echo $cf['mmf_required_message'];?>" onfocus="this.select();" size="80" />
    </div>
   <br />
    
     <div>	    		
	    <label  for="mmf-required"><?php _e('Your message was sent successfully. Thanks.', 'mmf'); ?></label>
        <input type="text" id="mail_sent_ok" name="mail_sent_ok" value="<?php echo $cf['mmf_mail_sent_ok'];?>" onfocus="this.select();" size="80" />
    </div>  <br />
     <div>	    		
	    <label  for="mmf-required"><?php _e('Failed to send your message. Please try later or contact administrator by other way.', 'mmf'); ?></label>
        <input type="text" id="mail_sent_ng" name="mail_sent_ng" value="<?php echo $cf['mmf_mail_sent_ng'];?>" onfocus="this.select();" size="80" />
    </div>  <br />
     <div>	    		
	    <label  for="mmf-required"><?php _e('Validation errors occurred. Please confirm the fields and submit it again.', 'mmf'); ?></label>
        <input type="text" id="validation_error" name="validation_error" value="<?php echo $cf['mmf_validation_error'];?>" onfocus="this.select();" size="80" />
    </div>  <br />
     <div>
	    <label  for="mmf-required"><?php _e('Please accept the terms to proceed.', 'mmf'); ?></label>
        <input type="text" id="accept_terms_message" name="accept_terms_message" value="<?php echo $cf['mmf_accept_terms_message'];?>" onfocus="this.select();" size="80" />
    </div>  <br />
    <div>	    		
	    <label  for="mmf-required"><?php _e('Email address seems invalid.', 'mmf'); ?></label>
        <input type="text" id="invalid_email" name="invalid_email" value="<?php echo $cf['mmf_invalid_email'];?>" onfocus="this.select();" size="80" />
    </div>  <br />
     <div>	    		
	    <label  for="mmf-required"><?php _e('Your entered code is incorrect.', 'mmf'); ?></label>
        <input type="text" id="captcha_not_match" name="captcha_not_match" value="<?php echo $cf['mmf_captcha_not_match'];?>" onfocus="this.select();" size="80" />
    </div>  <br />
     <div>	    		
	    <label  for="mmf-required"><?php _e('You reached your maximum limit.', 'mmf'); ?></label>
        <input type="text" id="mail_over_limit" name="mail_over_limit" value="<?php echo $cf['mmf_mail_over_limit'];?>" onfocus="this.select();" size="80" />
    </div>  <br />
     <div>	    		
	    <label  for="mmf-required"><?php _e('On success redirect this page.', 'mmf'); ?></label>
        <input type="text" id="success_mes" name="success_mes" value="<?php echo $cf['mmf_success_mes'];?>" onfocus="this.select();" size="80" />
    </div>  <br />
    <div>
	    <label  for="mmf-required"><?php _e('On failure redirect this page.', 'mmf'); ?></label>
        <input type="text" id="failure_mes" name="failure_mes" value="<?php echo $cf['mmf_failure_mes'];?>" onfocus="this.select();" size="80" />
    </div>
</div>