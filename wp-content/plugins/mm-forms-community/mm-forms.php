<?php
/*
Plugin Name: MM Forms Community
Plugin URI: http://plugins.motionmill.com
Description: MM Forms Community comes with the power of a tank, but drives like a bike. 
Author: Motionmill
Version: 1.1
Author URI: http://motionmill.com/
*/
/*  Copyright 2008 Motionmill (email: plugins at motionmill dot com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

define('CONTACTFORM', dirname(plugin_basename(__FILE__)));
$includes = ABSPATH . PLUGINDIR . '/'. CONTACTFORM.'/includes/';
require_once("includes/functions.php");

class mm_forms {

	var $contact_forms;
	var $captcha;
	
	function mm_forms() {

		add_action('init', array(&$this, 'load_plugin_textdomain'));
		add_action('admin_menu', array(&$this, 'add_pages'));
		add_action('admin_head', array(&$this, 'admin_head'));
    	add_action('wp_head', array(&$this, 'wp_head'));
		add_action('wp_print_scripts', array(&$this, 'load_js'));
		add_action('init', array(&$this, 'init_switch'), 11);
		add_filter('the_content', array(&$this, 'the_content_filter'), 9);
		add_filter('widget_text', array(&$this, 'widget_text_filter'));
		add_filter('plugin_action_links', array(&$this, 'mm_plugin_links'), 10, 2 );
		if (remove_filter('the_content', 'wpautop'))
            add_filter('the_content', array(&$this, 'wpautop_substitute'));
		
		add_action('admin_notices', array(&$this, 'check_installation'));			
		register_activation_hook(CONTACTFORM.'/mm-forms.php',array(&$this, 'mm_forms_install'));
		add_action('init', array(&$this, 'export_file'), 11);
		add_action('init', array(&$this, 'mm_forms_tinymce_addbuttons'), 10);

	}

	function check_installation(){
		if ($_GET['page'] == CONTACTFORM ."/mm-forms.php") return;
		
		$mmf_dir_list = "";
		$mmf_dir_list2 = "";
		
		define ('MMF_DIR', dirname(__FILE__));

		if (!is_dir(MMF_DIR."/upload/")){
			$mmf_dir_list2.= "<li>".MMF_DIR."/upload" . "</li>";
		}elseif (!is_writable(MMF_DIR."/upload")){
			$mmf_dir_list.= "<li>".MMF_DIR."/upload" . "</li>";
		}

		if (!is_dir(MMF_DIR."/upload/form-upload")){
			$mmf_dir_list2.= "<li>".MMF_DIR."/upload/form-upload" . "</li>";
		}elseif (!is_writable(MMF_DIR."/upload/form-upload")){
			$mmf_dir_list.= "<li>".MMF_DIR."/upload/form-upload" . "</li>";
		}
		if (!is_dir(MMF_DIR."/upload/temp")){
			$mmf_dir_list2.= "<li>".MMF_DIR."/upload/temp" . "</li>";
		}elseif (!is_writable(MMF_DIR."/upload/temp")){
			$mmf_dir_list.= "<li>".MMF_DIR."/upload/temp" . "</li>";
		}
		if (!is_dir(MMF_DIR."/captcha/tmp")){
			$mmf_dir_list2.= "<li>".MMF_DIR."/captcha/tmp" . "</li>";
		}elseif (!is_writable(MMF_DIR."/captcha/tmp")){
			$mmf_dir_list.= "<li>".MMF_DIR."/captcha/tmp" . "</li>";
		}
		if (!is_dir(MMF_DIR."/exports")){
			$mmf_dir_list2.= "<li>".MMF_DIR."/exports" . "</li>";
		}elseif (!is_writable(MMF_DIR."/exports")){
			$mmf_dir_list.= "<li>".MMF_DIR."/exports" . "</li>";
		}

		if ($mmf_dir_list2 != ""){
			echo "<div id='mmf-install-error-message' class='error'><p><strong>".__('MM Forms Community is not ready yet.', 'mmf')."</strong> ".__('You must create the following folders (and they must be writable):', 'mmf')."</p><ul>";
			echo $mmf_dir_list2;
			echo "</ul></div>";
		}
		if ($mmf_dir_list != ""){
			echo "<div id='mmf-install-error-message' class='error'><p><strong>".__('MM Forms Community is not ready yet.', 'mmf')."</strong> ".__('The following folders must be writable (usually chmod 777 is neccesary):', 'mmf')."</p><ul>";
			echo $mmf_dir_list;
			echo "</ul></div>";
		}
	}

	function export_file()
	{
		if($_GET['action'] == 'export')
		{
			global $includes;
			require_once $includes.'class_actions.php';
			$obj_actions = new actions($_GET['action'], '' , $_GET['id'], '', 0, 0);
		}       
	}
	function mm_forms_install()
	{
		include_once (dirname (__FILE__)."/includes/mm-forms-install.php");
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		install_mmf(ABSPATH.PLUGINDIR."/".CONTACTFORM);
	}
	
	
	function recursive_remove_directory($directory, $empty=FALSE)
 	{
		 // if the path has a slash at the end we remove it here
		 if(substr($directory,-1) == '/')
		 {
			 $directory = substr($directory,0,-1);
		 }
	  
		 // if the path is not valid or is not a directory ...
		 if(!file_exists($directory) || !is_dir($directory))
		 {
			 // ... we return false and exit the function
			 return FALSE;
	  
		 // ... if the path is not readable
		 }elseif(!is_readable($directory))
		 {
			 // ... we return false and exit the function
			 return FALSE;
	  
		 // ... else if the path is readable
		 }else{
	  
			 // we open the directory
			 $handle = opendir($directory);
	  
			 // and scan through the items inside
			 while (FALSE !== ($item = readdir($handle)))
			 {
				 // if the filepointer is not the current directory
				 // or the parent directory
				 if($item != '.' && $item != '..')
				 {
					 // we build the new path to delete
					 $path = $directory.'/'.$item;
	  
					 // if the new path is a directory
					 if(is_dir($path)) 
					 {
						 // we call this function with the new path
						 $this->recursive_remove_directory($path);
	  
					 // if the new path is a file
					 }else{
						 // we remove the file
						 unlink($path);
					 }
				 }
			 }
			 // close the directory
			 closedir($handle);
	  
			 // if the option to empty is not set to true
			 if($empty == FALSE)
			 {
				 // try to delete the now empty directory
				 if(!rmdir($directory))
				 {
				   // return false if not possible
				   return FALSE;
			   }
		   }
		   // return success
		   return TRUE;
		 }
 	}
	
	function mm_forms_uninstall()
	{	
		global $wpdb;
		
		$contactform_config	 = $wpdb->prefix . 'contactform_config';
		$contactform = $wpdb->prefix . 'contactform';
		$contactform_submit = $wpdb->prefix . 'contactform_submit';
		$contactform_submit_data = $wpdb->prefix . 'contactform_submit_data';
			
		$wpdb->query("DROP TABLE $contactform_config");
		$wpdb->query("DROP TABLE $contactform");
		$wpdb->query("DROP TABLE $contactform_submit");
		$wpdb->query("DROP TABLE $contactform_submit_data");
			
		$this->recursive_remove_directory('../wp-content/plugins/'.CONTACTFORM. '/upload');
		//delete_option('mmf');
	}
	// Original wpautop function has harmful effect on formatting of form elements.
	// This wpautop_substitute is a temporary substitution until original is patched.
	// See http://trac.wordpress.org/ticket/4605
	function wpautop_substitute($pee, $br = 1) {
		$pee = $pee . "\n"; // just to make things a little easier, pad the end
		//return $pee;		
		$pee = preg_replace('|<br />\s*<br />|', "\n\n", $pee);
		// Space things out a little
		$allblocks = '(?:address|area|blockquote|caption|colgroup|dd|div|dl|dt|form|h[1-6]|li|map|math|ol|p|pre|table|tbody|td|tfoot|th|thead|tr|ul)';
		$pee = preg_replace('!(<' . $allblocks . '[^>]*>)!', "\n$1", $pee);
		$pee = preg_replace('!(</' . $allblocks . '>)!', "$1\n\n", $pee);
		$pee = str_replace(array("\r\n", "\r"), "\n", $pee); // cross-platform newlines
		$pee = preg_replace("/\n\n+/", "\n\n", $pee); // take care of duplicates
		$pee = preg_replace('/\n?(.+?)(?:\n\s*\n|\z)/s', "<p>$1</p>\n", $pee); // make paragraphs, including one at the end
		$pee = preg_replace('|<p>\s*?</p>|', '', $pee); // under certain strange conditions it could create a P of entirely whitespace
		$pee = preg_replace('!<p>([^<]+)\s*?(</(?:div|address|form)[^>]*>)!', "<p>$1</p>$2", $pee);
		$pee = preg_replace( '|<p>|', "$1<p>", $pee );
		$pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee); // don't pee all over a tag
		$pee = preg_replace("|<p>(<li.+?)</p>|", "$1", $pee); // problem with nested lists
		$pee = preg_replace('|<p><blockquote([^>]*)>|i', "<blockquote$1><p>", $pee);
		$pee = str_replace('</blockquote></p>', '</p></blockquote>', $pee);
		$pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)!', "$1", $pee);
		$pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee);
		if ($br) {
			$pee = preg_replace('/<(script|style).*?<\/\\1>/se', 'str_replace("\n", "<WPPreserveNewline />", "\\0")', $pee);
			$pee = preg_replace('|(?<!<br />)\s*\n|', "<br />\n", $pee); // optionally make line breaks
			$pee = str_replace('<WPPreserveNewline />', "\n", $pee);
		}
		$pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*<br />!', "$1", $pee);
		$pee = preg_replace('!<br />(\s*</?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)[^>]*>)!', '$1', $pee);
		if (strpos($pee, '<pre') !== false)
			$pee = preg_replace('!(<pre.*?>)(.*?)</pre>!ise', " stripslashes('$1') .  stripslashes(clean_pre('$2'))  . '</pre>' ", $pee);
		$pee = preg_replace( "|\n</p>$|", '</p>', $pee );		
		return $pee;
	}

	function init_switch() {
		if ('POST' == $_SERVER['REQUEST_METHOD'] && $_POST['_mmf_is_ajax_call']) {
			$this->ajax_json_echo();
			exit();
		} elseif (! is_admin()) {
			$this->process_nonajax_submitting();
			$this->cleanup_captcha_files();
		}
	}
	
	function ajax_json_echo() {
	
		if (isset($_POST['_mmf'])) {
			$id = (int) $_POST['_mmf'];
			$unit_tag = $_POST['_mmf_unit_tag'];
			$contact_forms = $this->contact_forms();
			$cf = $contact_forms[$id];
			if ($cf) {
			
				$cf = stripslashes_deep($cf);				
				$validation = $this->validate($cf);				
				$captchas = $this->refill_captcha($cf);
				if (! empty($captchas)) {
					$captchas_js = array();
					foreach ($captchas as $name => $cap) {
						$captchas_js[] = '"' . $name . '": "' . $cap . '"';
					}
					$captcha = '{ ' . join(', ', $captchas_js) . '}';
				} else {
					$captcha = 'null';
				}
				$cf['mail']['mmf_hide_form'] = $cf['mail']['mmf_hide_form'] == "yes"  ? 1 : 0;
 				//@header('Content-Type: text/plain; charset=' . get_option('blog_charset'));
 				@header('Content-type: application/json; charset=' . get_option('blog_charset'));
                
				if (! $validation['valid']) { // Validation error occured
					$invalids = array();
					foreach ($validation['reason'] as $name => $reason) {
						$invalids[] = array("into"=>'span.mmf-form-control-wrap.' . $name,"message"=>$reason);
					}
					
					// start bram
					$response = array(	"mailSent"=>0,
										"message"=>$this->message('validation_error'),
										"into"=>"#".$unit_tag,
										"invalids"=>$invalids,
										"captcha"=>$captchas,
										"hidefrm"=>$cf['mail']['mmf_hide_form']);
					echo json_encode($response);
					
                } 
				elseif (! $this->acceptance($cf)) { // Not accepted terms
                    //echo '{"mailSent":0,"message":"' . js_escape($this->message('accept_terms')) . '","into":"#' . $unit_tag . '","captcha":' . $captcha . ',"hidefrm":'.$cf['mail']['mmf_hide_form'].'}';
                    // start bram
					$response = array(	"mailSent"=>0,
										"message"=>$this->message('accept_terms'),
										"into"=>"#".$unit_tag,
										"captcha"=>$captchas,
										"hidefrm"=>$cf['mail']['mmf_hide_form']);
					echo json_encode($response);

                    
				} 
				elseif ($this->akismet($cf)) { // Spam!
					// start bram
					$response = array(	"mailSent"=>0,
										"message"=>$this->message('mail_sent_ng'),
										"into"=>"#".$unit_tag,
										"captcha"=>$captchas,
										"hidefrm"=>$cf['mail']['mmf_hide_form']);
					echo json_encode($response);
					//echo '{"mailSent":0,"message":"' . js_escape($this->message('mail_sent_ng')) . '","into":"#' . $unit_tag . '","spam":1,"captcha":' . $captcha . ',"hidefrm":'.$cf['mail']['mmf_hide_form'].'}';
				} 
				elseif ($cf['mail']['save_data'] == 2)
				{
				/* Modified by Hitesh: Check Limit of total submition */
						if($this->checklimit($id))
						{
								if($this->submit_form($id))
								{
								$response = array(	"mailSent"=>1,
										"message"=>$this->message('mail_data_saved'),
										"into"=>"#".$unit_tag,
										"captcha"=>$captchas,
										"hidefrm"=>$cf['mail']['mmf_hide_form']);
								echo json_encode($response);
								//echo '{"mailSent":1,"message":"' . js_escape($this->message('mail_data_saved')) . '","into":"#' . $unit_tag . '","captcha":' . $captcha . ',"hidefrm":'.$cf['mail']['mmf_hide_form'].'}';
								}
								else 
								{
									
									$response = array(	"mailSent"=>0,
											"message"=>$this->message('mail_data_not_saved'),
											"into"=>"#".$unit_tag,
											"captcha"=>$captchas,
											"hidefrm"=>$cf['mail']['mmf_hide_form']);
									echo json_encode($response);
									//echo '{"mailSent":0,"message":"' . js_escape($this->message('mail_data_not_saved')) . '","into":"#' . $unit_tag . '","captcha":' . $captcha . ',"hidefrm":'.$cf['mail']['mmf_hide_form'].'}';
								}						
						}
						else
						{
							$response = array(	"mailSent"=>0,
									"message"=>$this->message('mail_over_limit'),
									"into"=>"#".$unit_tag,
									"captcha"=>$captchas,
									"hidefrm"=>$cf['mail']['mmf_hide_form']);
							echo json_encode($response);
							//		echo '{"mailSent":0,"message":"' . js_escape($this->message('mail_over_limit')) . '","into":"#' . $unit_tag . '","captcha":' . $captcha . ',"hidefrm":'.$cf['mail']['mmf_hide_form'].'}';								
						}				
				} 
				elseif ($cf['mail']['save_data'] == 1) 
				{				
					if($this->checklimit($id)) //ckech limit for mail and database
					{
                        if($this->mail($cf)){
                            $this->save_form_data($id,$_POST);
							$response = array(	"mailSent"=>1,
									"message"=>$this->message('mail_sent_ok'),
									"into"=>"#".$unit_tag,
									"captcha"=>$captchas,
									"hidefrm"=>$cf['mail']['mmf_hide_form']);
							echo json_encode($response);
                            //echo '{"mailSent":1,"message":"' . js_escape($this->message('mail_sent_ok')) . '","into":"#' . $unit_tag . '","captcha":' . $captcha . ',"hidefrm":'.$cf['mail']['mmf_hide_form'].'}';
                        } else {
                        	$response = array(	"mailSent"=>0,
									"message"=>$this->message('mail_sent_ng'),
									"into"=>"#".$unit_tag,
									"captcha"=>$captchas,
									"hidefrm"=>$cf['mail']['mmf_hide_form']);
							echo json_encode($response);
                            // echo '{"mailSent":0,"message":"' . js_escape($this->message('mail_sent_ng')) . '","into":"#' . $unit_tag . '","captcha":' . $captcha . ',"hidefrm":'.$cf['mail']['mmf_hide_form'].'}';
                        }
                	}
					else
					{
                    	$response = array(	"mailSent"=>0,
								"message"=>$this->message('mail_over_limit'),
								"into"=>"#".$unit_tag,
								"captcha"=>$captchas,
								"hidefrm"=>$cf['mail']['mmf_hide_form']);
						echo json_encode($response);
						//echo '{"mailSent":0,"message":"' . js_escape($this->message('mail_over_limit')) . '","into":"#' . $unit_tag . '","captcha":' . $captcha . ',"hidefrm":'.$cf['mail']['mmf_hide_form'].'}';
					}
				} 
				elseif ($cf['mail']['save_data'] == 0) 
				{
				if($this->checklimit($id)) //ckech limit for mail and database
					{
                        if($this->mail($cf)){
							$this->updatechecklimit($id);
	                    	$response = array(	"mailSent"=>1,
									"message"=>$this->message('mail_sent_ok'),
									"into"=>"#".$unit_tag,
									"captcha"=>$captchas,
									"hidefrm"=>$cf['mail']['mmf_hide_form']);
							echo json_encode($response);
                            // echo '{"mailSent":1,"message":"' . js_escape($this->message('mail_sent_ok')) . '","into":"#' . $unit_tag . '","captcha":' . $captcha . ',"hidefrm":'.$cf['mail']['mmf_hide_form'].'}';
                        } else {
                        	$this->updatechecklimit($id);
	                    	$response = array(	"mailSent"=>0,
									"message"=>$this->message('mail_sent_ng'),
									"into"=>"#".$unit_tag,
									"captcha"=>$captchas,
									"hidefrm"=>$cf['mail']['mmf_hide_form']);
							echo json_encode($response);
                            //echo '{"mailSent":0,"message":"' . js_escape($this->message('mail_sent_ng')) . '","into":"#' . $unit_tag . '","captcha":' . $captcha . ',"hidefrm":'.$cf['mail']['mmf_hide_form'].'}';
                        }
					}
					else
					{
                    	$response = array(	"mailSent"=>0,
								"message"=>$this->message('mail_over_limit'),
								"into"=>"#".$unit_tag,
								"captcha"=>$captchas,
								"hidefrm"=>$cf['mail']['mmf_hide_form']);
						echo json_encode($response);
						// echo '{"mailSent":0,"message":"' . js_escape($this->message('mail_over_limit')) . '","into":"#' . $unit_tag . '","captcha":' . $captcha . ',"hidefrm":'.$cf['mail']['mmf_hide_form'].'}';
					}
                }
			}
		}
	}


    function submit_form($id) {		
			$this->save_form_data($id,$_POST);
		    return true;
    }
	
	function checklimit($id) 
	{
		//Modified by hitesh : Error message when over limit 		
			global $wpdb, $user_ID;
			$sql="SELECT count(id) as tot FROM " . $wpdb->prefix . "contactform_submit where fk_form_id=".$id;		   
            $results =  $wpdb->get_results($sql);
			foreach ($results as $result) {
			$myResult=$result->tot;
			}
			
			$sql="SELECT total_sub FROM " . $wpdb->prefix . "contactform where form_id=".$id;		   
			$results =  $wpdb->get_results($sql);
			foreach ($results as $result) 
			{
				$totResult=$result->total_sub;
			}
			if($totResult=='')
				$totResult=0;
			
			$myResult=$totResult + $myResult;
			
			$sql="SELECT max_submissions FROM " . $wpdb->prefix . "contactform where form_id=".$id;
            $max_subs =  $wpdb->get_results($sql);
			foreach ($max_subs as $max_sub) {
			$myMax=$max_sub->max_submissions;
			}
			if($myMax==0)
			{
				return true;
			}
			else
			{
				if($myResult<$myMax)
					return true;
				else
					return false;
			}
    }	
	
	function updatechecklimit($id)
	{
		//Modified by hitesh : Update limit when forms is send		
		global $wpdb;
		$sql="SELECT total_sub FROM " . $wpdb->prefix . "contactform where form_id=".$id;
		$results =  $wpdb->get_results($sql);
		foreach ($results as $result)
		{ $myResult=$result->total_sub;	}	
		
		$where = array("form_id" => $id);
		$values_contactform['total_sub'] = $myResult + 1;
		$wpdb->update($wpdb->prefix."contactform",$values_contactform,$where);
    }
    
