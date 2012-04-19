<?php
	session_start();
	$error = "";
	$msg = "";
	$fileElementName = 'fileToUpload';
	function createthumb($name,$filename,$new_w,$new_h)
	{
		$system=explode(".",$name);
		if (preg_match("/jpg|jpeg/i",$system[1])){$src_img=imagecreatefromjpeg($name);}
		if (preg_match("/png/i",$system[1])){$src_img=imagecreatefrompng($name);}
		$old_x=imageSX($src_img);
		$old_y=imageSY($src_img);
		if ($old_x > $old_y) 
		{
			$thumb_w=$new_w;
			$thumb_h=$old_y*($new_h/$old_x);
		}
		if ($old_x < $old_y) 
		{
			$thumb_w=$old_x*($new_w/$old_y);
			$thumb_h=$new_h;
		}
		if ($old_x == $old_y) 
		{
			$thumb_w=$new_w;
			$thumb_h=$new_h;
		}
		$dst_img=ImageCreateTrueColor($thumb_w,$thumb_h);
		imagecopyresampled($dst_img,$src_img,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y); 
		if (preg_match("/png/",$system[1]))
		{
			imagepng($dst_img,$filename); 
		} else {
			imagejpeg($dst_img,$filename); 
		}
		imagedestroy($dst_img); 
		imagedestroy($src_img); 
	}
	
	if(!empty($_FILES[$fileElementName]['error']))
	{
		switch($_FILES[$fileElementName]['error'])
		{

			case '1':
				$error = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
				break;
			case '2':
				$error = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
				break;
			case '3':
				$error = 'The uploaded file was only partially uploaded';
				break;
			case '4':
				$error = 'No file was uploaded.';
				break;

			case '6':
				$error = 'Missing a temporary folder';
				break;
			case '7':
				$error = 'Failed to write file to disk';
				break;
			case '8':
				$error = 'File upload stopped by extension';
				break;
			case '999':
			default:
				$error = 'No error code avaiable';
		}
	}elseif(empty($_FILES['fileToUpload']['tmp_name']) || $_FILES['fileToUpload']['tmp_name'] == 'none')
	{
		$error = 'No file was uploaded..';
	}else 
	{
		$uploaddir = '../upload/temp/';
		
		$file_name = time() .'-'.basename($_FILES['fileToUpload']['name']);
		$uploadfile = $uploaddir . $file_name;
		$uploadfile_thumb = $uploaddir . "thumb_". $file_name;
		
		if (!move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $uploadfile)) {			
			$error = "Error in uploading file!";
		}
//		/* Only create thumbnails for images */
//		$file_ext = strtolower(substr($uploadfile,-3)) {
//		if ($file_ext == "gif" || $file_ext == "jpg" || $file_ext == "png") {
//			createthumb($uploadfile, $uploadfile_thumb, 60, 60);			
//		}

		$msg .= "The File : (" . $_FILES['fileToUpload']['name'] . ") has been successfully uploaded! ";
			
	}
		
	echo "{";
	echo				"error: '" . $error . "',\n";
	echo				"msg: '" . $msg ."',\n";
	echo				"filename: '" . $file_name ."'\n";
	echo "}";
?>