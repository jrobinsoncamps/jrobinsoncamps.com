<?php
function gt_get_image_url($post_id=0)
{
global $post;
if(!$post_id)
$post_id=$post->ID;//get the post id for current post
$image_url=get_post_meta($post_id,"_gt_header_image",true);

if(empty($image_url))		{
		//check for default header
	
		if(get_option("gt_unique_header"))
		{
		$settings=unserialize(get_option("gt_unique_header"));
		$image_url=$settings["gt_default_header"];
		
		
		}
		}
 if(isset($image_url))
 return $image_url;
else return false;
}	
		?>