/* This function is disabled for now must be replaced by groups instead of users */
/*	function checkUserAuthentication($id) 
	{
		if (is_numeric($id)) {
			return true;
		} else {
			//Modified by hitesh : Check form show for User/Group
			global $wpdb, $user_ID;
			$sql="SELECT allow_user FROM " . $wpdb->prefix . "contactform where form_id=".$id;
            $select_user_lists =  $wpdb->get_results($sql);
			foreach ($select_user_lists as $select_user_list)
			{ $select_user_list_main=$select_user_list->allow_user;	}
			$selectLists=split(" ", $select_user_list_main);
			if($selectLists[0]=="")
			{
				$flag=true;			
			}
			else
			{
				for($j=0;$j<count($selectLists);$j++)
				{
					if($selectLists[$j]==$user_ID) {$flag = true; break;}
					else{$flag = false;}
				}
			}
			return $flag;
		}
    } */
	
	function test(){
	
		echo "TEsting";
	}
	
	function getsuccessurl($id) 
	{
		//Modified by hitesh : Redirect url on success
			global $wpdb, $user_ID;
			$sql="SELECT url_success FROM " . $wpdb->prefix . "contactform where form_id=".$id;
            $select_url_success =  $wpdb->get_results($sql);
			foreach ($select_url_success as $select_url)
			{ 
				$select_url_main=$select_url->url_success;
			}
			return $select_url_main;
    }
	
	function getfailureurl($id) 
	{
		//Modified by hitesh : Redirect url on failure
			global $wpdb, $user_ID;
			$sql="SELECT url_failure FROM " . $wpdb->prefix . "contactform where form_id=".$id;
            $select_url_failure =  $wpdb->get_results($sql);
			foreach ($select_url_failure as $select_url)
			{ 
				$select_url_main=$select_url->url_failure;
			}			
			return $select_url_main;
    }	
	
	function checkdateavailability($id)
	{
		//Modified by hitesh : Check Date Availability
			global $wpdb, $user_ID;
						
			$currentdate=date("Y-m-d");
			$sql="SELECT from_date, to_date FROM " . $wpdb->prefix . "contactform where form_id=".$id;		   
            $select_dates =  $wpdb->get_results($sql);
			foreach ($select_dates as $select_date) 
			{
			$select_from_date=$select_date->from_date;
			$select_to_date=$select_date->to_date;
			}
			
			if($select_from_date=='0000-00-00' || $select_to_date=='0000-00-00')
			{
				return true;
			}
			else
			{
				if($currentdate>=$select_from_date && $currentdate<=$select_to_date)
					return true;
				else
					return false;
			}
    }
	
	
	function find_in_list($data_arr, $key, $value)
  	{
		$wildcards = array('%', '^', '$', '*', '?');
		$value = trim($value, ' "\'');
		
		$w = 0;
		foreach($wildcards as $wildcard){
			if(strpos($value, $wildcard) !== false){
				$w = 1;
				break;
			}				
		}
		
		foreach($data_arr as $obj) {
		
			if(stristr($obj->form_key, trim($key, " []")) !== false) {	
				$arr = explode(",", $value);
				//print_r($arr);
				foreach($arr as $v) {

					if($w == 1) {
						$v = str_replace("%", "*", $v);
						if (eregi(trim($v), $obj->value)) {
							$flag = 1;
							break;
						}
					} else {					
						if (trim($v) == $obj->value) {
							$flag = 1;
							break;
						}
					}	
				}
			}
		} // foreach($data_arr as $obj) {
		
		return $flag;
	 //return false;
 	}
	
	function find($data_arr, $key, $value)
  	{
  	 
	 	$wildcards = array('%', '^', '$', '*', '?');
		$value = trim($value, ' "\'');

		$w = 0;
		foreach($wildcards as $wildcard){
			if(strpos($value, $wildcard) !== false){
				$w = 1;
				break;
			}				
		}
		
		
		foreach($data_arr as $obj) {
			if(stristr($obj->form_key, trim($key, " []")) !== false) {
					if($w == 1) {
						//echo "<br>".$value ."----". $obj->value; 
						$value = str_replace("%", ".*", $value);
						if (eregi($value, $obj->value)) {
							$flag = 1;
							break;
						}
					} else {
						//echo "<br>".$value ."----". $obj->value; 					
						if ($value == $obj->value) {
							$flag = 1;
							break;
						}
					}
			}
		} // foreach($data_arr as $obj) {
		
		return $flag;
	 //return false;
 	}  
	
	function break_by_operator($condition) {
	
		$type = "";
		
		if($value == "") {
			list($key, $value) = explode("=", $condition);
			$type = "=";
		}
		
		
		if($value == "") {
			list($key, $value) = explode(" like ", $condition);
			$type = "LIKE";
		}
		
		if($value == "") {
			list($key, $value) = explode(" in ", $condition);
			$type = "IN";
		}
		
		$arr = array ('key' => $key, 'value'=> $value,  'type' => $type );
		/*
		echo "<pre>";
		print_r($arr);
		echo "</pre>";
		*/
		return $arr;
			
	
	}
	function satisfy_condition($data_arr, $condition){
		global $user_ID;
		

		/* Replace %%loggedin_user%% by userid */
			$pattern = "/\%%loggedin_user%%/ims";
			$condition = preg_replace($pattern, $user_ID, $condition);
			
		if(trim($condition) == "")
			return true;
		
		$cond = "";
		
		$condition_parts = array();
		if(stripos($condition, " or ")) {
			$cond = "OR";
			$condition_parts = explode(" or ", strtolower($condition)) ;
		}else if(stripos($condition, " and ")) { 
			$cond = "AND";
			$condition_parts = explode(" and ", strtolower($condition)) ;
		}else {
			$cond = "NO";
		}

		foreach($condition_parts  as $user_condition) {
			$c_parts = $this->break_by_operator($user_condition);
			$flag = 0; 
	
			if($cond == "AND") {
			
				if($c_parts['type'] == 'LIKE' || $c_parts['type'] == '=') {
					$flag = $this->find($data_arr, $c_parts['key'], $c_parts['value']);
				} elseif($c_parts['type'] == 'IN') {
					$flag = $this->find_in_list($data_arr, $c_parts['key'], $c_parts['value']);
				} else
					return true; // if condition in not valid then row will be printed
				 	
				if($flag == 0)
				  return false;
			} // if($cond == "AND") {
			
			if($cond == "OR") {
				if($c_parts['type'] == 'LIKE' || $c_parts['type'] == '=') {
					$flag = $this->find($data_arr, $c_parts['key'], $c_parts['value']);
				}elseif($c_parts['type'] == 'IN') {
					$flag = $this->find_in_list($data_arr, $c_parts['key'], $c_parts['value']);
				}else
					return true; // if condition in not valid then row will be printed
			 	
				if($flag == 1)
					return true;
			} // if($cond == "OR") {
		}
		
		if($cond == "AND" and $flag == 1) {
				return true;
		}
		
		if($cond == "NO") {
			$c_parts = $this->break_by_operator(strtolower($condition));
			$flag = 0;
			if($c_parts['type'] == 'LIKE' || $c_parts['type'] == '=') {
					$flag = $this->find($data_arr, $c_parts['key'], $c_parts['value']);
			}elseif($c_parts['type'] == 'IN') {
					$flag = $this->find_in_list($data_arr, $c_parts['key'], $c_parts['value']);
			}else
					return true; // if condition in not valid then row will be printed
			if($flag == 1)
				return true;		
		}		
		return false;	
	}

	function displaysubmitdata($submitdataformid, $conditons)
	{
		global $wpdb;
		$tableformat="";
		$sql="SELECT formheader,formloopdata,formfooter FROM " . $wpdb->prefix . "contactform where form_id =". $submitdataformid;
		$select_datas =  $wpdb->get_results($sql);
		$formheader = $select_datas[0]->formheader;
		$formfooter = $select_datas[0]->formfooter;
		$formloopdata = $select_datas[0]->formloopdata;

		if ($formheader <> '')
		{
			$tableformat.=$formheader;
		}			
		if ($formloopdata <> '')
		{
			// PHP 4 workaround
			if(! function_exists('str_split'))
		    {
		        function str_split($text, $split = 1)
		        {
		            $array = array();
					for ($i = 0; $i < strlen($text); $i += $split)
		            {
		                $array[] = substr($text, $i, $split);
		            }
					return $array;
		        }
		    }
			$splitdata123=$formloopdata;
			$replace_array = array('[', ']');
			$strarray=str_split($formloopdata);
			$tflag=false;
			$myStr="('";
			$mycount=0;			
			for($i=0;$i<count($strarray);$i++)
			{		
				//echo $strarray[$i]."<br>";
				if($strarray[$i]=='[')
				{
					$tflag=true;
				}
				if($strarray[$i]==']')
				{
					$tflag=false;
					$myStr.="','";
					$mycount=$mycount+1;
				}				   
				if($tflag==true and $strarray[$i]!='[')
				{
					$myStr=$myStr.$strarray[$i];
				}	
			}			
			//$myStr=rtrim($myStr,",'");
			$myStr.="')";	
			$splitdata=explode('[',$formloopdata);
			$splitdata= str_replace(']','',$splitdata);
			$datalist = '("'.str_replace(' ','',$splitdata[1]);
			for($c=2;$c<count($splitdata);$c++)
			{
				$datalist.='","'.str_replace(' ','',$splitdata[$c]);
			}					
							
			$s=substr($datalist,0,60);						
			$sql2="SELECT id, submit_date FROM " . $wpdb->prefix . "contactform_submit where fk_form_id=".$submitdataformid;
			
			$getjoinerdata=$wpdb->get_results($sql2);

			$data="";
			foreach($getjoinerdata as $select12)
			{
				$fk_form_joiner_id=$select12->id;				
				$sql="SELECT fk_form_joiner_id FROM " . $wpdb->prefix . "contactform_submit_data where fk_form_joiner_id =". $fk_form_joiner_id." group by fk_form_joiner_id";
				
				
				$select_datas =  $wpdb->get_results($sql);
				$letters = array('-', '_');
				foreach ($select_datas as $select_data)
				{

				$sqldata='SELECT form_key,value FROM '. $wpdb->prefix.'contactform_submit_data where fk_form_joiner_id='.$select_data->fk_form_joiner_id .' ORDER BY id ASC';
				 
				  $getdatalist=$wpdb->get_results($sqldata);
				  $data.="#";	
				  $k=0;
				  $test = $formloopdata;
						foreach($getdatalist as $select)
						{
							
							if($this->satisfy_condition($getdatalist, $conditons)) {
							
								$pattern = "/\[" . $select->form_key . "]/ims";
								$test = preg_replace($pattern, $select->value, $test);

							} else { 
								$test = "";
								break; 
							}
							
						}
						$pattern = "/\[submit_id]/ims";
						$test = preg_replace($pattern, $fk_form_joiner_id, $test);
						$pattern = "/\[submit_date]/ims";
						$test = preg_replace($pattern, $select12->submit_date, $test);
						$tableformat .= $test;													
					}					

				 }	
							
				}			
				if ($formfooter<> '')
				{
					$tableformat.=$formfooter;
				}	
				echo $tableformat;
	}
	
	   function formloopdata_callback($matches) {
        if (isset($_POST[$matches[1]])) {
            $submitted = $_POST[$matches[1]];
            if (is_array($submitted))
                $submitted = join(', ', $submitted);
            return stripslashes($submitted);
        } else {
            return $matches[0];
        }
    }
	function form_avil_display_error($id) 
	{
		//Modified by hitesh : Check Date Availability
			global $wpdb, $user_ID;
										
			$sql="SELECT display_error FROM " . $wpdb->prefix . "contactform where form_id=".$id;		   
            $display_errors =  $wpdb->get_results($sql);
			foreach ($display_errors as $display_error) 
			{
				$form_error=$display_error->display_error;
			}
			if($form_error=="error")
				return true;
			else
				return false;
    }
	
	function mail($contact_form) {
		$contact_form = $this->upgrade_160($contact_form);
		$regex = '/\[\s*([a-zA-Z][0-9a-zA-Z:._-]*)\s*\]/';
        $callback = array(&$this, 'mail_callback');
		$mail_subject = preg_replace_callback($regex, $callback, $contact_form['mail']['subject']);
		$mail_sender = preg_replace_callback($regex, $callback, $contact_form['mail']['sender']);
		$mail_body = preg_replace_callback($regex, $callback, $contact_form['mail']['body']);
		$mail_recipient = preg_replace_callback($regex, $callback, $contact_form['mail']['recipient']);
				
		$mail_headers = "From: $mail_sender\n";
				
		if ($contact_form['mail']['mail_format'] == "html") 
		{
			$mail_headers .= 'MIME-Version: 1.0' . "\r\n";
			$mail_headers .= "Content-Type: text/html; charset=\"" . get_option('blog_charset') . "\"\n";
		}
		else 
		{
			$mail_headers .= "Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\"\n";
		}
		
		$attachments = array();
		if ($contact_form['mmf_mail_attachment'] == 1) {
			// get attachments
			foreach ($_POST as $key => $val) {
				$pos = strpos($key,'uploaded-file');
				if ($pos === false) {
					//
				} else {
					$attachments[] = dirname(__FILE__) . '/upload/temp/' . $val ;
				}
			}
		}

		if (@wp_mail($mail_recipient, $mail_subject, $mail_body, $mail_headers, $attachments)) {
			// Mail 2
			if ($contact_form['mail_2']['active']) {
				$mail_2_subject = preg_replace_callback($regex, $callback, $contact_form['mail_2']['subject']);
				$mail_2_sender = preg_replace_callback($regex, $callback, $contact_form['mail_2']['sender']);
				$mail_2_body = preg_replace_callback($regex, $callback, $contact_form['mail_2']['body']);
				$mail_2_recipient = preg_replace_callback($regex, $callback, $contact_form['mail_2']['recipient']);
				$mail_2_bcc = preg_replace_callback($regex, $callback, $contact_form['mail_2']['bcc']);
				$mail_2_headers = "From: $mail_2_sender\n"
					. "Bcc: $mail_2_bcc\n";
				if ($contact_form['mail']['mail_format'] == "html") 
				{
					$mail_2_headers .= 'MIME-Version: 1.0' . "\r\n";
					$mail_2_headers .= "Content-Type: text/html; charset=\"" . get_option('blog_charset') . "\"\n";
				}
				else 
				{
					$mail_2_headers .= "Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\"\n";
				}
				
				if ($contact_form['mmf_mail2_attachment'] != 1) {
					$attachments == "";
				}
				
				@wp_mail($mail_2_recipient, $mail_2_subject, $mail_2_body, $mail_2_headers,$attachments);
			}
			
			return true;
		} else {
			return false;
		}
	}
    
 	   function mail_callback($matches) {
        if (isset($_POST[$matches[1]])) {
            $submitted = $_POST[$matches[1]];
            if (is_array($submitted))
                $submitted = join(', ', $submitted);
            return stripslashes($submitted);
        } else {
            return $matches[0];
        }
    }

	function akismet($contact_form) {
		global $akismet_api_host, $akismet_api_port;
		
		if (! function_exists('akismet_http_post') || ! (get_option('wordpress_api_key') || $wpcom_api_key))
			return false;

		$akismet_ready = false;
		$author = $author_email = $author_url = $content = '';
		$fes = $this->form_elements($contact_form['form'], false);
		
		foreach ($fes as $fe) {
			if (! is_array($fe['options'])) continue;
			
			if (preg_grep('%^akismet:author$%', $fe['options']) && '' == $author) {
				$author = $_POST[$fe['name']];
				$akismet_ready = true;
			}
			if (preg_grep('%^akismet:author_email$%', $fe['options']) && '' == $author_email) {
				$author_email = $_POST[$fe['name']];
				$akismet_ready = true;
			}
			if (preg_grep('%^akismet:author_url$%', $fe['options']) && '' == $author_url) {
				$author_url = $_POST[$fe['name']];
				$akismet_ready = true;
			}
			
			if ('' != $content)
				$content .= "\n\n";
			$content .= $_POST[$fe['name']];
		}
		
		if (! $akismet_ready)
			return false;
		
		$c['blog'] = get_option('home');
		$c['user_ip'] = preg_replace('/[^0-9., ]/', '', $_SERVER['REMOTE_ADDR']);
		$c['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
		$c['referrer'] = $_SERVER['HTTP_REFERER'];
		$c['comment_type'] = 'contactform7';
		if ($permalink = get_permalink())
			$c['permalink'] = $permalink;
		if ('' != $author)
			$c['comment_author'] = $author;
		if ('' != $author_email)
			$c['comment_author_email'] = $author_email;
		if ('' != $author_url)
			$c['comment_author_url'] = $author_url;
		if ('' != $content)
			$c['comment_content'] = $content;
		
		$ignore = array('HTTP_COOKIE');
		
		foreach ($_SERVER as $key => $value)
			if (! in_array($key, $ignore))
				$c["$key"] = $value;
		
		$query_string = '';
		foreach ($c as $key => $data)
			$query_string .= $key . '=' . urlencode(stripslashes($data)) . '&';
		
		$response = akismet_http_post($query_string, $akismet_api_host, '/1.1/comment-check', $akismet_api_port);
		if ('true' == $response[1])
			return true;
		else
			return false;
	}

  	 function acceptance($contact_form) {
        $fes = $this->form_elements($contact_form['form'], false);
		
        $accepted = true;
        
		foreach ($fes as $fe) {
            if ('acceptance' != $fe['type'])
                continue;
            
            $invert = (bool) preg_grep('%^invert$%', $fe['options']);
            
            if ($invert && $_POST[$fe['name']] || ! $invert && ! $_POST[$fe['name']])
                $accepted = false;
        }
        
        return $accepted;
    }
	
	function get_contactform_option($option_name){
		global $wpdb;
		
		$row = $wpdb->get_row( "SELECT config_option_value FROM " . $wpdb->prefix . "contactform_config WHERE config_option = '$option_name' LIMIT 1" );
		$value = false;
		if ( is_object( $row) ) { // Has to be get_row instead of get_var because of funkiness with 0, false, null values
				$value = $row->config_option_value;
		}
		
		$value = untrailingslashit( $value );
		
		return maybe_unserialize( $value );
	}
	
	
	function update_contactform_option ($option_name, $newvalue, $action = 'update'){
		global $wpdb;
	
		wp_protect_special_option( $option_name );
	
		$safe_option_name = $wpdb->escape( $option_name );
		$newvalue = sanitize_option( $option_name, $newvalue );
	
		$oldvalue = $this->get_contactform_option($safe_option_name );
		
		$newvalue = maybe_serialize( $newvalue );
		if ($action == 'insert' ) {
			$wpdb->query( $wpdb->prepare( "INSERT INTO " . $wpdb->prefix . "contactform_config SET config_option_value = %s , config_option = %s", $newvalue, $option_name ) );
		} else {
			$wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "contactform_config SET config_option_value = %s WHERE config_option = %s", $newvalue, $option_name ) );
		}
	}
	
	
	function set_initial() {
		$mmf = get_option('mmf');
		if (! is_array($mmf))
			$mmf = array();

		$contact_forms = $mmf['contact_forms'];
		if (! is_array($contact_forms))
			$contact_forms = array();

		if (0 == count($contact_forms))
			$contact_forms[1] = $this->default_pack(__('Contact form', 'mmf') . ' 1');

		$mmf['contact_forms'] = $contact_forms;
		update_option('mmf', $mmf);        
	}

	function load_plugin_textdomain() { // l10n
		load_plugin_textdomain('mmf', 'wp-content/plugins/'.CONTACTFORM.'/languages');
	}

	function contact_forms() {
		if (is_array($this->contact_forms))
			return $this->contact_forms;
		$mmf = $this->get_contactform_option('mmf');
		$this->contact_forms = $mmf['contact_forms'];
		if (! is_array($this->contact_forms))
			$this->contact_forms = array();
		return $this->contact_forms;
	}

	function update_contact_forms($contact_forms) {

		$mmf = $this->get_contactform_option('mmf');
		$mmf['contact_forms'] = $contact_forms;
		$this->update_contactform_option('mmf', $mmf);
	}

	function upgrade_160($contact_form) {
		if (! isset($contact_form['mail']['recipient']))
			$contact_form['mail']['recipient'] = $contact_form['options']['recipient'];
		return $contact_form;
	}

