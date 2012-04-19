<?php

/*
	functions
	
	Created by Tom Belmans on 2009-09-25.
	Copyright (c) 2009 Addwittz - Motionmill. All rights reserved.
*/

function showsUserOptionsDrop($currentuser)
{
    $string = '';
	global $wpdb, $user_ID;

	$sql="SELECT ID,user_login FROM " . $wpdb->prefix . "users";
	$results =  $wpdb->get_results($sql);
	$i=0;
	foreach ($results as $result)
	{
		$userList[$i]=$result->ID;
		$userListName[$i]=$result->user_login;
		$i++;
	}
	
	$sql="SELECT allow_user FROM " . $wpdb->prefix . "contactform where form_id=".$currentuser;
	$results =  $wpdb->get_results($sql);		
	foreach ($results as $result)
	{
		$selectList=$result->allow_user;
	}
	$selectLists=split(" ", $selectList);
	
	for($i=0;$i<count($userList);$i++)
	{	
		$s='';
		for($j=0;$j<count($selectLists);$j++)
		{
			if($selectLists[$j]==$userList[$i])
			{
				$s=' selected="selected"';
			}
		}
		$string .= '<option value="'.$userList[$i].'"'.$s.'>'.$userListName[$i].'</option>'."\n";
	}
	
   return $string; 
}



function displaysubmitdata($submitdataformid)
{
	//Modified by hitesh : Preview submited data to front end
		global $wpdb;
		$sql2="SELECT id FROM " . $wpdb->prefix . "contactform_submit where fk_form_id=".$submitdataformid;
		$getjoinerdata=$wpdb->get_results($sql2);
		$data="";
		foreach($getjoinerdata as $select12)
		{
			$fk_form_joiner_id=$select12->id;
			$sql="SELECT fk_form_joiner_id FROM " . $wpdb->prefix . "contactform_submit_data where fk_form_joiner_id =". $fk_form_joiner_id." group by fk_form_joiner_id";
			$select_datas =  $wpdb->get_results($sql);
			foreach ($select_datas as $select_data)
			{
			   $sql1="SELECT * FROM " . $wpdb->prefix . "contactform_submit_data where fk_form_joiner_id=".$select_data->fk_form_joiner_id ." ORDER BY `wp_contactform_submit_data`.`id` ASC";
			   $getdata=$wpdb->get_results($sql1);
			   $data.="#";
				foreach($getdata as $select)
				{
				  $data.='|'.$select->form_key.'^'.$select->value.'^';
				}
			}
		 }
		 $tableformat='';
		 $tableformat.='<table width="95%" cellpadding="0" cellspacing="0" class="table_border"><tr class="header_row"><td>Name</td><td>E-Mail </td><td>Subject</td><td>Message</td></tr>';	
		 
		$eachdatapart=explode('#',$data);
		for($t=1;$t<count($eachdatapart) && $t<3; $t++)
		{
			if($t%2==0)
				$rowcolor="odd_row";
			else
				$rowcolor="even_row";
				
			$tableformat.='<tr class="'.$rowcolor.'">';
			$datapart=explode('|',$eachdatapart[$t]);
			for($h=0;$h<count($datapart);$h++)
			{
				$datapartsub=explode('^',$datapart[$h]);
				for($k=0;$k<count($datapartsub);$k++)
				{
					if($k==1&&$h==2)
					{  
						$temptableformat='<td>'.$datapartsub[$k].'</td>';
					}
					if($k==1&&$h>2&&$h<7)
					{  
						$tableformat.='<td>'.$datapartsub[$k].'</td>';
					}
				}
			}		
			$tableformat.='</tr>';		  
		}
	   	$tableformat.='</table>';
		return $tableformat;				
}

/* Deprecated function 
	function getFormsList(){
		global $wpdb, $user_ID;
		$sql="SELECT allow_user FROM " . $wpdb->prefix . "contactform where form_id=".$currentuser;
		
	}
	
	function form_data_tamplate()
	{
        $string = '';
		global $wpdb, $user_ID;
		$sql="SELECT ID,user_login FROM " . $wpdb->prefix . "users";
		$results =  $wpdb->get_results($sql);
		$i=0;
		foreach ($results as $result)
		{
			$userList[$i]=$result->ID;
			$userListName[$i]=$result->user_login;
			$i++;
		}	
	}
	
	function get_menu_css($formid)
	{
		global $wpdb;
		$sql = "SELECT csstext FROM " . $wpdb->prefix. "contactform where form_id=".$formid;
	        $results =  $wpdb->get_results($sql);
			if($results=="")
			{
				return 'Error in css';
				break;
			}
			foreach ($results as $result) 
			{
				echo $result->csstext;
			}//End First For loop
	}   
		
	
*/

function createThumb($src,$height,$width,$siteurl) {
	ini_set('memory_limit','64M');
	// getting params and setting defaults
	if ( !isset($src) ) {
	    echo 'createThumb: missing "src" parameter';
		return;
	}
	if ( !isset($height) && !isset($width) ) {
	    echo 'createThumb: missing "width" and "height" parameters';
		return;
	}
	if ( !isset($siteurl) ) {
	    echo 'twobble: missing "siteurl" parameter';
		return;
	}

	$src           = trim($src);
	$maxWidth      = isset($width) ? trim($width) : 0;
	$maxHeight     = isset($height) ? trim($height) : 0;


	// filename
	$parts = explode('.', basename($src));
	$extension = array_pop($parts);
	$name = implode('.', $parts);

	$fileName = 'thumb_'.$name.'_'.$maxWidth.'_'.$maxHeight.'.jpg';
	if ( $l = strrpos($src, '/') ) {
	    $path = substr($src, 0, $l);
	} else {
	    $path = '/';
	}

	$file = $path.'/'.$fileName;
	if ( !file_exists($file) ) {
	    // calculate new size
	    list($oldWidth, $oldHeight) = getimagesize($src);
	    $width = $oldWidth;
	    $height = $oldHeight;

	    if ( $maxWidth && $oldWidth > $maxWidth ) {
	        $width = $maxWidth;
	        $height = $oldHeight*($maxWidth/$oldWidth);
	    }

	    if ( $maxHeight && $height>$maxHeight ) {
	        $width = $width*($maxHeight/$height);
	        $height = $maxHeight;
	    }

	    // image
	    $thumb = imagecreatetruecolor($width, $height);
	    switch ( $extension ) {
	        case 'png':
	            $src = imagecreatefrompng($src);
	        break;
	        case 'gif':
	            $src = imagecreatefromgif($src);
	        break;
	        case 'jpg':
	        case 'jpeg':
			case 'JPG':
			case 'JPEG':
	            $src = imagecreatefromjpeg($src);
	        break;
	        default:
	            echo 'createThumb: unknown filetype';
	            return '';
	        break;
	    }
	    // resize
	    imagecopyresampled($thumb, $src, 0, 0, 0, 0, $width, $height, $oldWidth, $oldHeight);

	    // save as file
	    imagejpeg($thumb, $file, 95);
	    imagedestroy($thumb);
	    imagedestroy($src);
	}

	$dir = $path;
	$urlpath = str_replace($dir, $siteurl . '/wp-content/plugins/'.CONTACTFORM.'/upload/form-upload', $path);
	$file = $urlpath . "/" . $fileName ;

	return $file;
}


?>
