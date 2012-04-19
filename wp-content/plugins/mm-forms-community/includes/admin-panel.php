<div id="mmf_message" style="-moz-background-clip:border;-moz-background-inline-policy:continuous;-moz-background-origin:padding;background:#FFFFDD none repeat scroll 0 0;padding:1.5em 1em;position:relative;"><span id="mmf_msg"></span></div>
<div class="wrap" style="margin-top:16px;">
<div class="wrap">
<?php
if($_REQUEST['uninstall'] == '')  {
 ?>
	<h2><img src="<?php echo get_option('siteurl') . '/wp-content/plugins/'.CONTACTFORM.'/images/forms_logo.jpg'; ?>" /> <?php _e('MM Forms Community', 'mmf'); ?></h2>
	<span style="margin-left:10px;font:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:250; color:#999999;">comes with the power of a tank, but drives like a bike.  built at </span><a href="http://motionmill.com" target="_blank"><span style="font:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:250; color:#999999;">motionmill.com</span></a>
<?php 
        if ("http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] != $base_url . '?page=' . $page) {
?>        
<?php } ?>
<?php
}
?>
<?php if (isset($updated_message)) : ?>
<script type="text/javascript">
$("#mmf_msg").text("<?php echo $updated_message ; ?>");
$("#mmf_message").animate({"height": "show"}, { duration: 0 });
setTimeout(function() { $("#mmf_message").fadeOut(); }, 5000);
</script>

<!-- <div id="message" class="updated fade"><p><strong><?php echo $updated_message; ?></strong></p></div> -->
<?php endif; ?>
<?php if ($obj_actions->view_all) :
	require_once $includes.'forms_list.php'; 
endif;
if ($cf) :
	require_once $includes.'settings_main.php';
endif;
if ($obj_actions->view_emails) :
	require_once $includes.'view_emails.php';
endif; 
if ($obj_actions->view_form_details) :
	require_once $includes.'details.php';
endif; ?>
</div>