/* Admin panel */

	function add_pages() {
	
		$base_url = get_option('siteurl') . '/wp-admin/options-general.php';
		$page = str_replace('\\', '%5C', plugin_basename(__FILE__));

		$contact_forms = $this->contact_forms();
		if (isset($_POST['mmf-save'])) {
			$id = $_POST['mmf-id'];
			check_admin_referer('mmf-save_' . $id);
			$title = trim($_POST['mmf-title']);	
			$mmf_maxsub=trim($_POST['mmf-maxsub']);
			
			$mmf_filetypes=trim($_POST['mmf-filetypes']);
			$mmf_maxfilesize=trim($_POST['mmf-maxfilesize']);
			
			
			$mmf_fromdate=trim($_POST['mmf-fromdate']);
			$mmf_todate=trim($_POST['mmf-todate']);
			$mmf_hide_form=trim($_POST['mmf-hide_form']);
			$mmf_url_success=trim($_POST['mmf-url_success']);
			$mmf_url_failure=trim($_POST['mmf-url_failure']);
			$mmf_list_fields=trim($_POST['mmf-list_fields']);
			$mmf_add_meta_author = trim($_POST['mmf-add-meta-author']);
			$mmf_add_meta_customfields = trim($_POST['mmf-add-meta-customfields']);
			$mmf_add_meta_loggeduser = trim($_POST['mmf-add-meta-loggeduser']);
			$mmf_form_data_css=trim($_POST['mmf-form_data_css']);			
			$set_user_list=$_POST['mmf-set_user_list'];	
			
			
			$mmf_form_header=$_POST['mmf-form_header'];			
			$mmf_form_loopdata=$_POST['mmf-form_loopdata'];	
			$mmf_form_footer=$_POST['mmf-form_footer'];	
			$mmf_form_conditions=$_POST['mmf-form_conditions'];	
			
			$mmf_failure_message=$_POST['failure_message'];	
			$mmf_success_message=$_POST['success_message'];	
			$mmf_required_message=$_POST['required_message'];	
			$mmf_display_message=$_POST['mmf-display_message'];			
			$mmf_mail_sent_ok=$_POST['mail_sent_ok'];
			$mmf_mail_sent_ng=$_POST['mail_sent_ng'];
			$mmf_validation_error=$_POST['validation_error'];
			$mmf_accept_terms_message=$_POST['accept_terms_message'];		
			$mmf_invalid_email=$_POST['invalid_email'];
			$mmf_captcha_not_match=$_POST['captcha_not_match'];
			$mmf_mail_over_limit=$_POST['mail_over_limit'];
			$mmf_success_mes=$_POST['success_mes'];
			$mmf_failure_mes=$_POST['failure_mes'];
			$mmf_mail_attachment = $_POST['mmf_mail_attachment'] == true ? 1 : 0 ;
			$mmf_mail2_attachment = $_POST['mmf_mail2_attachment'] == true ? 1 : 0 ;
			
			if($set_user_list <> "")
			{
				$mmf_set_user_list=$set_user_list[0];
				for($j=1;$j<count($set_user_list);$j++)
				{
					$mmf_set_user_list.=" ";
					$mmf_set_user_list.=$set_user_list[$j];
				}
			}
			else
			{
				$mmf_set_user_list.="";
			}
			
			/*display specific field submit form data*/
			$set_user_field=$_POST['mmf-set_user_field'];	
			if($set_user_field <> "")
			{
				$mmf_set_user_field=$set_user_field[0];
				for($j=1;$j<count($set_user_field);$j++)
				{
					$mmf_set_user_field.="^";
					$mmf_set_user_field.=$set_user_field[$j];
				}
			}
			else
			{
				$mmf_set_user_field.="";
			}
			
			$form = trim($_POST['mmf-form']);			
			$form_fields = trim(str_replace(" ","",$_POST['mmf-form_fields']));
			$csv_separator = trim($_POST['mmf-csv_separator']);
			$export_form_ids = trim($_POST['mmf-export_form_ids']);
			$all_form_fields = trim($_POST['mmf-all_form_fields']);
            $rss_feed = trim($_POST['mmf-rss_feed']);		

			$mail = array(
				'subject' => trim($_POST['mmf-mail-subject']),
				'sender' => trim($_POST['mmf-mail-sender']),
				'body' => trim($_POST['mmf-mail-body']),
				'recipient' => trim($_POST['mmf-mail-recipient']),
				'save_data' => trim($_POST['mmf-save_data']),
				'mmf_maxsub'=> trim($_POST['mmf-maxsub']),
				
				'mmf_filetypes'=> trim($_POST['mmf-filetypes']),
				'mmf_maxfilesize'=> trim($_POST['mmf-maxfilesize']),
				
				
				
				'mmf_fromdate'=> trim($_POST['mmf-fromdate']),
				'mmf_todate'=> trim($_POST['mmf-todate']),
				'mail_format' => trim($_POST['mmf-mail_format']),
				'display_error' => trim($_POST['mmf-display_error']),
				'mmf_hide_form' => trim($_POST['mmf-hide_form']),
				
				'mmf_set_user_list' =>$mmf_set_user_list,
				'mmf_form_header' =>$mmf_form_header,
				'mmf_form_loopdata' =>$mmf_form_loopdata,
				'mmf_form_footer' =>$mmf_form_footer,
				'mmf_form_conditions' =>$mmf_form_conditions,
				
				'mmf_set_user_field' =>$mmf_set_user_field,
				'mmf_url_success'=>trim($_POST['mmf-url_success']),
				'mmf_url_failure'=>trim($_POST['mmf-url_failure']),
				'mmf_list_fields'=>trim($_POST['mmf-list_fields']),
				'mmf_add_meta_author'=>trim($_POST['mmf-add-meta-author']),
				'mmf_add_meta_customfields'=>trim($_POST['mmf-add-meta-customfields']),
				'mmf_add_meta_loggeduser'=>trim($_POST['mmf-add-meta-loggeduser']),				
				'mmf_form_data_css'=>trim($_POST['mmf-form_data_css']),
				'mmf_failure_message'=>trim($_POST['failure_message']),
				'mmf_success_message'=>trim($_POST['success_message']),
				'mmf_required_message'=>trim($_POST['required_message']),
				'mmf_display_message'=>trim($_POST['mmf-display_message']),				
				'mmf_mail_sent_ok'=>trim($_POST['mail_sent_ok']),
				'mmf_mail_sent_ng'=>trim($_POST['mail_sent_ng']),
				'mmf_validation_error'=>trim($_POST['validation_error']),
				'mmf_accept_terms_message'=>trim($_POST['accept_terms_message']),
				'mmf_invalid_email'=>trim($_POST['invalid_email']),
				'mmf_captcha_not_match'=>trim($_POST['captcha_not_match']),
				'mmf_mail_over_limit'=>trim($_POST['mail_over_limit']),
				'mmf_success_mes'=>trim($_POST['success_mes']),
				'mmf_failure_mes'=>trim($_POST['failure_mes'])
				);
				
			$mail_2 = array(
				'active' => (1 == $_POST['mmf-mail-2-active']) ? true : false,
				'subject' => trim($_POST['mmf-mail-2-subject']),
				'sender' => trim($_POST['mmf-mail-2-sender']),
				'body' => trim($_POST['mmf-mail-2-body']),
				'recipient' => trim($_POST['mmf-mail-2-recipient']),
				'bcc'	=> trim($_POST['mmf-mail-2-bcc'])
				);
			$options = array(
				'recipient' => trim($_POST['mmf-options-recipient']) // For backward compatibility.
				);
			$read_flag = array(
				'read_flag' => 0);
			
			if (array_key_exists($id, $contact_forms)) {
				$contact_forms[$id] = compact('title', 'form', 'mail', 'mail_2', 'options','form_fields','csv_separator','export_form_ids', 'rss_feed', 'all_form_fields','mmf_maxsub','mmf_filetypes' ,'mmf_maxfilesize' ,'mmf_hide_form','mmf_fromdate','mmf_todate','mmf_set_user_list','mmf_form_header','mmf_form_loopdata','mmf_form_footer','mmf_form_conditions','mmf_set_user_field','mmf_url_success','mmf_url_failure','mmf_form_data_css','mmf_failure_message','mmf_success_message','mmf_required_message','mmf_display_message','mmf_mail_sent_ok','mmf_mail_sent_ng','mmf_validation_error','mmf_accept_terms_message','mmf_invalid_email','mmf_captcha_not_match','mmf_mail_over_limit','mmf_success_mes','mmf_failure_mes','mmf_list_fields','mmf_add_meta_author','mmf_add_meta_customfields','mmf_add_meta_loggeduser','mmf_mail_attachment','mmf_mail2_attachment'); 
				$redirect_to = $base_url . '?page=' . $page . '&contactform=' . $id . '&message=saved&tab=fo';
			} else {
				$key = (empty($contact_forms)) ? 1 : max(array_keys($contact_forms)) + 1;
				$contact_forms[$key] = compact('title', 'form', 'mail', 'mail_2', 'options','form_fields','csv_separator','export_form_ids', 'rss_feed', 'all_form_fields','mmf_maxsub' ,'mmf_filetypes' ,'mmf_maxfilesize',  'mmf_hide_form','mmf_fromdate','mmf_todate','mmf_set_user_list','mmf_set_user_field','mmf_form_header','mmf_form_loopdata','mmf_form_footer','mmf_form_conditions','mmf_url_success','mmf_url_failure','mmf_form_data_css','mmf_failure_message','mmf_success_message','mmf_required_message','mmf_display_message','mmf_mail_sent_ok','mmf_mail_sent_ng','mmf_validation_error','mmf_accept_terms_message','mmf_invalid_email','mmf_captcha_not_match','mmf_mail_over_limit','mmf_success_mes','mmf_failure_mes','mmf_list_fields','mmf_add_meta_author','mmf_add_meta_customfields','mmf_add_meta_loggeduser','mmf_mail_attachment','mmf_mail2_attachment'); 
				$redirect_to = $base_url . '?page=' . $page . '&contactform=' . $key . '&message=created&tab=fo';
			}
			$this->update_contact_forms($contact_forms);
			
			$form_id = ($id != -1) ? $id : $key;
			
			$contactform_values = array();
			$contactform_values['form_id'] = $form_id;
			$contactform_values['form_name'] = $title;
			
			$contactform_values['max_submissions'] = $mmf_maxsub;
			
			$contactform_values['uploadfiletypes'] = $mmf_filetypes;
			$contactform_values['maxuploadfilesize'] = $mmf_maxfilesize;
			
			
			$contactform_values['from_date'] = $mmf_fromdate;
			$contactform_values['to_date'] = $mmf_todate;
			$contactform_values['hide_form'] = $mmf_hide_form;		
			
			$contactform_values['url_success'] = $mmf_url_success;
			$contactform_values['url_failure'] = $mmf_url_failure;
			$contactform_values['csstext'] = $mmf_form_data_css;
			
			$contactform_values['to'] = $mail['recipient'];
			$contactform_values['cc'] = $mail_2['recipient'];
			$contactform_values['bcc'] = $mail_2['bcc'];
			$contactform_values['save_data'] = $mail['save_data'];
			$contactform_values['mail_format'] = $mail['mail_format'];
			
			
			
			$contactform_values['display_error'] = $mail['display_error'];			
			$contactform_values['allow_user'] = $mail['mmf_set_user_list'];
			$contactform_values['display_submitdata'] = $mail['mmf_set_user_field'];
						
			$contactform_values['form_fields'] = $form_fields;
		    	$csv_separator = $csv_separator == "" ? "," : $csv_separator;
			$contactform_values['csv_separator'] = $csv_separator;
			$contactform_values['export_form_ids'] = $export_form_ids;
            		$contactform_values['rss_feed'] = $rss_feed;
			$contactform_values['all_form_fields'] = $all_form_fields;	
			$contactform_values['formheader'] = stripslashes($mmf_form_header);	
			$contactform_values['formloopdata'] = stripslashes($mmf_form_loopdata);	
			$contactform_values['formfooter'] = stripslashes($mmf_form_footer);	
			$contactform_values['formconditions'] = stripslashes($mmf_form_conditions);	
			
			$contactform_values['failure_message'] = stripslashes($mmf_failure_message);	
			$contactform_values['success_message'] = stripslashes($mmf_success_message);	
			$contactform_values['required_message'] = stripslashes($mmf_required_message);	
			$contactform_values['display_message'] = stripslashes($mmf_display_message);			
			$contactform_values['mail_sent_ok'] = stripslashes($mmf_mail_sent_ok);
			$contactform_values['mail_sent_ng'] = stripslashes($mmf_mail_sent_ng);
			$contactform_values['validation_error'] = stripslashes($mmf_validation_error);			
			$contactform_values['accept_terms_message'] = stripslashes($mmf_accept_terms_message);
			$contactform_values['invalid_email'] = stripslashes($mmf_invalid_email);
			$contactform_values['captcha_not_match'] = stripslashes($mmf_captcha_not_match);			
			$contactform_values['mail_over_limit'] = stripslashes($mmf_mail_over_limit);
			$contactform_values['success_mes'] = stripslashes($mmf_success_mes);
			$contactform_values['failure_mes'] = stripslashes($mmf_failure_mes);
			$contactform_values['mail_attachment'] = $mmf_mail_attachment;
			$contactform_values['mail2_attachment'] = $mmf_mail2_attachment;

			$this->save_contactform($contactform_values,$id);
			wp_redirect($redirect_to);
			exit();
		} 
		elseif (isset($_POST['mmf-delete'])) {
			$id = $_POST['mmf-id'];
			check_admin_referer('mmf-delete_' . $id);
			
			unset($contact_forms[$id]);
			$this->update_contact_forms($contact_forms);
			
			$this->delete_contactform($id);
			
			wp_redirect($base_url . '?page=' . $page . '&message=deleted');
			exit();
		}elseif (isset($_POST['mmf-copy_from'])) {
			
			$copy_from = $_POST['mmf-copy_from'];
			
			$id = $_POST['mmf-id'];
			
			if (array_key_exists($id, $contact_forms)) {
				
				$contact_forms[$id] = $contact_forms[$copy_from];
				$contact_forms[$id]['title'] = "Copy of ".$contact_forms[$copy_from]['title'];
				$this->update_contact_forms($contact_forms);
				$this->manage_in_db($tobeupdated_form_id, $with_form_id, "update");
				// update ones setting with selected one settings
				$redirect_to = $base_url . '?page=' . $page . '&contactform=' . $id . '&message=updatedfrsetting&tab=fo';
			}else{
				// create new one from the selected setting
				$key = (empty($contact_forms)) ? 1 : max(array_keys($contact_forms)) + 1;
				
				$contact_forms[$key] = $contact_forms[$copy_from];
				$contact_forms[$key]['title'] = "Copy of ".$contact_forms[$copy_from]['title'];
				$this->update_contact_forms($contact_forms);
				$this->manage_in_db($tobeupdated_form_id, $with_form_id, "new");
				//echo "create new one with selected settings";
				$redirect_to = $base_url . '?page=' . $page . '&contactform=' . $key . '&message=createdfrsetting&tab=fo';	
			}	
			
			wp_redirect($redirect_to);
			exit();
			
		}

         add_options_page(__('MM Forms Community', 'mmf'), __('MM Forms Community', 'mmf'), 'manage_options', __FILE__, array(&$this, 'option_page'));
			
	}
	
	
	function manage_in_db($tobeupdated_form_id, $with_form_id, $type) {
		global $wpdb;
		
		$sql="SELECT * FROM " . $wpdb->prefix . "contactform where form_id =". $with_form_id;
		$select_datas =  $wpdb->get_results($sql);
		
		$contactform_values = array();
		
		if("new" == $type)
			$id = -1;
		else 	
			$id = tobeupdated_form_id;
			
		$contactform_values['form_id'] = $tobeupdated_form_id;
		
		$contactform_values['title'] = $select_datas[0]->title;		
		$contactform_values['max_submissions'] = $select_datas[0]->max_submissions;		
		$contactform_values['uploadfiletypes'] = $select_datas[0]->uploadfiletypes;
		$contactform_values['maxuploadfilesize'] = $select_datas[0]->maxuploadfilesize;
		$contactform_values['from_date'] = $select_datas[0]->from_date;
		$contactform_values['to_date'] = $select_datas[0]->to_date;
		$contactform_values['hide_form'] = $select_datas[0]->hide_form;
		$contactform_values['url_success'] = $select_datas[0]->url_success;
		$contactform_values['url_failure'] = $select_datas[0]->url_failure;
		$contactform_values['csstext'] = $select_datas[0]->csstext;
		$contactform_values['to'] = $select_datas[0]->to;
		$contactform_values['cc'] = $select_datas[0]->cc;
		$contactform_values['bcc'] = $select_datas[0]->bcc;
		$contactform_values['save_data'] = $select_datas[0]->save_data;
		$contactform_values['mail_format'] = $select_datas[0]->mail_format;
		$contactform_values['display_error'] = $select_datas[0]->display_error;
		$contactform_values['allow_user'] = $select_datas[0]->allow_user;
		$contactform_values['display_submitdata'] = $select_datas[0]->display_submitdata;		
		$contactform_values['form_fields'] = $select_datas[0]->form_fields;
		$contactform_values['csv_separator'] = $select_datas[0]->csv_separator;
		$contactform_values['export_form_ids'] = $select_datas[0]->export_form_ids;
		$contactform_values['rss_feed'] = $select_datas[0]->rss_feed;
		$contactform_values['all_form_fields'] = $select_datas[0]->all_form_fields;
		$contactform_values['formheader'] = $select_datas[0]->formheader;
		$contactform_values['formloopdata'] = $select_datas[0]->formloopdata;
		$contactform_values['formfooter'] = $select_datas[0]->formfooter;
		$contactform_values['formconditions'] = $select_datas[0]->formconditions;
		$contactform_values['failure_message'] = $select_datas[0]->failure_message;
		$contactform_values['success_message'] = $select_datas[0]->success_message;
		$contactform_values['required_message'] = $select_datas[0]->required_message;
		$contactform_values['display_message'] = $select_datas[0]->display_message;
		$contactform_values['mail_sent_ok'] = $select_datas[0]->mail_sent_ok;
		$contactform_values['mail_sent_ng'] = $select_datas[0]->mail_sent_ng;
		$contactform_values['validation_error'] = $select_datas[0]->validation_error;
		$contactform_values['accept_terms_message'] = $select_datas[0]->accept_terms_message;
		$contactform_values['invalid_email'] = $select_datas[0]->invalid_email;
		$contactform_values['captcha_not_match'] = $select_datas[0]->captcha_not_match;
		$contactform_values['mail_over_limit'] = $select_datas[0]->mail_over_limit;
		$contactform_values['success_mes'] = $select_datas[0]->success_mes;
		$contactform_values['failure_mes'] = $select_datas[0]->failure_mes;
		
		$this->save_contactform($contactform_values,$id);
	}
	
	
	function admin_head() {
		global $plugin_page;
		
		if (isset($plugin_page) && $plugin_page == plugin_basename(__FILE__)) {
			$admin_stylesheet_url = get_option('siteurl') . '/wp-content/plugins/'.CONTACTFORM.'/css/admin-stylesheet.css';
			echo '<link rel="stylesheet" href="' . $admin_stylesheet_url . '" type="text/css" />';						
			$admin_cal_url = get_option('siteurl') . '/wp-content/plugins/'.CONTACTFORM.'/includes/ui/';
			echo '<link rel="stylesheet" href="' . $admin_cal_url . 'ui.all.css" type="text/css" />';			
			
			$js_path = get_option('siteurl') . '/wp-content/plugins/'.CONTACTFORM.'/js/';
			$javascript_url = $js_path.'mmf-admin.js';
?>

<script type="text/javascript">

//<![CDATA[
var _mmf = {
	l10n: {
		optional: "<?php echo js_escape(__('optional', 'mmf')); ?>",
		generateTag: "<?php echo js_escape(__('Add form field', 'mmf')); ?>",
		textField: "<?php echo js_escape(__('Text field', 'mmf')); ?>",
		emailField: "<?php echo js_escape(__('Email field', 'mmf')); ?>",
		textArea: "<?php echo js_escape(__('Text area', 'mmf')); ?>",
		menu: "<?php echo js_escape(__('Drop-down menu', 'mmf')); ?>",
		checkboxes: "<?php echo js_escape(__('Checkboxes', 'mmf')); ?>",
       	dateField:"<?php echo js_escape(__('DateField', 'mmf')); ?>",
		fileField:"<?php echo js_escape(__('FileField', 'mmf')); ?>",
		radioButtons: "<?php echo js_escape(__('Radio buttons', 'mmf')); ?>",
		acceptance: "<?php echo js_escape(__('Acceptance', 'mmf')); ?>",
		isAcceptanceDefaultOn: "<?php echo js_escape(__("Make this checkbox checked by default?", 'mmf')); ?>",
		isAcceptanceInvert: "<?php echo js_escape(__("Make this checkbox work inversely?", 'mmf')); ?>",
		isAcceptanceInvertMeans: "<?php echo js_escape(__("* That means visitor who accepts the term unchecks it.", 'mmf')); ?>",
		captcha: "<?php echo js_escape(__('CAPTCHA', 'mmf')); ?>",
		submit: "<?php echo js_escape(__('Submit button', 'mmf')); ?>",
		tagName: "<?php echo js_escape(__('Name', 'mmf')); ?>",
		isRequiredField: "<?php echo js_escape(__('Required field?', 'mmf')); ?>",
		allowsMultipleSelections: "<?php echo js_escape(__('Allow multiple selections?', 'mmf')); ?>",
		insertFirstBlankOption: "<?php echo js_escape(__('Insert a blank item as the first option?', 'mmf')); ?>",
		makeCheckboxesExclusive: "<?php echo js_escape(__('Make checkboxes exclusive?', 'mmf')); ?>",
		menuChoices: "<?php echo js_escape(__('Choices', 'mmf')); ?>",
		label: "<?php echo js_escape(__('Label', 'mmf')); ?>",
		defaultValue: "<?php echo js_escape(__('Default value', 'mmf')); ?>",
		akismet: "<?php echo js_escape(__('Akismet', 'mmf')); ?>",
		akismetAuthor: "<?php echo js_escape(__("This field requires author's name", 'mmf')); ?>",
		akismetAuthorUrl: "<?php echo js_escape(__("This field requires author's URL", 'mmf')); ?>",
		akismetAuthorEmail: "<?php echo js_escape(__("This field requires author's email address", 'mmf')); ?>",
		generatedTag: "<?php echo js_escape(__('Press the button "Add To Form" to add the field to the form', 'mmf')); ?>",
		fgColor: "<?php echo js_escape(__("Foreground color", 'mmf')); ?>",
		bgColor: "<?php echo js_escape(__("Background color", 'mmf')); ?>",
		imageSize: "<?php echo js_escape(__("Image size", 'mmf')); ?>",
		imageSizeSmall: "<?php echo js_escape(__("Small", 'mmf')); ?>",
		imageSizeMedium: "<?php echo js_escape(__("Medium", 'mmf')); ?>",
		imageSizeLarge: "<?php echo js_escape(__("Large", 'mmf')); ?>",
		imageSettings: "<?php echo js_escape(__("Image settings", 'mmf')); ?>",
		inputFieldSettings: "<?php echo js_escape(__("Input field settings", 'mmf')); ?>",
		tagForImage: "<?php echo js_escape(__("For image", 'mmf')); ?>",
		tagForInputField: "<?php echo js_escape(__("For input field", 'mmf')); ?>",
		oneChoicePerLine: "<?php echo js_escape(__("* One choice per line.", 'mmf')); ?>"
	}
};
//]]>
</script>

	
<script type='text/javascript' src='<?php echo $javascript_url; ?>'></script>
<script type='text/javascript' src='<?php echo $js_path; ?>mmf-tabs.js'></script>
<script type='text/javascript' src='<?php echo $admin_cal_url; ?>jquery-1.3.2.min.js'></script>
<script type='text/javascript' src='<?php echo $admin_cal_url; ?>jquery-ui-1.7.2.custom.min.js'></script>

 
 <script type="text/javascript">
	$(function() {
		$("#datetopicker").datepicker();
	});
	$(function() {
		$("#datefrompicker").datepicker();
	});	
	$(function() {
		$("#datefrontpicker").datepicker();
	});
</script>
 
<?php
		}
	}
	
	function option_page() {
        global $wpdb,$includes, $user_ID;
		
		$base_url = get_option('siteurl') . '/wp-admin/options-general.php';
		$page = plugin_basename(__FILE__);
		$contact_forms = $this->contact_forms();

		$image_path = get_option('siteurl').'/wp-content/plugins/'.CONTACTFORM.'/images/';
		require_once $includes.'class_actions.php';		
		$id = $_POST['mmf-id'];
		$tab = $_GET['tab'];
		switch ($_GET['message']) {
			case 'created':
				$updated_message = __('Congratulations, you just created a new form.', 'mmf');
				break;
			case 'createdfrsetting':
				$updated_message = __('Form created with selected form settings.', 'mmf');
				break;
					
			case 'updatedfrsetting':
				$updated_message = __('Form updated with selected form settings.', 'mmf');
				break;
					
			case 'saved':
				$updated_message = __('Congratulations, your changes have been saved.', 'mmf');
				break;
			case 'deleted':
				$updated_message = __('Succesfully deleted the form.', 'mmf');
				break;
		}
		
		if ('new' == $_GET['contactform']) 
		{
			$unsaved = true;
			$current = -1;
			$cf = $this->default_pack(__('Untitled', 'mmf'), true);
			require_once $includes.'admin-panel.php';
		} elseif (array_key_exists($_GET['contactform'], $contact_forms)) {
			$current = (int) $_GET['contactform'];			
			$cf = stripslashes_deep($contact_forms[$current]);

			$cf = $this->upgrade_160($cf);
		}
		elseif ('uninstall' == $_GET['contactform']) {
			$this->contact_form_uninstall();
		}
		else
		{
			$action = $_GET['action'];
			
			$id = $_GET['id'];
			$form_id = $_GET['form_id'];
			
			$current_page = ($_GET['pg']) ? $_GET['pg'] : 1;
			
			if($_POST['records_per_page'])
			{
				$records_per_page = $_POST['records_per_page'];
				//$_SESSION['records_per_page'] = $records_per_page;
				//setcookie("records_per_page",$records_per_page);
			}
			else
			{
				$records_per_page = $_GET['rec_per_pg'];
			}
			$records_per_page = ($records_per_page) ? $records_per_page : 20;
			
			if($action == 'deleteform')
			{
				//updating the mmf option in options table
				unset($contact_forms[$id]);
				$this->update_contact_forms($contact_forms);				
			}
			
			//echo  "action ->".$action." contact_forms->". $contact_forms ."id -> ". $id." form_id ->" .$form_id.$current_page.$records_per_page;die();
           	$obj_actions = new actions( $action, $contact_forms , $id, $form_id,$current_page,$records_per_page);

			$obj_actions->url = $base_url;			
			
		}
		
		require_once $includes.'admin-panel.php';
	}

    function contact_form_uninstall(){

        if('uninstall' == strtolower($_POST['uninstall'])) {
            $this->mm_forms_uninstall();
            $plugin= CONTACTFORM .'/mm-forms.php';
			deactivate_plugins($plugin);
			update_option('recently_activated', array($plugin => time()) + (array)get_option('recently_activated'));
            echo "<div style='color:red; margin-left: 20px; margin-top: 40px;'> "._e('All the MM-Forms data have been successufully deleted.')."</div>";
        }else{
            echo "<div style='color:blue; margin-left: 20px; margin-top: 40px;'>"._e('Ooops! you did not type proper word.'). "<a style='color:red' href='javascript:history.back(-1)'>Back</a></div>";
        }
    }
	function default_pack($title, $initial = false) {
		$cf = array('title' => $title,			
			'form' => $this->default_form_template(),
			'mail' => $this->default_mail_template(),
			'mail_2' => $this->default_mail_2_template(),
			'options' => $this->default_options_template());
		if ($initial)
			$cf['initial'] = true;
		return $cf;
	}

	function default_form_template() {
		$template .= '<p><label>' . __('Your Name', 'mmf') . ' ' . __('(required)', 'mmf') . '<br />' . "\n";
		$template .= '    [text* your-name] </label></p>' . "\n\n";
		$template .= '<p><label>' . __('Your Email', 'mmf') . ' ' . __('(required)', 'mmf') . '<br />' . "\n";
		$template .= '    [email* your-email] </label></p>' . "\n\n";
		$template .= '<p><label>' . __('Subject', 'mmf') . '<br />' . "\n";
		$template .= '    [text your-subject] </label></p>' . "\n\n";
		$template .= '<p><label>' . __('Your Message', 'mmf') . '<br />' . "\n";
		$template .= '    [textarea your-message] </label></p>' . "\n\n";
		$template .= '<p>[submit "' . __('Send', 'mmf') . '"]</p>';
		return $template;
	}
	
	function default_mail_template() {
		$subject = '[your-subject]';
		$sender = '[your-name] <[your-email]>';
		$body = '[your-message]';
		$recipient = get_option('admin_email');
		return compact('subject', 'sender', 'body', 'recipient');
	}

	function default_mail_2_template() {
		$active = false;
		$subject = '[your-subject]';
		$sender = '[your-name] <[your-email]>';
		$body = '[your-message]';
		$recipient = '[your-email]';
		return compact('active', 'subject', 'sender', 'body', 'recipient');
	}

	function default_options_template() {
		$recipient = get_option('admin_email'); // For backward compatibility.
		return compact('recipient');
	}
	
	function message($status) {
		
		switch ($status) {
			case 'mail_sent_ok':
				$custom_flag=$this->check_custom_message("mail_sent_ok");				
				if($custom_flag=='false')
					return __('Your message was sent successfully. Thanks.', 'mmf');
				else
					return __($custom_flag, 'mmf');						
				
            case 'mail_data_saved':
				$custom_flag=$this->check_custom_message("successful");				
				if($custom_flag=='false')
					return __('Form data saved successfully. Thanks for submitting!', 'mmf');
				else
					return __($custom_flag, 'mmf');					
            case 'mail_data_not_saved':
				$custom_flag=$this->check_custom_message("failure");				
				if($custom_flag=='false')
					return __('Error occured in saving form data.', 'mmf');
				else
					return __($custom_flag, 'mmf');

			case 'mail_sent_ng':
				$custom_flag=$this->check_custom_message("mail_sent_ng");				
				if($custom_flag=='false')
					return __('Failed to send your message. Please try later or contact administrator by other way.', 'mmf');
				else
					return __($custom_flag, 'mmf');
				
			case 'validation_error':
				$custom_flag=$this->check_custom_message("validation_error");				
				if($custom_flag=='false')
					return __('Validation errors occurred. Please confirm the fields and submit it again.', 'mmf');
				else
					return __($custom_flag, 'mmf');
				
            case 'accept_terms':
				$custom_flag=$this->check_custom_message("accept_terms");				
				if($custom_flag=='false')
					 return __('Please accept the terms to proceed.', 'mmf');
				else
					return __($custom_flag, 'mmf');
               
			case 'invalid_email':
				$custom_flag=$this->check_custom_message("invalid_email");				
				if($custom_flag=='false')
					return __('Email address seems invalid.', 'mmf');
				else
					return __($custom_flag, 'mmf');
				
			case 'invalid_required':
				$custom_flag=$this->check_custom_message("required");				
				if($custom_flag=='false')
					return __('Please fill the required field.', 'mmf');
				else
					return __($custom_flag, 'mmf');					
			case 'captcha_not_match':
				$custom_flag=$this->check_custom_message("captcha_not_match");				
				if($custom_flag=='false')
					return __('Your entered code is incorrect.', 'mmf');
				else
					return __($custom_flag, 'mmf');
				
			case 'mail_over_limit':
				$custom_flag=$this->check_custom_message("mail_over_limit");				
				if($custom_flag=='false')
					return __('You reached your maximum limit','mmf');
				else
					return __($custom_flag, 'mmf');
				
			case 'success':
				$custom_flag=$this->check_custom_message("success_mes");				
				if($custom_flag=='false')
					return __('On success redirect this page','mmf');
				else
					return __($custom_flag, 'mmf');
				
			case 'failure':
				$custom_flag=$this->check_custom_message("failure_mes");				
				if($custom_flag=='false')
					return __('On failure redirect this page','mmf');
				else
					return __($custom_flag, 'mmf');
				
		}
	}
	
	function check_custom_message($dis_msg)
	{
		/* Modified by hitesh : check and display custom message and display it */
		global $wpdb, $user_ID;
		$message_status='';
		if (isset($_POST['_mmf']))		
			$id = (int) $_POST['_mmf'];			
			$sql="SELECT display_message, failure_message, success_message,required_message,mail_sent_ok,mail_sent_ng,validation_error,accept_terms_message,invalid_email,captcha_not_match,mail_over_limit,success_mes,failure_mes FROM " . $wpdb->prefix . "contactform where display_message=1 and form_id=".$id;
		$results =  $wpdb->get_results($sql);		
		if($results)
		{
			switch ($dis_msg) 
			{
				case 'required':
					foreach ($results as $result) 
					{
						$message_status  .= $result->required_message;
					}
					return $message_status;
				case 'successful':
					foreach ($results as $result) 
					{
						$message_status  .= $result->success_message;
					}
					return $message_status;
				case 'failure':
					foreach ($results as $result) 
					{
						$message_status  .= $result->failure_message;
					}
					return $message_status;
					
				case 'mail_sent_ok':
					foreach ($results as $result) 
					{
						$message_status  .= $result->mail_sent_ok;
					}
					return $message_status;
				case 'mail_sent_ng':
					foreach ($results as $result) 
					{
						$message_status  .= $result->mail_sent_ng;
					}
					return $message_status;
				case 'validation_error':
					foreach ($results as $result) 
					{
						$message_status  .= $result->validation_error;
					}
					return $message_status;
				case 'accept_terms_message':
					foreach ($results as $result) 
					{
						$message_status  .= $result->accept_terms_message;
					}
					return $message_status;
				case 'invalid_email':
					foreach ($results as $result) 
					{
						$message_status  .= $result->invalid_email;
					}
					return $message_status;
				case 'captcha_not_match':
					foreach ($results as $result) 
					{
						$message_status  .= $result->captcha_not_match;
					}
					return $message_status;
				case 'mail_over_limit':
					foreach ($results as $result) 
					{
						$message_status  .= $result->mail_over_limit;
					}
					return $message_status;
				case 'success_mes':
					foreach ($results as $result) 
					{
						$message_status  .= $result->success_mes;
					}
					return $message_status;
				case 'failure_mes':
					foreach ($results as $result) 
					{
						$message_status  .= $result->failure_mes;
					}
					return $message_status;					
			}
		}			
		else
		{
			return "false";
		}
	}
	
