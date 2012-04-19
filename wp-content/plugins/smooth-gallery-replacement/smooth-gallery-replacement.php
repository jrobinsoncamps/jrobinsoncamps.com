<?php
/*
Plugin Name: Smooth Gallery Replacement
Plugin URI: http://wordpress.org/extend/plugins/smooth-gallery-replacement/
Description: Instantly replace your standard WordPress [gallery] presentation with John Design's SmoothGallery.
Version: 1.0
Author: Ulf Benjaminsson
Author URI: www.ulfben.com
License: GPL
*/
if(!defined('WP_CONTENT_URL')){
	define('WP_CONTENT_URL', get_option('siteurl').'/wp-content');
}
if(!defined('WP_CONTENT_DIR')){
	define('WP_CONTENT_DIR', ABSPATH.'wp-content');
}
if(!defined('WP_PLUGIN_URL')){
	define('WP_PLUGIN_URL', WP_CONTENT_URL.'/plugins');
}
if(!defined('WP_PLUGIN_DIR')){
	define('WP_PLUGIN_DIR', WP_CONTENT_DIR.'/plugins');
}
define('SGR_OPT', 'Smooth_Gallery_Replacement_Options');
define('SGR_URL', WP_PLUGIN_URL.'/smooth-gallery-replacement/');
define('SGR_SCRIPT_URL', SGR_URL.'scripts/');
define('SGR_TAG', '<!--sgr-->');

if(function_exists('register_activation_hook') && function_exists('register_deactivation_hook')) {	
	register_activation_hook(__FILE__, 'sgr_set_options');
	register_deactivation_hook(__FILE__, 'sgr_delete_options');		
}
add_action('admin_menu', 'sgr_add_option_page');

function sgr_add_option_page() {
	if ( function_exists('add_options_page') ) {
		add_options_page('Smooth Gallery Repl. Settings', 'Smooth Gallery R.', 8, __FILE__, 'sgr_option_page');
		add_filter('plugin_action_links', 'sgr_add_plugin_actions', 10, 2 );
	}
}

function sgr_get_info($s = 'Version'){
	$plugin_data = get_plugin_data(__FILE__);
	return "".$plugin_data[$s];	
}

function sgr_add_admin_footer(){ //shows some plugin info at the footer of the config screen.
	$plugin_data = get_plugin_data(__FILE__);
	printf('%1$s plugin | Version %2$s | by %3$s', $plugin_data['Title'], $plugin_data['Version'], $plugin_data['Author']);
	echo ' (who <a href="http://www.amazon.com/gp/registry/wishlist/2QB6SQ5XX2U0N/105-3209188-5640446?reveal=unpurchased&filter=all&sort=priority&layout=standard&x=21&y=17">appreciates books</a>) :)<br />';
}
	
function sgr_add_plugin_actions($links, $file){ //add's a "Settings"-link to the entry on the plugin screen
	static $this_plugin;
	if(!$this_plugin){
		$this_plugin = plugin_basename(__FILE__);
	}	
	if($file == $this_plugin){				
		$settings_link = $settings_link = '<a href="options-general.php?page='.$this_plugin.'">' . __('Settings') . '</a>';
		array_unshift($links, $settings_link);	
	}
	return $links;		
}

function sgr_set_options(){
	$conf = array(
			'adminPreview' => 0, //this is used in PHP, not JS. So let it be a digit.
			'delay'	=> 5000,
			'embedLinks' => 'false',
			'size' => 'medium',
			'showArrows' => 'true',			
			'showCarousel' => 'true',
			'showInfopane' => 'true',
			'showSingle' => 'false', //display only the first image if JS is disabled.		
			'textShowCarousel' => 'more',
			'timed' => 'false',
			'useHistoryManager' => 'false'			
		);			
	update_option(SGR_OPT, $conf);	
}

function sgr_delete_options(){
	delete_option(SGR_OPT);	
}

