<?php
$realPath= str_replace('\\','/',ABSPATH); //get a system independent path for the wordpress directory
//and here is the path for the upload directory
$upload_dir = $realPath."wp-content/uploads/";
define("GT_BASEPATH",$realPath); ///define the real path as a constant

define("GT_UPLOAD_PATH",'wp-content/uploads/');//that's upload path relative to base directory of blog




class GTUniqueHeaderUploader
{
		var $error="";	//$error= new Array();
	
	function get_upload_path()
			{
	
	//postthumb_imageupload
	/*it checks for standard ..if yes ..creates the standard directory if required otherwise ...checks for the directory name given..
	*/
				$top_dir= GT_BASEPATH.GT_UPLOAD_PATH.date('Y'); //uploads/yyyy
				$image_dir = $top_dir."/".date('m'); //uploads/yyy/mm
				
				//return if the path exists..
				if(file_exists($image_dir))
						return $image_dir;

		//if the intended folder does not exists..
		//check for top level folder
				$dir_created=true;
		
				if(!file_exists($top_dir))
						$dir_created=mkdir($top_dir);//it's boolean TRUE on SUCCESS and FALSE on Failure
	
				if($dir_created&&file_exists($image_dir)||$dir_created&&mkdir($image_dir))
						return $image_dir; 	
				else
					$this->$error="Unable to Create image directory";
			return FALSE;
			}
			
			
	function upload_file($field_name)
	{
	
	global $wp_version;
	global $wpdb;
	global $_FILES;
	$filename=$_FILES[$field_name]["name"];
			
	$uploadfile=$this->get_upload_path()."/".basename($filename);


	if(isset($_FILES[$field_name]['size'])&&$_FILES[$field_name]['size']>0)
				{
					
				if (move_uploaded_file($_FILES[$field_name]['tmp_name'], $uploadfile)) 
				{
	
					$image_filepath = $uploadfile;
	
	
				$upload_url=get_bloginfo("wpurl")."/".GT_UPLOAD_PATH.date(Y)."/".date(m)."/";
				$image_url=$upload_url.$filename;
				return $image_url;
				
		}
}
return false;	
	}
		
}



/*main class */
class  GTUniqueHeader
{
	
	//WORKS ONLY IN php 5+
	
public function __construct()
 {
 add_action("edit_form_advanced",array(&$this,'update_post_form'));
 add_action("simple_edit_form",array(&$this,'update_post_form'));
 add_action("edit_page_form",array(&$this,'update_post_form'));
 add_action("publish_post",array(&$this,'save_post_image'));
 add_action("wp_insert_post",array(&$this,'save_post_image'));
 add_action("save_post",array(&$this,'save_post_image'));
 
 //enqueue javascript...
 wp_enqueue_script("jquery");
}



//this function saves the image 
public function save_post_image($post_id) 
{
	global $wp_version;
	global $wpdb;
	global $_FILES;
	
	
	$uploader=new GTUniqueHeaderUploader();
	$image_url=$uploader->upload_file("gtheader_imageupload");
	if($image_url)
	$this->add_header_image($post_id,$image_url);
	/*extracting file name */
	 //get the new file name
	
	//IF THE FILE VALUE IS SET.. 
	
}


public function add_header_image($post_id,$image_url)
{

$custom_field="_gt_header_image";
$current_header=get_post_meta($post_id,$custom_field,0);
if(isset($current_header))
update_post_meta($post_id,$custom_field,$image_url);
else
add_post_meta($post_id,$custom_field,$image_url);

}


public function show_content()
{
?>
<div id="gtheaderimagediv" class="postbox"><h3><?php _e('Header Image'); ?></h3>
		<div class="inside">
			<!-- Why can't browsers calculate the height of a div? -->
			<table border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td>
			<!--<img id="loading" src="<?php bloginfo('siteurl');?>/wp-content/plugins/post-thumb/include/ajaxfileupload/loading.gif" style="display:none;" />
			--><?php 
					if(isset($this->header_image)):?>
						<img src="<?php echo $this->header_image ?>" height="80" alt="" style="float:left;margin-right:10px;height:80px;" />
						<?php endif;?>
						
						<input type="hidden" name="MAX_FILE_SIZE" value="30000000" />
						<input type="file" name="gtheader_imageupload" size="30" tabindex="1"  id="gtheader_imageupload" style="background-color:white;" />&nbsp;&nbsp;
						
						<br />
					</td>
					<td>
					<?php global $post;
							if(get_post_meta($post->ID, '_gt_header_image',true))
							{
								$url = get_post_meta($post->ID, '_gt_header_image',true);
							?>
								<img src="<?php echo $url; ?>" alt="" style="height:50px; width:60px;"/>
							<?php }
							else
							{
						 $gt_settings=	get_option('gt_unique_header');
						 $header_image=array();
						 $header_image=unserialize($gt_settings);
							if(isset($header_image)&&$header_image['gt_default_header'])
							{
							$url = $header_image['gt_default_header'];
							?>
								<img src="<?php echo $url; ?>" alt="" style="height:50px; width:60px;"/>
							<?php }}

						?>
					</td>
				</tr>
			</table>
		</div>
	</div>
<?php 

}//end of update post_form section

public function update_post_form()
{

//show the form 
$this->show_content();
?>
<script typ="text/javascript">
(function($) {

		function enhanceForm() {

			// Mutate the form to a fileupload form
			// As usual: Special code for IE
			if (jQuery.browser.msie) $('#post').attr('encoding', 'multipart/form-data');
			else $('#post').attr('enctype', 'multipart/form-data');

			// Ensure proper encoding
			$('#post').attr('acceptCharset', 'UTF-8');

			// Insert the fileupload field
			
//alert("done");
		}

		/* 
			
			We call the function right now, because wordpress already 
			generated all we need for this. We could also plug this in 
			as onLoad method via jQuery:
			
			$(document).ready(
				function() { 
					enhanceForm(); 
				}
			);

			But that's a little bit slow since the form addition
			shows after the completion of page loading

		*/
function show_image_input()
{
$("#gtheaderimagediv").insertAfter("#titlediv");
}

		enhanceForm();
show_image_input();//move the image upload box to top
	})(jQuery);
</script>
<?php

}


}//end of the class PostThumb

 

?>