function process_nonajax_submitting() {
		if (! isset($_POST['_mmf']))
			return;
		$id = (int) $_POST['_mmf'];
		$contact_forms = $this->contact_forms();
       $cf = $contact_forms[$id];
		if ($cf) {
			$cf = stripslashes_deep($cf);
			$validation = $this->validate($cf);
			if (! $validation['valid']) {
				$_POST['_mmf_validation_errors'] = array('id' => $id, 'messages' => $validation['reason']);
			} elseif (! $this->acceptance($cf)) { // Not accepted terms
				$_POST['_mmf_mail_sent'] = array('id' => $id, 'ok' => false, 'message' => $this->message('accept_terms'));
			} elseif ($this->akismet($cf)) { // Spam!
				$_POST['_mmf_mail_sent'] = array('id' => $id, 'ok' => false, 'message' => $this->message('mail_sent_ng'), 'spam' => true);
			} elseif ($cf['mail']['save_data']== 2) { // Just save to database
			    if($this->submit_form($id)) {
				$_POST['_mmf_mail_sent'] = array('id' => $id, 'ok' => true, 'message' => $this->message('mail_data_saved'));
			    } else {
				$_POST['_mmf_mail_sent'] = array('id' => $id, 'ok' => false, 'message' => $this->message('mail_data_not_saved'));
			    }
			} elseif ($cf['mail']['save_data']== 1) { // Save to database and email
			    if ($this->mail($cf)) {
				$this->save_form_data($id,$_POST);
				$_POST['_mmf_mail_sent'] = array('id' => $id, 'ok' => true, 'message' => $this->message('mail_sent_ok'));
			    } else {
				$_POST['_mmf_mail_sent'] = array('id' => $id, 'ok' => false, 'message' => $this->message('mail_sent_ng'));
			    }
			} elseif ($cf['mail']['save_data']== 0) { // Just Email
			    if ($this->mail($cf)) {
				$_POST['_mmf_mail_sent'] = array('id' => $id, 'ok' => true, 'message' => $this->message('mail_sent_ok'));
			    } else {
				$_POST['_mmf_mail_sent'] = array('id' => $id, 'ok' => false, 'message' => $this->message('mail_sent_ng'));
			    }
			}
		}
	}

	function process_nonajax_submitting_old() {
		if (! isset($_POST['_mmf']))
			return;
		$id = (int) $_POST['_mmf'];
		$contact_forms = $this->contact_forms();
        $cf = $contact_forms[$id];
		if ($cf) {
			$cf = stripslashes_deep($cf);
			$validation = $this->validate($cf);
			if (! $validation['valid']) {
				$_POST['_mmf_validation_errors'] = array('id' => $id, 'messages' => $validation['reason']);
			} elseif (! $this->acceptance($cf)) { // Not accepted terms
				$_POST['_mmf_mail_sent'] = array('id' => $id, 'ok' => false, 'message' => $this->message('accept_terms'));
			} elseif ($this->akismet($cf)) { // Spam!
				$_POST['_mmf_mail_sent'] = array('id' => $id, 'ok' => false, 'message' => $this->message('mail_sent_ng'), 'spam' => true);
			} elseif ($this->mail($cf)) {
				$_POST['_mmf_mail_sent'] = array('id' => $id, 'ok' => true, 'message' => $this->message('mail_sent_ok'));
			} else {
				$_POST['_mmf_mail_sent'] = array('id' => $id, 'ok' => false, 'message' => $this->message('mail_sent_ng'));
			}
		}
	}

