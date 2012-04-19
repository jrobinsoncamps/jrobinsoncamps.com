<?php
/*Plugin name: WP Unique Article Header Image
Version:1.0 
Author:Brajesh Singh
Author URI:http://ThinkingInWordpress.com
Plugin URI:http://www.thinkinginwordpress.com/2008/09/wp-unique-article-header-image-plugin-for-wordpress/
Description:This plugin allows uploading  of a unique header image/background image(that's up to you) images for each posts from the admin post screen and then allows you to show the thumbnail like in many of the magzine style themes 
License:GPL
*/
require_once realpath(dirname(__file__) . '/GTUniqueHeader.class.php');
require_once realpath(dirname(__file__) . '/gt-unique-header-front.php');
$gtUniqueheader=new GTUniqueHeader();
$gtms=new GTUniqueHeaderAdmin();
 class GTUniqueHeaderAdmin
{

 function __construct()
   {
   
    add_action('admin_menu',array(&$this,"add_options_subpanel"));
   }
	//=============================================
    // Displays The Options Panel
    //=============================================
    function options_panel() 
	{
if(isset($_POST['gt-header-option-submitted']))
				{

				$settings=array();
				$settings['gt_default_header']=$_POST['gt_default_header'];
				$settings['gt_homepage_header']=$_POST['gt_homepage_header'];
				
				$setting_str = serialize($settings);
			
				if(get_option('gt_unique_header'))
							update_option("gt_unique_header", $setting_str);
				else
					add_option("gt_unique_header", $setting_str, '', 'yes');
			}//updated

			$settings=unserialize(get_option('gt_unique_header'));
		
			$this->gt_header_show_options($settings);
		}

function gt_header_show_options($settings)
{	
	?>
		
		
		<div class="wrap">
			<h2>GT Unique Header Image Settings</h2>
			<form method="post" action="">
			<table class="form-table">
			<tr valign="top">
					<th scope="row">
							<input type="hidden" name="setting" value="true" />
							<?php _e("Default post/page header Image(url)");?>
					</th>
					<td>
						<input type="text" name="gt_default_header" value="<?php echo $settings['gt_default_header']; ?>" />
					</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e("Home page header(url)");?>
				</th>
				<td><input type="text" name="gt_homepage_header" value="<?php echo $settings['gt_homepage_header']; ?>" />
				</td>
			</tr>
			<tr valign="top">
			<th colspan='2' scope='row'>
					<input class ="sub" type="submit" name="gt-header-option-submitted" value="<?php _e('Save Settings') ?> &raquo; " />
			</th>
			</tr>
			</table>
			</form>
		</div>
		<?php
	}

	//=============================================
    // admin options panel
    //=============================================
    function add_options_subpanel() {
        if (function_exists('add_options_page')) {
          add_options_page('GT Unique Header Image', 'GT Unique Header Image', 10, __FILE__, array(&$this,'options_panel'));
        }
    }
   } 
  
?>