/**
*	This implementation of the [gallery] shortcode supports 
*	excluding images from the gallery: [gallery exclude='id1,id2']
*/
function sgr_shortcode($attr){
	global $post;	
	//$a_rel = "slimbox[cleaner-gallery-$post->ID]";
	//$a_class = "slimbox";		
	if(isset($attr['orderby'])){
		$attr['orderby'] = sanitize_sql_orderby($attr['orderby']);
		if(!$attr['orderby']){
			unset($attr['orderby']);
		}
	}
	$pid = $post->ID;
	if(!$pid && isset($_POST['submit']) && isset($_POST['previewID'])){	
		$pid = $_POST['previewID'];
	}
	extract(shortcode_atts(array(
		'order' => 'ASC',
		'orderby' => 'menu_order', //menu_order ID
		'id' => $pid,
		'itemtag' => 'dl',
		'icontag' => 'dt',
		'link'	=> '',
		'captiontag' => 'dd',
		'columns' => 4, //new default.
		'size' => 'thumbnail',
		'exclude' => ''
	), $attr));
	$exclude = explode(',',$exclude);
	$id = intval($id);
	//echo '<!-- $order = ' . $order . ' $orderby = ' . $orderby . '-->';
	$attachments = get_children( array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby, 'post__not_in' => $exclude) );
	if(empty($attachments)){
		return '';
	}
	if(is_feed()){
		$output = "\n";
		foreach($attachments as $id => $attachment){
			$output .= wp_get_attachment_link($id, $size, true)."\n";
		}
		return $output;
	}
	$conf = get_option(SGR_OPT);
	$galleryID = 'sgr_'.$id.uniqid(mt_rand()); //in case the user runs several galleries in the same post
	$hideAllButFirst = $conf['showSingle'] == 'true';
	$firstImage = true;
	$size = $conf['size'];
	$smoothgallery = SGR_TAG.'<div id="'.$galleryID.'" class="smoothgallery">';	
	foreach($attachments as $id => $attachment){		
		if($size === 'original'){
			$url = wp_get_attachment_url($id);
		}else{
			$url = wp_get_attachment_image_src($id, $size, false);
			$url = $url[0];//1 = width, 2 = height	
		}	
		if($link === ''){
			$linkTo = get_attachment_link($id); //to attachment page
		}else if($link === 'file'){
			$linkTo = wp_get_attachment_url($id); //to original file
		}
		$thumb = wp_get_attachment_image_src($id, 'thumbnail');
		$thumb = $thumb[0]; 					
		$title = htmlspecialchars($attachment->post_title);						
		$caption = ($attachment->post_content) ? $attachment->post_content : ($attachment->post_excerpt) ? $attachment->post_excerpt : '';
		$hidden = ($hideAllButFirst) ? !$firstImage : false;
		$smoothgallery .= sgr_get_markup($url, $title, htmlspecialchars($caption), $linkTo, $thumb, $hidden);		
		$firstImage = false;
	}	
	$smoothgallery .= '</div>';
	$smoothgallery .='<script type="text/javascript">
	function init_'.$galleryID.'() {	
	var options = {timed:'.$conf['timed'].', delay:'.$conf['delay'].', showArrows:'.$conf['showArrows'].', useHistoryManager:'.$conf['useHistoryManager'].',showCarousel:'.$conf['showCarousel'].', textShowCarousel:"'.$conf['textShowCarousel'].'",showInfopane:'.$conf['showInfopane'].', embedLinks:'.$conf['embedLinks'].'};
	var my = new gallery($("'.$galleryID.'"), options);';
	if($conf['useHistoryManager'] == 'true'){ 
		$smoothgallery .='History.start();';
	}		
$smoothgallery .='};
window.addEvent("domready", init_'.$galleryID.');
 </script>';
	$smoothgallery .= SGR_TAG;
	return $smoothgallery;
}

function sgr_get_markup($img, $title, $caption, $link, $thumb, $hidden = false){	
	$smoothgallery = ($hidden) ? '<div class="imageElement" style="display:none;">' : '<div class="imageElement">';
	$smoothgallery .= '<h3>'.$title.'</h3>';				
	$smoothgallery .= '<img src="'.$img.'" class="full" alt="'.$title.'"/>';
	$smoothgallery .= '<p>'.$caption.'</p>';
	$smoothgallery .= '<img src="'.$thumb.'" class="thumbnail" alt="'.$title.'" />';		
	$smoothgallery .= '<a href="'.$link.'" title="open image" class="open"></a>';		
	$smoothgallery .= '</div>';
	return $smoothgallery;
}

function sgr_css () {
	if(is_admin()){
		$conf = get_option(SGR_OPT);	
		if($conf['adminPreview'] == 0 && !isset($_POST['submit'])){
			return;
		}else if($conf['adminPreview'] == 0 && !isset($_POST['adminPreview'])){
			return;
		}
	}	
	//$slimbox = WP_PLUGIN_URL.'/smooth-gallery-replacement/scripts/slimbox-1.69/css/slimbox.css';
	$file = SGR_URL.'css/jd.gallery.css';   
	wp_register_style('sgr_css', $file); 
	//wp_register_style('slimbox', $slimbox); 
	wp_enqueue_style('sgr_css');	
	//wp_enqueue_style('slimbox');	
}	