/* Post content filtering */

	var $processing_unit_tag;
	var $processing_within;
	var $unit_count;
	var $widget_count;
	
	function the_content_filter($content) {		
		$this->processing_within = 'p' . get_the_ID();
		$this->unit_count = 0;
					
		$checkformdata=explode(' ',$content);
		$submitdataformid=$checkformdata[1];
		$checkformdata=explode('[',$checkformdata[0]);
		
				
		if(strpos($content, '[formdata ') !== false)
		//if($checkformdata[1]=='formdata')
		{
			$data = explode('[formdata ', $content);
			$submitdataformid = explode(" ", $data[1]);
			$submitdataformid = $submitdataformid[0];			
			//Display form submited data
			global $wpdb;
			$condition = explode('WHERE', $content);
			if(strlen($condition[1]) != 0) {
				$condition = substr($condition[1], 0, strlen($condition[1]) - 1);
			} else {
				$condition = "";
			}
			
			$sql = "SELECT csstext FROM " . $wpdb->prefix. "contactform where form_id=".$submitdataformid;
            $results =  $wpdb->get_results($sql);
			if($results==""){return 'Error in css';	break;}
			foreach ($results as $result) 
			{			
				echo "<style>".$result->csstext."</style>";
			}			
			return $this->displaysubmitdata($submitdataformid, $condition);
		}
		
		$regex = '/\[\s*form\s+(\d+)(?:\s+.*?)?\s*\]/';
		return preg_replace_callback($regex, array(&$this, 'the_content_filter_callback'), $content);
		
		$this->processing_within = null;
	}
	
	function widget_text_filter($content) {
		$this->widget_count += 1;
		$this->processing_within = 'w' . $this->widget_count;
		$this->unit_count = 0;

		$regex = '/\[\s*form\s+(\d+)(?:\s+.*?)?\s*\]/';
		return preg_replace_callback($regex, array(&$this, 'the_content_filter_callback'), $content);
		
		$this->processing_within = null;
	}
	
	function the_content_filter_callback($matches) {
		global $wp_query;

		$contact_forms = $this->contact_forms();
		$id = (int) $matches[1];
		$this->set_formid($id);
		if (! ($cf = $contact_forms[$id])) return $matches[0];		
		$cf = stripslashes_deep($cf);
		$this->unit_count += 1;
		$unit_tag = 'mmf-f' . $id . '-' . $this->processing_within . '-o' . $this->unit_count;
		$this->processing_unit_tag = $unit_tag;
		$form = '<div class="mmf" id="' . $unit_tag . '">';
		$url = parse_url($_SERVER['REQUEST_URI']);
		
		if (empty($url['query'])) {
			if ( get_option('permalink_structure') != '' )  {
				$url = $url['path'];				
			} else {
				$url = $url['path'] . '?p=' . $wp_query->post->ID . '#' . $unit_tag;
			}
		} else {
			$url = $url['path'] . '?' . $url['query'] . '#' . $unit_tag;
		}
		//////////////	

		//print_r($cf['form']);		
//		if($this->checkdateavailability($id) && $this->checkUserAuthentication($id))
		if($this->checkdateavailability($id))
		{		
			$form .= '<form action="' . $url . '#outcome_msg" method="post" class="mmf-form" enctype="multipart/form-data">';
			$form .= '<input type="hidden" name="_mmf" value="' . $id . '" />';
			$form .= '<input type="hidden" name="_mmf_success_url" value="'.$this->getsuccessurl($id).'" />';		
		
			//$form .='<input type="hidden" name="_mmf_is_ajax_call" value="1"  />';
			$form .= '<input type="hidden" name="_mmf_failure_url" value="'.$this->getfailureurl($id).'" />';
			$form .= '<input type="hidden" name="_mmf_unit_tag" value="' . $unit_tag . '" />';
		
			$form .= '<input type="hidden" name="page_post_id" value="' . $wp_query->post->ID . '" />';
			$form .= '<input type="hidden" name="page_post_title" value="' . $wp_query->post->post_title . '" />';
		
		
			/* Add post meta */
			$curuser = get_userdata($wp_query->post->post_author);
			if ($cf['mail']['mmf_add_meta_author'] == 1) {
				$form .= '<input type="hidden" name="meta_post_author_id" value="' . $wp_query->post->post_author . '" />' ;
				$form .= '<input type="hidden" name="meta_post_author_name" value="' . $curuser->display_name . '" />' ;
				$form .= '<input type="hidden" name="meta_post_author_email" value="' . $curuser->user_email . '" />' ;
				$form .= '<input type="hidden" name="meta_post_author_url" value="' . $curuser->user_url . '" />' ;
			}
		
			if ($cf['mail']['mmf_add_meta_customfields'] == 1) {
				$customfields = get_post_custom($wp_query->post->ID);
			
				//$my_custom_field = $custom_fields['my_custom_field'];
				foreach ( $customfields as $cfkey => $cfvalue ) {
					if (substr($cfkey,0,1) != "_") {
						$my_custom_field = $customfields[$cfkey];
						foreach ( $my_custom_field as $key => $value ) {
							$form .= '<input type="hidden" name="customfield_' . $cfkey . '" value="' . $value . '" />' ;
						}
					}
				}
			
			}
			if ($cf['mail']['mmf_add_meta_loggeduser'] ==1) {
				global $current_user;
				get_currentuserinfo();
		
				$form .= '<input type="hidden" name="loggedin_user_email" value="' . $current_user->user_email . '" />';
				$form .= '<input type="hidden" name="loggedin_user_name" value="' . $current_user->display_name . '" />';
			}

			$form .= $this->form_elements($cf['form']);		
		
			$form .= '</form>';
		}
		else
		{	
			if($this->form_avil_display_error($id))	
				$form .="Form is not Available";
		}
		
		$class = 'mmf-response-output';
		
		if ($this->processing_unit_tag == $_POST['_mmf_unit_tag']) {
			if (isset($_POST['_mmf_mail_sent']) && $_POST['_mmf_mail_sent']['id'] == $id) {
				if ($_POST['_mmf_mail_sent']['ok']) {
					$class .= ' mmf-mail-sent-ok';
					$content = $_POST['_mmf_mail_sent']['message'];
				} else {
					$class .= ' mmf-mail-sent-ng';
					if ($_POST['_mmf_mail_sent']['spam'])
						$class .= ' mmf-spam-blocked';
					$content = $_POST['_mmf_mail_sent']['message'];
				}
			} elseif (isset($_POST['_mmf_validation_errors']) && $_POST['_mmf_validation_errors']['id'] == $id) {
				$class .= ' mmf-validation-errors';
				$content = $this->message('validation_error');
			}
		}
		
		$class = ' class="' . $class . '"';
		
		$form .= '<a name="outcome_msg"></a><div' . $class . '>' . $content . '</div>';
		
		$form .= '</div>';
		
		$this->processing_unit_tag = null;
		return $form;
	}

	function validate($contact_form) {
		$fes = $this->form_elements($contact_form['form'], false);
		$valid = true;
		$reason = array();

		foreach ($fes as $fe) {
			$type = $fe['type'];
			$name = $fe['name'];
            $values = $fe['values'];
            
            // Before validation corrections
            if (preg_match('/^(?:text|email)[*]?$/', $type))
                $_POST[$name] = trim(strtr($_POST[$name], "\n", " "));
            
			if (preg_match('/^(?:select|checkbox|radio)[*]?$/', $type)) {
                if (is_array($_POST[$name])) {
                    foreach ($_POST[$name] as $key => $value) {
                        if (! in_array($value, $values)) // Not in given choices.
                            unset($_POST[$name][$key]);
                    }
                } else {
                    if (! in_array($_POST[$name], $values)) //  Not in given choices.
                        $_POST[$name] = '';
                }
            }
            
            if ('acceptance' == $type)
                $_POST[$name] = $_POST[$name] ? 1 : 0;
            
			// Required item (*)
			if (preg_match('/^(?:text|textarea|checkbox)[*]$/', $type)) {
				if (empty($_POST[$name])) {
					$valid = false;
					$reason[$name] = $this->message('invalid_required');
				}
			}
            
            if ('select*' == $type) {
                if (empty($_POST[$name]) ||
                        ! is_array($_POST[$name]) && '---' == $_POST[$name] ||
                        is_array($_POST[$name]) && 1 == count($_POST[$name]) && '---' == $_POST[$name][0]) {
                    $valid = false;
					$reason[$name] = $this->message('invalid_required');
                }
			}

			if (preg_match('/^email[*]?$/', $type)) {
				if ('*' == substr($type, -1) && empty($_POST[$name])) {
					$valid = false;
					$reason[$name] = $this->message('invalid_required');
				} elseif (! empty($_POST[$name]) && ! is_email($_POST[$name])) {
					$valid = false;
					$reason[$name] = $this->message('invalid_email');
				}
			}

			if (preg_match('/^captchar$/', $type)) {
				$captchac = '_mmf_captcha_challenge_' . $name;
				if (! $this->check_captcha($_POST[$captchac], $_POST[$name])) {
					$valid = false;
					$reason[$name] = $this->message('captcha_not_match');
				}
				$this->remove_captcha($_POST[$captchac]);
			}
		}
		return compact('valid', 'reason');
	}

	function refill_captcha($contact_form) {
		$fes = $this->form_elements($contact_form['form'], false);
		$refill = array();
		
		foreach ($fes as $fe) {
			$type = $fe['type'];
			$name = $fe['name'];
			$options = $fe['options'];
			if ('captchac' == $type) {
				$op = $this->captchac_options($options);
				if ($filename = $this->generate_captcha($op))
					$captcha_url = get_option('siteurl') . '/wp-content/plugins/'.CONTACTFORM.'/captcha/tmp/' . $filename;
					$refill[$name] = $captcha_url;
			}
		}
		return $refill;
	}

	function redirect () {
				header('Location: http://www.google.com/');		
	}

	function wp_head() 
	{

		$stylesheet_url = get_option('siteurl') . '/wp-content/plugins/'.CONTACTFORM.'/css/stylesheet.css';
		$calendar_css = get_option('siteurl') . '/wp-content/plugins/'.CONTACTFORM.'/css/calendar.css';
		
		echo '<link rel="stylesheet" href="' . $stylesheet_url . '" type="text/css" />'."\n";
		echo '<link rel="stylesheet" href="' . $calendar_css . '" type="text/css" />'."\n";

	}
			
	function load_js() {
		global $pagenow;
        if (is_admin() && 'options-general.php' == $pagenow && false !== strpos($_GET['page'], 'mm-forms'))
			wp_enqueue_script('jquery');
		if (! is_admin()) {
			$jquery_form_script = get_option('siteurl') . '/wp-content/plugins/'.CONTACTFORM.'/js/jquery.form.js';
			wp_enqueue_script('jquery.form.js', $jquery_form_script, array('jquery'), '');

			$javascript_url = get_option('siteurl') . '/wp-content/plugins/'.CONTACTFORM.'/js/mm-forms.js';
			$javascript_url_ajax = get_option('siteurl') . '/wp-content/plugins/'.CONTACTFORM.'/js/ajaxfileupload.js';

			$calendar_js = get_option('siteurl') . '/wp-content/plugins/'.CONTACTFORM.'/js/calendar.js';

			wp_enqueue_script('mm-forms.js',$javascript_url,array('jquery'),'');
			wp_enqueue_script('ajaxfileupload.js',$javascript_url_ajax,array('jquery'),'');
			wp_enqueue_script('calendar.js',$calendar_js,array('jquery'),'');				
		}
	}

