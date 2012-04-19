<?php

function install_mmf($plugindir)
{
	global $wpdb;

	$contactform_config	 = $wpdb->prefix . 'contactform_config';
	$contactform = $wpdb->prefix . 'contactform';
	$contactform_submit = $wpdb->prefix . 'contactform_submit';
	$contactform_submit_data = $wpdb->prefix . 'contactform_submit_data';
	
	$contactform_options = $wpdb->prefix . 'contactforms_options';
	
	
	if($wpdb->get_var("show tables like '$contactform_config'") != $contactform_config) {
      
		$sql = "CREATE TABLE " . $contactform_config . " (
	   `id` int(11) NOT NULL auto_increment,
	   `config_option` varchar(20) NOT NULL,
	   `config_option_value` longtext NOT NULL,
	    PRIMARY KEY  (`id`)
	    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;";
	
      dbDelta($sql); 
 				
   }

	if($wpdb->get_var("show tables like '$contactform'") != $contactform) {
      
		$sql = "CREATE TABLE " . $contactform . " (
	   `form_id` varchar(50) NOT NULL,
  	   `form_name` varchar(50) NOT NULL,
       `to` varchar(250) NOT NULL,
       `cc` varchar(250) default NULL,
       `bcc` varchar(250) default NULL,
       `form_fields` varchar(250) NOT NULL,
       `csv_separator` char(1) NOT NULL,
	   `export_form_ids` tinyint(1) NOT NULL default '0',
	   `save_data` binary(1) NOT NULL default '1',
	   `mail_format` char(4) default NULL,
       `rss_feed` TINYINT(1) default 0,
	   `all_form_fields` TINYINT(1) default 0,
	   `max_submissions` bigint(5) NOT NULL default '10',
	   `uploadfiletypes` varchar(250) default NULL,
	   `maxuploadfilesize` bigint(5)  NULL default '10',
	   `from_date` date NOT NULL,
	   `to_date` date NOT NULL,
	   `display_error` varchar(10) NOT NULL,
   	   `hide_form` varchar(3) NOT NULL default 'no',
	   `allow_user` varchar(10) default NULL,
	   `url_success` varchar(200) default NULL,
	   `url_failure` varchar(200) default NULL,
	   `total_sub` bigint(20) NOT NULL default '0',
	   `csstext` longtext NOT NULL,
	   `display_submitdata` varchar(200) NOT NULL default '0',
	   `formheader` LONGTEXT NOT NULL,
	   `formconditions` LONGTEXT NOT NULL,
	   `formloopdata` LONGTEXT NOT NULL,
	   `formfooter` LONGTEXT NOT NULL,
	   `failure_message` varchar(200) NOT NULL,
	   `success_message` varchar(200) NOT NULL,
	   `required_message` varchar(200) NOT NULL,
	   `display_message` tinyint(1) NOT NULL,
	   `mail_sent_ok` varchar(200) NOT NULL,
	   `mail_sent_ng` varchar(200) NOT NULL,
	   `validation_error` varchar(200) NOT NULL,
 	   `accept_terms_message` varchar(200) NOT NULL,
	   `invalid_email` varchar(200) NOT NULL,
	   `captcha_not_match` varchar(200) NOT NULL,
	   `mail_over_limit` varchar(200) NOT NULL,
	   `success_mes` varchar(200) NOT NULL,
	   `failure_mes` varchar(200) NOT NULL,
	   `mail_attachment` TINYINT( 2 ) NOT NULL DEFAULT '0',
	   `mail2_attachment` TINYINT( 2 ) NOT NULL DEFAULT '0',
  	    PRIMARY KEY  (`form_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
	
      dbDelta($sql);
 				
   }
	
	if($wpdb->get_var("show tables like '$contactform_submit'") != $contactform_submit) {
      
		$sql = "CREATE TABLE " . $contactform_submit . " (
	   `id` int(11) NOT NULL auto_increment,
	   `fk_form_id` varchar(50) NOT NULL,
	   `session_id` varchar(50) NOT NULL,
	   `submit_date` datetime NOT NULL,
	   `client_ip` varchar(20) NOT NULL,
	   `client_browser` varchar(100) NOT NULL,
	   `request_url` varchar(100) NOT NULL,
	   `read_flag` tinyint(1) NOT NULL default '0',
	    PRIMARY KEY  (`id`),
	  	KEY `fk_form_id` (`fk_form_id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=86 ;";
	
      dbDelta($sql); 
 				
   }
   
	if($wpdb->get_var("show tables like '$contactform_submit_data'") != $contactform_submit_data) {
      
		$sql = "CREATE TABLE " . $contactform_submit_data . " (
	  	`id` int(11) NOT NULL auto_increment,
  		`fk_form_joiner_id` int(11) NOT NULL,
  		`form_key` varchar(100) NOT NULL,
  		`value` text NOT NULL,
  		 PRIMARY KEY  (`id`),
		 KEY `fk_form_joiner_id` (`fk_form_joiner_id`)
		 ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1104 ;";
	
      	dbDelta($sql);
 				
   }   	

//ALTER TABLE `wp29`.`wp_contactform_submit_data` ADD INDEX `form_key` ( `form_key` ) 

	
	$mmf_obj = new mm_forms();
	
	$mmf = array();
	$contact_forms = array();
	$contact_forms = $mmf['contact_forms'];
	//$contact_forms[1] = $mmf_obj->default_pack(__('Contact form', 'mmf') . ' 1');
	$mmf['contact_forms'] = $contact_forms;
	$mmf_obj->update_contactform_option('mmf', $mmf , 'insert');

	mkdir($plugindir . '/upload', 0777);
	mkdir($plugindir . '/exports', 0777);
	mkdir($plugindir . '/upload/temp', 0777);
	mkdir($plugindir . '/upload/form-upload', 0777);
   
}
?>
