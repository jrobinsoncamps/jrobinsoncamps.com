<?php

/*
	frontend_settings
	
	Created by Tom Belmans on 2009-09-24.
	Copyright (c) 2009 Addwittz - Motionmill. All rights reserved.
*/


?>

	<h3>Header</h3>
	<div>
    <textarea name="mmf-form_header" id="mmf-form_data" cols="45" rows="5"><?php echo $this->form_display_header($current);	?></textarea>
	</div>

  	<h3>Data</h3>
    <div>
		<textarea name="mmf-form_loopdata" id="mmf-form_data" cols="45" rows="5"><?php echo $this->form_display_loopdata($current);	?></textarea>
	</div>
	<p>
		<span style="font:Verdana, Arial, Helvetica, sans-serif; font-size:10px; font-weight:250; color:#999999;">Additionally you can use [submit_id] and [submit_date] to display the submission ID and date.</span>

 	<label><h3>Footer</h3>
	<div>
    	<textarea name="mmf-form_footer" id="mmf-form_data" cols="45" rows="5"><?php echo $this->form_display_footer($current);	?></textarea>
  	</div>

	<h3>Conditions</h3>
	<div>
    <textarea name="mmf-form_conditions" id="mmf-form_conditions" cols="45" rows="5"><?php echo $this->form_display_conditions($current);	?></textarea>
		<br />
		<span style="font:Verdana, Arial, Helvetica, sans-serif; font-size:10px; font-weight:250; color:#999999;">
		You can use wildcard charecters in your condition for optimized results.</span>
		<span style="font:Verdana, Arial, Helvetica, sans-serif; font-size:10px; font-weight:250; color:#999999;">
		1. "^" charecter means starting charcter of the field must be followed by charecters after "^" wildcard charecter.
		<br />
		e.g. <em>[your-name] = ^john</em>
		<br />
		it means that all those name starting from the word "john". So all the name starting from "john" will be returned.
		</span>
		<span style="font:Verdana, Arial, Helvetica, sans-serif; font-size:10px; font-weight:250; color:#999999;">
		2. "%" charecter means any number of charcters before or after the "%" charecter.
		<br />
		e.g. <em>[your-subject] = %message</em>
		<br />
		it means that, No matter what are the charecters in start of your-subject field, but there must be a word "message" in your-subject data
		</span>
		<span style="font:Verdana, Arial, Helvetica, sans-serif; font-size:10px; font-weight:250; color:#999999;">Example: you can also join your condtions by AND and OR. e.g <em>[your-name] = ^john OR [your-subject] = %message</em></span>
		<span style="font:Verdana, Arial, Helvetica, sans-serif; font-size:10px; font-weight:250; color:#999999;">If you want to show the submissions of a certain user use this statement <em>[user_ID] = %%loggedin_user%%</em>.</span>
	</div>