/* Processing form element placeholders */

	function form_elements($form, $replace = true) {
		$types = 'text[*]?|email[*]?|textarea[*]?|select[*]?|checkbox[*]?|radio|acceptance|captchac|captchar|datefield|fileField';
		$regex = '%\[\s*(' . $types . ')(\s+[a-zA-Z][0-9a-zA-Z:._-]*)([-0-9a-zA-Z:#_/\s]*)?((?:\s*(?:"[^"]*"|\'[^\']*\'))*)?\s*\]%';
		$submit_regex = '/\[\s*submit(\s+(?:"[^"]*"|\'[^\']*\'))?\s*\]/';		
		if ($replace) {
			// print_r($form);
			$form = preg_replace_callback($regex, array(&$this, 'form_element_replace_callback'), $form);
			// Submit button
			$form = preg_replace_callback($submit_regex, array(&$this, 'submit_replace_callback'), $form);			
			return $form;
		} else {
			$results = array();
			preg_match_all($regex, $form, $matches, PREG_SET_ORDER);
			foreach ($matches as $match) {
				$results[] = (array) $this->form_element_parse($match);
			}
			return $results;
		}
	}
	
	function form_display_header($form) {
		global $wpdb;
		$tableformat="";	
		$sql="SELECT formheader FROM " . $wpdb->prefix . "contactform where form_id =". $form;
		$select_datas =  $wpdb->get_results($sql);
			if ($select_datas[0]->formheader== '')
			{
				$tableformat.='<table>'.chr(13);
				$tableformat.='<tr>'.chr(13).'<th>Name</th>'.chr(13).'<th>E-mail</th>'.chr(13).'<th>Subject</th><th>Message</th>'.chr(13).'</tr>'.chr(13);		
			}
			else
			{
				$tableformat.=$select_datas[0]->formheader;
			}
		return $tableformat;			
		}
	
	function form_display_loopdata($form) {
		global $wpdb;
		$tableformat="";	
		$sql="SELECT id FROM " . $wpdb->prefix . "contactform_submit where fk_form_id =". $form. " group by fk_form_id" ;
		$select_datas =  $wpdb->get_results($sql);
		if($select_datas[0]=='')
		{
			return str_replace('<br />','',nl2br("<tr>\n<td>[your-name]</td>\n<td>[your-email]</td>\n<td>[your-subject]</td>\n<td>[your-message]</td>\n</tr>"));
		}
		else
		{	
			$sql="SELECT formloopdata FROM " . $wpdb->prefix . "contactform where form_id =". $form;
			$formloopdata =  $wpdb->get_results($sql);
			if ($formloopdata[0]->formloopdata <> '')
			{
				$tableformat.=$formloopdata[0]->formloopdata;		
			}
			else
			{
				$letters = array('-', '_');
				foreach ($select_datas as $select_data)
				{
				   $sql1="SELECT * FROM " . $wpdb->prefix . "contactform_submit_data where fk_form_joiner_id=".$select_data->id." and form_key != 'page_post_title' and form_key != 'user_ID' and form_key != 'page_post_id' ORDER BY `" . $wpdb->prefix . "contactform_submit_data`.`id` ASC ";		   
				   
				   
				   $getdata=$wpdb->get_results($sql1);
				   
				   $data.="#";
				   
				   $tableformat.='<tr>'.chr(13);
					foreach($getdata as $select)
					{
						$sql2="SELECT display_submitdata FROM " . $wpdb->prefix . "contactform where form_id=".$form;
						$getdatastr=$wpdb->get_results($sql2);
						foreach($getdatastr as $select12)
						{
							$datastr=$select12->display_submitdata;
						}
						$form_key=explode("^",$datastr);
						$tableformat.='<td>['.$select->form_key.']</td>'.chr(13);
					}
				}
				$tableformat.='</tr>';
			}
			return $tableformat;
		}		
	}
	function form_display_footer($form) 
	{
		global $wpdb;
		$tableformat="";
		$sql="SELECT formfooter FROM " . $wpdb->prefix . "contactform where form_id =". $form;
		$select_datas =  $wpdb->get_results($sql);
			if ($select_datas[0]->formfooter<>'')
			{
				$tableformat.=$select_datas[0]->formfooter;
			}
			else
			{
				$sql="SELECT id FROM " . $wpdb->prefix . "contactform_submit where fk_form_id =". $form. " group by fk_form_id" ;
				$select_datas =  $wpdb->get_results($sql);
				$tableformat.='</table>'.chr(13);
			}
			return $tableformat;
	}
	function form_display_conditions($form) 
	{
		global $wpdb;
		$tableformat = "";
		$sql = "SELECT formconditions FROM " . $wpdb->prefix . "contactform where form_id =". $form;
		$select_datas =  $wpdb->get_results($sql);
		
		if ($select_datas[0]->formconditions <> '')	{
				$tableformat .= $select_datas[0]->formconditions;
		}
		
		return $tableformat;
	}
	
	var $__form_id = 0;
	function set_formid($form_id){
			
			$this->__form_id =  $form_id;
			
	}
	
	function get_formid(){
	
		return $this->__form_id ;
	}
	function get_uploadfiletypes($form_id){
		
			global $wpdb, $user_ID;
			$sql="SELECT uploadfiletypes FROM " . $wpdb->prefix . "contactform where form_id=".$form_id;
			$select =  $wpdb->get_results($sql);
			foreach ($select as $se)
			{ 
				$uploadfiletypes = $se->uploadfiletypes;
			}			
			return $uploadfiletypes;
	}
	
	function get_maxfilesize($form_id){
		
			global $wpdb, $user_ID;
			$sql="SELECT maxuploadfilesize FROM " . $wpdb->prefix . "contactform where form_id=".$form_id;
            $select_maxuploadfilesize =  $wpdb->get_results($sql);
			foreach ($select_maxuploadfilesize as $select)
			{ 
				$maxuploadfilesize=$select->maxuploadfilesize;
			}			
			return $maxuploadfilesize;
	}
	
	function form_element_replace_callback($matches) {
			
		extract((array) $this->form_element_parse($matches)); // $type, $name, $options, $values
		
		if ($this->processing_unit_tag == $_POST['_mmf_unit_tag']) {
			$validation_error = $_POST['_mmf_validation_errors']['messages'][$name];
			$validation_error = $validation_error ? '<span class="mmf-not-valid-tip-no-ajax">' . $validation_error . '</span>' : '';
		} else {
			$validation_error = '';
		}
		
		$atts = '';
        $options = (array) $options;
        
        $id_array = preg_grep('%^id:[-0-9a-zA-Z_]+$%', $options);
        if ($id = array_shift($id_array)) {
            preg_match('%^id:([-0-9a-zA-Z_]+)$%', $id, $id_matches);
            if ($id = $id_matches[1])
                $atts .= ' id="' . $id . '"';
        }
        
        $class_att = "";
        $class_array = preg_grep('%^class:[-0-9a-zA-Z_]+$%', $options);
        foreach ($class_array as $class) {
            preg_match('%^class:([-0-9a-zA-Z_]+)$%', $class, $class_matches);
            if ($class = $class_matches[1])
                $class_att .= ' ' . $class;
        }
        
        if (preg_match('/^email[*]?$/', $type))
            $class_att .= ' mmf-validates-as-email';
        if (preg_match('/[*]$/', $type))
            $class_att .= ' mmf-validates-as-required';
        
        if (preg_match('/^checkbox[*]?$/', $type))
            $class_att .= ' mmf-checkbox';
        
        if ('radio' == $type)
            $class_att .= ' mmf-radio';
        
        if (preg_match('/^captchac$/', $type))
            $class_att .= ' mmf-captcha-' . $name;
        
        if ('acceptance' == $type) {
            $class_att .= ' mmf-acceptance';
            if (preg_grep('%^invert$%', $options))
                $class_att .= ' mmf-invert';
        }
        
        if ($class_att)
            $atts .= ' class="' . trim($class_att) . '"';
		
		// Value.
		if ($this->processing_unit_tag == $_POST['_mmf_unit_tag']) {
			if (isset($_POST['_mmf_mail_sent']) && $_POST['_mmf_mail_sent']['ok'])
				$value = '';
			elseif ('captchar' == $type)
				$value = '';
			else
				$value = $_POST[$name];
		} else {
			$value = $values[0];
		}
        
        // Default selected/checked for select/checkbox/radio
        if (preg_match('/^(?:select|checkbox|radio)[*]?$/', $type)) {
            $scr_defaults = array_values(preg_grep('/^default:/', $options));
            preg_match('/^default:([0-9_]+)$/', $scr_defaults[0], $scr_default_matches);
            $scr_default = explode('_', $scr_default_matches[1]);
        }
		
		//print_r($type);
		
		switch ($type) {
			case 'text':
			case 'text*':
			case 'email':
			case 'email*':
			case 'captchar':		
				if (is_array($options)) {
					$size_maxlength_array = preg_grep('%^[0-9]*[/x][0-9]*$%', $options);
					if ($size_maxlength = array_shift($size_maxlength_array)) {
						preg_match('%^([0-9]*)[/x]([0-9]*)$%', $size_maxlength, $sm_matches);
						if ($size = (int) $sm_matches[1])
							$atts .= ' size="' . $size . '"';
                        else
                            $atts .= ' size="40"';
						if ($maxlength = (int) $sm_matches[2])
							$atts .= ' maxlength="' . $maxlength . '"';
					} else {
                        $atts .= ' size="40"';
                    }
				}
				$html = '&nbsp;<input type="text"  name="' . $name . '" value="' . attribute_escape($value) . '"' . $atts . ' />';
				$html = '<span class="mmf-form-control-wrap ' . $name . '">' . $html . $validation_error . '</span>';
				return $html;
				break;
			
			
			case 'fileField':
				if (is_array($options)) {
					$size_maxlength_array = preg_grep('%^[0-9]*[/x][0-9]*$%', $options);
					if ($size_maxlength = array_shift($size_maxlength_array)) {
						preg_match('%^([0-9]*)[/x]([0-9]*)$%', $size_maxlength, $sm_matches);
						if ($size = (int) $sm_matches[1])
							$atts .= ' size="' . $size . '"';
                        else
                            $atts .= ' size="40"';
						if ($maxlength = (int) $sm_matches[2])
							$atts .= ' maxlength="' . $maxlength . '"';
					} else {
                        $atts .= ' size="40"';
                    }
				}
				
				$fid = $this->get_formid();
				
				$html .= '<input type="hidden" id="uploaded-file-'.$name.'" name="uploaded-file-'.$name.'" value="none" />';
				
				$html .= '
				
				<img  id="thumb_image-'.$name.'" src="'.get_option("siteurl") . "/wp-content/plugins/'.CONTACTFORM.'/images/transparent.gif".'" />
				<iframe width="0" height="0" style="visibility: hidden">
						 </iframe> <br> <iframe id="bfile" name="bfile" src="'.get_option('siteurl') . '/wp-content/plugins/'.CONTACTFORM.'/includes/ajaxfileupload.php?ufiletypes='.$this->get_uploadfiletypes($fid).'&maxfilesize='.$this->get_maxfilesize($fid).'&fieldname='.$name.'" scrolling="no" frameborder="0" width="400" height="55"></iframe>';
				
				
				return $html;
				break;
			

			case 'datefield':					
				$html .= '&nbsp;<input type="text" id="' . $name . '" name="' . $name . '"  value="' . attribute_escape($value) . '"' . $atts . ' length="10" />';
				$calendar_icon = get_option("siteurl") . "/wp-content/plugins".CONTACTFORM."/images/calendar.png";
				$html .= '<img src="' . $calendar_icon .'" class="calendarButton" onClick="displayDatePicker(\'' . $name . '\');" onMouseOver="this.style.cursor=\'pointer\'" /> ';//parameterpass
				$html = '<span class="mmf-form-control-wrap ' . $name . '">' . $html . $validation_error . '</span>';
				return $html;
				break;
				
			case 'textarea':
			case 'textarea*':
				if (is_array($options)) {
					$cols_rows_array = preg_grep('%^[0-9]*[x/][0-9]*$%', $options);
					if ($cols_rows = array_shift($cols_rows_array)) {
						preg_match('%^([0-9]*)[x/]([0-9]*)$%', $cols_rows, $cr_matches);
						if ($cols = (int) $cr_matches[1])
							$atts .= ' cols="' . $cols . '"';
                        else
                            $atts .= ' cols="40"';
						if ($rows = (int) $cr_matches[2])
							$atts .= ' rows="' . $rows . '"';
                        else
                            $atts .= ' rows="10"';
					} else {
                        $atts .= ' cols="40" rows="10"';
                    }
				}
				$html = '<textarea name="' . $name . '"' . $atts . '>' . $value . '</textarea>';
				$html = '<span class="mmf-form-control-wrap ' . $name . '">' . $html . $validation_error . '</span>';
				return $html;
				break;
			case 'select':
			case 'select*':
                $multiple = (preg_grep('%^multiple$%', $options)) ? true : false;
                $include_blank = preg_grep('%^include_blank$%', $options);
                
				if ($empty_select = empty($values) || $include_blank)
					array_unshift($values, '---');
                
				$html = '';
                foreach ($values as $key => $value) {
                    $selected = '';
                    if (! $empty_select && in_array($key + 1, $scr_default))
                        $selected = ' selected="selected"';
                    if ($this->processing_unit_tag == $_POST['_mmf_unit_tag'] && (
                            $multiple && in_array($value, $_POST[$name]) ||
                            ! $multiple && $_POST[$name] == $value))
                        $selected = ' selected="selected"';
					$html .= '<option value="' . attribute_escape($value) . '"' . $selected . '>' . $value . '</option>';
                }
                
                if ($multiple)
                    $atts .= ' multiple="multiple"';
                
				$html = '<select name="' . $name . ($multiple ? '[]' : '') . '"' . $atts . '>' . $html . '</select>';
				$html = '<span class="mmf-form-control-wrap ' . $name . '">' . $html . $validation_error . '</span>';
				return $html;
				break;
            case 'checkbox':
            case 'checkbox*':
            case 'radio':
                $multiple = (preg_match('/^checkbox[*]?$/', $type) && ! preg_grep('%^exclusive$%', $options)) ? true : false;
                $html = '';
                
                if (preg_match('/^checkbox[*]?$/', $type) && ! $multiple)
                    $onclick = ' onclick="mmfExclusiveCheckbox(this);"';
                
                $input_type = rtrim($type, '*');
                
                foreach ($values as $key => $value) {
                    $checked = '';
                    if (in_array($key + 1, $scr_default))
                        $checked = ' checked="checked"';
                    if ($this->processing_unit_tag == $_POST['_mmf_unit_tag'] && (
                            $multiple && in_array($value, $_POST[$name]) ||
                            ! $multiple && $_POST[$name] == $value))
                        $checked = ' checked="checked"';
                    if (preg_grep('%^label[_-]?first$%', $options)) { // put label first, input last
                        $item = '<span class="mmf-list-item-label">' . $value . '</span>&nbsp;';
                        $item .= '<input type="' . $input_type . '" name="' . $name . ($multiple ? '[]' : '') . '" value="' . attribute_escape($value) . '"' . $checked . $onclick . ' />';
                    } else {
                        $item = '<input type="' . $input_type . '" name="' . $name . ($multiple ? '[]' : '') . '" value="' . attribute_escape($value) . '"' . $checked . $onclick . ' />';
                        $item .= '&nbsp;<span class="mmf-list-item-label">' . $value . '</span>';
                    }
                    $item = '<span class="mmf-list-item">' . $item . '</span>';
                    $html .= $item;
                }
                
                $html = '<span' . $atts . '>' . $html . '</span>';
				$html = '<span class="mmf-form-control-wrap ' . $name . '">' . $html . $validation_error . '</span>';
				return $html;
				break;
            case 'acceptance':
                $invert = (bool) preg_grep('%^invert$%', $options);
                $default = (bool) preg_grep('%^default:on$%', $options);
                
                $onclick = ' onclick="mmfToggleSubmit(this.form);"';
                $checked = $default ? ' checked="checked"' : '';
                $html = '<input type="checkbox" name="' . $name . '" value="1"' . $atts . $onclick . $checked . ' />';
                return $html;
                break;
			case 'captchac':
				$op = array();
				// Default
				$op['img_size'] = array(72, 24);
				$op['base'] = array(6, 18);
				$op['font_size'] = 14;
				$op['font_char_width'] = 15;
				
				$op = array_merge($op, $this->captchac_options($options));
				
				if (! $filename = $this->generate_captcha($op)) {
					return '';
					break;
				}
				if (is_array($op['img_size']))
					$atts .= ' width="' . $op['img_size'][0] . '" height="' . $op['img_size'][1] . '"';
				$captcha_url = get_option('siteurl') . '/wp-content/plugins/'.CONTACTFORM.'/captcha/tmp/' . $filename;
				$html = '<img alt="captcha" src="' . $captcha_url . '"' . $atts . ' />';
				$ref = substr($filename, 0, strrpos($filename, '.'));
				$html = '<input type="hidden" id="_mmf_captcha_challenge_' . $name . '" name="_mmf_captcha_challenge_' . $name . '" value="' . $ref . '" />' . $html;
				return $html;
				break;
		}
	}

	function submit_replace_callback($matches) {
		if ($matches[1])
			$value = $this->strip_quote($matches[1]);
		if (empty($value))
			$value = __('Send', 'mmf');
		$ajax_loader_image_url = get_option('siteurl') . '/wp-content/plugins/'.CONTACTFORM.'/images/ajax-loader.gif';
        
        $html = '<input type="submit" value="' . $value . '" />';
        $html .= ' <img class="ajax-loader" style="visibility: hidden;" alt="ajax loader" src="' . $ajax_loader_image_url . '" />';
		return $html;
	}

	function form_element_parse($element) {
		$type = trim($element[1]);
		$name = trim($element[2]);
		$options = preg_split('/[\s]+/', trim($element[3]));
		
		preg_match_all('/"[^"]*"|\'[^\']*\'/', $element[4], $matches);
		$values = $this->strip_quote_deep($matches[0]);
		
		return compact('type', 'name', 'options', 'values');
	}

	function strip_quote($text) {
		$text = trim($text);
		if (preg_match('/^"(.*)"$/', $text, $matches))
			$text = $matches[1];
		elseif (preg_match("/^'(.*)'$/", $text, $matches))
			$text = $matches[1];
		return $text;
	}

	function strip_quote_deep($arr) {
		if (is_string($arr))
			return $this->strip_quote($arr);
		if (is_array($arr)) {
			$result = array();
			foreach ($arr as $key => $text) {
				$result[$key] = $this->strip_quote($text);
			}
			return $result;
		}
	}

	function generate_captcha($options = null) {
		if (! is_object($this->captcha))
			$this->captcha = new tam_captcha();
		$captcha =& $this->captcha;
		
		if (! is_dir($captcha->tmp_dir) || ! is_writable($captcha->tmp_dir))
			return false;
		
		$img_type = imagetypes();
		if ($img_type & IMG_PNG)
			$captcha->img_type = 'png';
		elseif ($img_type & IMG_GIF)
			$captcha->img_type = 'gif';
		elseif ($img_type & IMG_JPG)
			$captcha->img_type = 'jpeg';
		else
			return false;
		
		if (is_array($options)) {
			if (isset($options['img_size']))
				$captcha->img_size = $options['img_size'];
			if (isset($options['base']))
				$captcha->base = $options['base'];
			if (isset($options['font_size']))
				$captcha->font_size = $options['font_size'];
			if (isset($options['font_char_width']))
				$captcha->font_char_width = $options['font_char_width'];
			if (isset($options['fg']))
				$captcha->fg = $options['fg'];
			if (isset($options['bg']))
				$captcha->bg = $options['bg'];
		}
		
		$prefix = mt_rand();
		$captcha_word = $captcha->generate_random_word();
		return $captcha->generate_image($prefix, $captcha_word);
	}

	function check_captcha($prefix, $response) {
		if (! is_object($this->captcha))
			$this->captcha = new tam_captcha();
		$captcha =& $this->captcha;
		
		return $captcha->check($prefix, $response);
	}

	function remove_captcha($prefix) {
		if (! is_object($this->captcha))
			$this->captcha = new tam_captcha();
		$captcha =& $this->captcha;
		
		$captcha->remove($prefix);
	}

	function cleanup_captcha_files() {
		if (! is_object($this->captcha))
			$this->captcha = new tam_captcha();
		$captcha =& $this->captcha;
		
		$tmp_dir = $captcha->tmp_dir;
		
		if (! is_dir($tmp_dir) || ! is_writable($tmp_dir))
			return false;
		
		if ($handle = opendir($tmp_dir)) {
			while (false !== ($file = readdir($handle))) {
				if (! preg_match('/^[0-9]+\.(php|png|gif|jpeg)$/', $file))
					continue;
				$stat = stat($tmp_dir . $file);
				if ($stat['mtime'] + 21600 < time()) // 21600 secs == 6 hours
					@ unlink($tmp_dir . $file);
			}
			closedir($handle);
		}
	}

	function captchac_options($options) {
		if (! is_array($options))
			return array();
		
		$op = array();
		$image_size_array = preg_grep('%^size:[smlSML]$%', $options);
		if ($image_size = array_shift($image_size_array)) {
			preg_match('%^size:([smlSML])$%', $image_size, $is_matches);
			switch (strtolower($is_matches[1])) {
				case 's':
					$op['img_size'] = array(60, 20);
					$op['base'] = array(6, 15);
					$op['font_size'] = 11;
					$op['font_char_width'] = 13;
					break;
				case 'l':
					$op['img_size'] = array(84, 28);
					$op['base'] = array(6, 20);
					$op['font_size'] = 17;
					$op['font_char_width'] = 19;
					break;
				case 'm':
				default:
					$op['img_size'] = array(72, 24);
					$op['base'] = array(6, 18);
					$op['font_size'] = 14;
					$op['font_char_width'] = 15;
			}
		}
		$fg_color_array = preg_grep('%^fg:#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$%', $options);
		if ($fg_color = array_shift($fg_color_array)) {
			preg_match('%^fg:#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$%', $fg_color, $fc_matches);
			if (3 == strlen($fc_matches[1])) {
				$r = substr($fc_matches[1], 0, 1);
				$g = substr($fc_matches[1], 1, 1);
				$b = substr($fc_matches[1], 2, 1);
				$op['fg'] = array(hexdec($r . $r), hexdec($g . $g), hexdec($b . $b));
			} elseif (6 == strlen($fc_matches[1])) {
				$r = substr($fc_matches[1], 0, 2);
				$g = substr($fc_matches[1], 2, 2);
				$b = substr($fc_matches[1], 4, 2);
				$op['fg'] = array(hexdec($r), hexdec($g), hexdec($b));
			}
		}
		$bg_color_array = preg_grep('%^bg:#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$%', $options);
		if ($bg_color = array_shift($bg_color_array)) {
			preg_match('%^bg:#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$%', $bg_color, $bc_matches);
			if (3 == strlen($bc_matches[1])) {
				$r = substr($bc_matches[1], 0, 1);
				$g = substr($bc_matches[1], 1, 1);
				$b = substr($bc_matches[1], 2, 1);
				$op['bg'] = array(hexdec($r . $r), hexdec($g . $g), hexdec($b . $b));
			} elseif (6 == strlen($bc_matches[1])) {
				$r = substr($bc_matches[1], 0, 2);
				$g = substr($bc_matches[1], 2, 2);
				$b = substr($bc_matches[1], 4, 2);
				$op['bg'] = array(hexdec($r), hexdec($g), hexdec($b));
			}
		}
		
		return $op;
	}
	function save_contactform($data,$id)
	{
		global $wpdb;
		
		
		$table = $wpdb->prefix . 'contactform';
		
		if($id == "-1")
			$wpdb->insert($table,$data);
		else
		{ 
			$where = array("form_id" => $id);
			$wpdb->update($table,$data,$where);
		}
		
	}

	function save_form_data($id, $form_data)
	{
		global $wpdb, $user_ID;
		$form_data['user_ID'] = $user_ID;
		$session_id = $_REQUEST['PHPSESSID'];
	   	$client_ip = $_SERVER['REMOTE_ADDR'];
		$client_browser = $_SERVER['HTTP_USER_AGENT'];
		$request_url = $_SERVER['HTTP_REFERER'];
		$values_contactform_submit = array();
		$values_contactform_submit['fk_form_id'] = $id;
		$values_contactform_submit['session_id'] = $session_id;
		$values_contactform_submit['submit_date'] = date("Y-m-d, H:i:s");
		$values_contactform_submit['client_ip'] = $client_ip;
		$values_contactform_submit['client_browser'] = $client_browser;
		$values_contactform_submit['request_url'] = $request_url;
		$values_contactform_submit['read_flag'] = 0;
		$table = $wpdb->prefix . 'contactform_submit';
		$wpdb->insert($table,$values_contactform_submit);
		$fk_form_joiner_id = $wpdb->insert_id;
				
		foreach(array_keys($form_data) as $key)
		{
			if(eregi("^_mmf" , $key)){
				continue;
			}
			
			$arr_value = $form_data[$key];

			if(is_array($form_data[$key])){
				$arr_value = "";
				foreach($form_data[$key] as $inner)
					$arr_value .= $inner . "|";
				$arr_value = substr($arr_value, 0, strlen($arr_value) - 1);	
			}		
			
			$values_contactform_submit_data = array();
			$values_contactform_submit_data['fk_form_joiner_id'] = $fk_form_joiner_id;
			
			
			if(eregi("uploaded-file-", $key) and $arr_value == 'none') {
				continue;
			}
			if(eregi("uploaded-file-", $key) and $arr_value != 'none') {
				list($cookie , $f_name) = explode("-",$arr_value);
				copy('wp-content/plugins/'.CONTACTFORM.'/upload/temp/'.$arr_value, 'wp-content/plugins/'.CONTACTFORM.'/upload/form-upload/'.$arr_value);
				unlink('wp-content/plugins/'.CONTACTFORM.'/upload/temp/'.$arr_value);
				
				// copying tumbnail
				copy('wp-content/plugins/'.CONTACTFORM.'/upload/temp/thumb_'.$arr_value, 'wp-content/plugins/'.CONTACTFORM.'/upload/form-upload/thumb_'.$arr_value);
				unlink('wp-content/plugins/'.CONTACTFORM.'/upload/temp/thumb_'.$arr_value);
				
				//$arr_value  = '<a href="'.get_bloginfo('url').'/wp-content/plugins/mm-forms/upload/form-upload/'.$arr_value.'">'.$f_name.'</a>'; 
			}
			
			$values_contactform_submit_data['form_key'] = $key;
			$values_contactform_submit_data['value'] = $arr_value;
			$wpdb->insert($wpdb->prefix . 'contactform_submit_data',$values_contactform_submit_data);	
		}
		
	}
	
	
	// code for adding plugin button in the editor
	function mm_forms_tinymce_addbuttons() {
			add_filter("mce_external_plugins", array(&$this, "mm_forms_tinymce_addplugin"), 11);
			add_filter('mce_buttons', array(&$this, 'mm_forms_tinymce_registerbutton'), 11);
		//}
	}
	
	function mm_forms_tinymce_registerbutton($buttons) {			
		array_push($buttons, 'separator', 'mm_forms');
		array_push($buttons, 'mmForm');
		
		array_push($buttons, 'separator', 'mm_forms1');
		array_push($buttons, 'mmForm');
		
		return $buttons;
	}
	
	function mm_forms_tinymce_addplugin($plugin_array) {
		$plugin_array['mm_forms'] = WP_PLUGIN_URL.'/'.CONTACTFORM .'/tinymce/plugins/'.CONTACTFORM.'/editor_plugin.js';
		return $plugin_array;
	}
	
	 // Adds Settings link to plugins settings
	function mm_plugin_links($links, $file) {

	    $this_plugin = plugin_basename(__FILE__);

	    if ($file == $this_plugin){
	        $settings_link = '<a href="admin.php?page='.CONTACTFORM.'/mm-forms.php">'.__("Settings").'</a>';
	        array_unshift($links, $settings_link);
	    }
	    return $links;
	}
}