function sgr_js() {
	if(is_admin()){
		$conf = get_option(SGR_OPT);	
		if($conf['adminPreview'] == 0 && !isset($_POST['submit'])){
			return;
		}else if($conf['adminPreview'] == 0 && !isset($_POST['adminPreview'])){
			return;
		}
	}	
	//$slimbox = SGR_SCRIPT_URL.'slimbox-1.69/slimbox.js';	
	wp_enqueue_script('mootools', SGR_SCRIPT_URL.'mootools-1.2.1-core-yc.js', false, '1.2.1');
	wp_enqueue_script('mootools-more', SGR_SCRIPT_URL.'mootools-1.2-more.js', array('mootools'), '1.2');
	wp_enqueue_script('history', SGR_SCRIPT_URL.'History.js', false, '1.0');
	wp_enqueue_script('history.routing', SGR_SCRIPT_URL.'History.Routing.js', array('history'), '2.0');		
	wp_enqueue_script('jd.gallery', SGR_SCRIPT_URL.'jd.gallery.js', array('mootools', 'history'), '2.1beta1');
	//wp_enqueue_script('slimbox', $slimbox, array('mootools'), '1.69');	
	//wp_enqueue_script('jg.gallery.set', SGR_SCRIPT_URL.'jd.gallery.set.js', array('jd.gallery'), '2.1beta1');
	//wp_enqueue_script('jd.gallery.transitions', SGR_SCRIPT_URL.'jd.gallery.transitions.js', array('jd.gallery'), '2.1beta1');	
}
remove_shortcode('gallery');// Remove original gallery shortcode
add_shortcode('gallery', 'sgr_shortcode');	
add_action('wp_print_styles', 'sgr_css');
add_action('wp_print_scripts', 'sgr_js');	
	