/* Use the admin_menu action to define the custom boxes */
add_action('admin_menu', 'mmform_add_custom_box');

/* Adds a custom section to the "advanced" Post and Page edit screens */
function mmform_add_custom_box() {

    add_meta_box( 'mmform_sectionid', __( 'MM Forms', 'myplugin_textdomain' ),
                'mmform_inner_custom_box', 'post', 'advanced' );
    add_meta_box( 'mmform_sectionid', __( 'MM Forms', 'myplugin_textdomain' ),
                'mmform_inner_custom_box', 'page', 'advanced' );

}

/* Prints the inner fields for the custom post/page section */
function mmform_inner_custom_box() {
	global $wpdb;
        echo '<input type="hidden" name="myplugin_noncename" id="myplugin_noncename" value="' .
    wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
        global $wpdb;

        $sql = "select * from " . $wpdb->prefix . "contactform_submit f left join " . $wpdb->prefix . "contactform_submit_data d on d.fk_form_joiner_id = f.id WHERE d.form_key = 'page_post_id' AND d.value = '" . $_REQUEST['post'] . "' order by f.submit_date DESC";
//      echo $sql ;
        $results =  $wpdb->get_results($sql);
		
        echo '<h4>Registrations</h4>';

        echo "<table class='widefat'>";
        echo "<tr><th scope='col' class='submit_date'>Submit Date</th>";
        echo "<th scope='col' class='client_ip'>Client IP</th>";
        echo "<th scope='col' class='request_url'>Request URL</th>";
        echo "<th scope='col' class='view' >View</th></tr>";

        if (!$results) {
                echo "<tr><td colspan='4'>No submitted form data available</td></tr>";
        } else {
                foreach ( $results as $res ) {
                        echo "<tr><th style='font-weight:normal;' scope='col'>" . $res->submit_date . "</th>";
                        echo "<th style='font-weight:normal;' scope='col'>" . $res->client_ip . "</th>";
                        echo "<th style='font-weight:normal;' scope='col'>" . $res->request_url . "</th>";
						echo "<th style='font-weight:normal;' scope='col'>
						
						<a href='" . get_bloginfo('url') . "/wp-admin/options-general.php?page=".CONTACTFORM."/mm-forms.php&action=viewDetail&id=" . $res->fk_form_joiner_id . "'>
						<img src='" . get_bloginfo('url') . "/wp-content/plugins".CONTACTFORM."/images/view.png' />
						</a>
						</th></tr>";
                }
        }
        echo "</table>";
}


require_once(dirname(__FILE__) . '/captcha/captcha.php');
$mmf = new mm_forms();

/* adding form to the the page from where it is called*/
function insert_mm_form($form_name){
	global $mmf, $wpdb;
	$sql = "SELECT form_id FROM " . $wpdb->prefix . "contactform where form_name = '".$form_name ."'";
	$form_id = $wpdb->get_var($sql);
	$matches = array (0 => '[form '.$form_id.' "'.$form_name.'"]', 1 => $form_id);
	echo $mmf->the_content_filter_callback($matches);
}

if(isset($_REQUEST['action']) and $_REQUEST['action']== 'rss' && isset($_REQUEST['form_id']) ) {
    require_once(dirname(__FILE__) ."/includes/mm-forms-rss-feed.php");
    $x = get_option('siteurl').'/action=rss&amp;form_id='.$_REQUEST['form_id'];
    $id = (int) $_REQUEST['form_id'];
	$contact_forms = $mmf->contact_forms();
    $cf = $contact_forms[$id];
	
    if($cf['rss_feed'] != 1) {
        echo  __('Sorry! RSS enabled options is not checked for this form.', 'mm-forms');
        exit;
    }
    require_once(dirname(__FILE__) ."/includes/mm-forms-generate-rss-feed.php");
}

/* Display form submissions in posts */
function get_total_submissions($atts) {
	extract(shortcode_atts(array(
		'form_id' => '',
	), $atts));
    
	global $wpdb ;
	$sql = "SELECT count(id) as total FROM " . $wpdb->prefix."contactform_submit" . " WHERE fk_form_id = '" . $form_id . "'";
	$res = mysql_query($sql);

	while($data = mysql_fetch_assoc($res))
	{
	        $total = $data[total];
	}
	return $total ;
}

add_shortcode('mmf_total_submissions', 'get_total_submissions');

/* Display form submissions anywhere */
function get_total_submissions_anywhere($form_id) {
    
	global $wpdb ;
	$sql = "SELECT count(id) as total FROM " . $wpdb->prefix."contactform_submit" . " WHERE fk_form_id = '" . $form_id . "'";
	$res = mysql_query($sql);

	while($data = mysql_fetch_assoc($res))
	{
	        $total = $data[total];
	}
	return $total ;
}


?>