function sgr_option_page(){
	add_action('in_admin_footer', 'sgr_add_admin_footer');
	$conf = get_option(SGR_OPT);		
	if(isset($_POST['submit'])) {
		function sgr_issetbool(&$var){return isset($var) ? 1 : 0;}
		function getBoolS($n){ return ($n === 0) ? 'false' : 'true'; } //strings are what we store in the database.		
		$conf = array(
			'delay'	=> intval($_POST['delay']),
			'adminPreview' => sgr_issetbool($_POST['adminPreview']),
			'embedLinks' => getBoolS(sgr_issetbool($_POST['embedLinks'])),
			'size' => $_POST['size'],
			'showSingle' => getBoolS(sgr_issetbool($_POST['showSingle'])),
			'showArrows' => getBoolS(sgr_issetbool($_POST['showArrows'])),
			'showCarousel' => getBoolS(sgr_issetbool($_POST['showCarousel'])),
			'showInfopane' => getBoolS(sgr_issetbool($_POST['showInfopane'])),		
			'textShowCarousel' => $_POST['textShowCarousel'],
			'timed' => getBoolS(sgr_issetbool($_POST['timed'])),
			'useHistoryManager' => getBoolS(sgr_issetbool($_POST['useHistoryManager']))			
		);
		update_option(SGR_OPT, $conf);						
		echo '<div id="message" class="updated fade"><p><font color="black">Settings updated...</font><br /></p></div>';	
	}
	if(isset($_POST['previewID'])){$_POST['previewID'] = intval($_POST['previewID']);}
	else {$_POST['previewID'] = 0;}
	
	function getBoolN($s){return ($n === 'false') ? 0 : 1;} //numbers are used in the HTML.	
	function isChecked($s){return ($s === 'true' || $s === 1) ? 'CHECKED' : '';}
	function isSelected($s, $v = 'size'){$conf = get_option(SGR_OPT); return($conf[$v] == $s ? 'SELECTED' : '');}
	$smoothgallery ='';
	if($conf['adminPreview']){ //prepare a preview gallery.
		if($_POST['previewID']){
			$post = get_post($_POST['previewID']);
			$smoothgallery = do_shortcode($post->post_content);
		}else{
			$galleryID = 'demo';
			$smoothgallery = '<p>Running with demo content will <em>not</em> show size-, links- or caption settings.<br />Enter a post ID with a gallery in for a proper preview.</p>';
			$smoothgallery .= SGR_TAG.'<div id="'.$galleryID.'" class="smoothgallery">';	
			$smoothgallery .= sgr_get_markup(SGR_URL.'demo/demo.png', 'demo title', 'demo caption', SGR_URL.'demo/demo.png', SGR_URL.'demo/demo_thumb.png', false);
			$smoothgallery .= sgr_get_markup(SGR_URL.'demo/demo2.png', 'demo2 title', 'demo2 caption', SGR_URL.'demo/demo2.png', SGR_URL.'demo/demo2_thumb.png', false);				
			$smoothgallery .= '</div>';
			$smoothgallery .='<script type="text/javascript">
				function init_'.$galleryID.'() {	
				var options = {timed:'.$conf['timed'].', delay:'.$conf['delay'].', showArrows:'.$conf['showArrows'].', useHistoryManager:false,showCarousel:'.$conf['showCarousel'].', textShowCarousel:"'.$conf['textShowCarousel'].'", showInfopane:'.$conf['showInfopane'].', embedLinks:'.$conf['embedLinks'].'};
				var my = new gallery($("'.$galleryID.'"), options);
			};
			window.addEvent("domready", init_'.$galleryID.');
			 </script>';
			$smoothgallery .= SGR_TAG;
		}
	}
	
	print('<div class="wrap">
<h2>Smooth Gallery Replacement</h2> <form method="post"><fieldset class="options"><table class="optiontable" width="100%">');
print('<tr><td width="400px"><h3>Settings</h3></td><td>&nbsp;<h3>Preview</h3></td></tr>');	
print("<tr><td rowspan='8' valign='top'><input name='adminPreview' type='checkbox' id='adminPreview' value='".$conf['adminPreview']."' ".isChecked($conf['adminPreview'])." /><label for='adminPreview'>&nbsp;". __('Enable Admin Preview')."</label>&nbsp;(<input name='previewID' type='text' size='3' id='previewID' value='".$_POST['previewID']."' /><label for='previewID'>&nbsp;". __('<em>optional:</em> post id to preview)')."</label><br />");
print("<input name='embedLinks' type='checkbox' id='embedLinks' value='".getBoolN($conf['embedLinks'])."' ".isChecked($conf['embedLinks'])." /><label for='embedLinks'>&nbsp;". __('Embed Links')."</label><br />");
print("<input name='showSingle' type='checkbox' id='showSingle' value='".getBoolN($conf['showSingle'])."' ".isChecked($conf['showSingle'])." /><label for='showSingle'>&nbsp;". __('If JavaScript is disabled, display only the first image.')."</label><br />");
print("<input name='showArrows' type='checkbox' id='showArrows' value='".getBoolN($conf['showArrows'])."' ".isChecked($conf['showArrows'])." /><label for='showArrows'>&nbsp;". __('Show Arrows')."</label><br />");
print("<input name='showCarousel' type='checkbox' id='showCarousel' value='".getBoolN($conf['showCarousel'])."' ".isChecked($conf['showCarousel'])." /><label for='showCarousel'>&nbsp;". __('Show Carousel')."</label>&nbsp;<input name='textShowCarousel' type='text' id='textShowCarousel' value='".$conf['textShowCarousel']."' /><label for='textShowCarousel'>&nbsp;". __('Carousel label')."</label><br />");
print("<input name='showInfopane' type='checkbox' id='showInfopane' value='".getBoolN($conf['showInfopane'])."' ".isChecked($conf['showInfopane'])." /><label for='showInfopane'>&nbsp;". __('Show Infopane')."</label><br />");
print("<input name='timed' type='checkbox' id='timed' value='".getBoolN($conf['timed'])."' ".isChecked($conf['timed'])." /><label for='timed'>&nbsp;". __('Autoplay (timed)')."</label>&nbsp;<input name='delay' type='text' size='3' id='delay' value='".$conf['delay']."' /><label for='delay'>&nbsp;". __('(delay in ms)')."</label><br />");
print ("<label for='size'>&nbsp;".__('Use image size: ')."</label><select name='size'>
			<option ".isSelected('thumbnail')." value='thumbnail'>thumbnail</option>
			<option ".isSelected('medium')." value='medium'>medium</option>
			<option ".isSelected('large')." value='large'>large</option>
			<option ".isSelected('original')." value='original'>original</option>						
			</select><br />");
print("<input name='useHistoryManager' type='checkbox' id='useHistoryManager' value='".getBoolN($conf['useHistoryManager'])."' ".isChecked($conf['useHistoryManager'])." /><label for='useHistoryManager'>&nbsp;". __('Use History Manager')."</label></td><br />");
print('</td><td rowspan="8">'.$smoothgallery.'&nbsp;</td></tr>');
print("</table></fieldset><p class='submit'><input type='submit' name='submit' value='".__('Update Settings &raquo;')."' /></p></form>");
print('<p>For more information and help with SmoothGallery, please read <a href="http://smoothgallery.jondesign.net/getting-started/">"Getting Started"</a>.<br />
For modification and advanced settings, see <a href="http://smoothgallery.jondesign.net/getting-started/faq/">JonDesign\'s offical FAQ</a></p>');	
print('<p>Remember to customize SmoothGallery in your own theme\'s CSS. Here\'s an example to get you started:</p>
<pre>.smoothgallery{
	width: 600px !important; /*override default size*/
	height: 400px !important;
	border:none; !important; 
} 
.smoothgallery p, .smoothgallery h3{
	display:none;	/*show only images if jscript is off*/
}
</pre></div>');
}//end of admin page function.	
?>