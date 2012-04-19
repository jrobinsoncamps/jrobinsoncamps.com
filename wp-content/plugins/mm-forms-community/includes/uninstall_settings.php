<?php

/*
	uninstall_settings
	
	Created by Tom Belmans on 2009-09-24.
	Copyright (c) 2009 Addwittz - Motionmill. All rights reserved.
*/


?>
	<p>
		<br />
	</p>
	<hr />
		<h3>Uninstall MM Forms</h3>
       <form method="post" action = "<?php echo $base_url . '?page=' . $page . '&contactform=uninstall'; ?>">
       <div style="margin-top: 20px;">
	<label for="uninstall"><?php _e('Type "Uninstall" in the box if you want to completely remove MM Forms plugin.  This includes deleting all
your data and database tables.','mm-forms'); ?><br /></label>
               <label style="color:red;text-decoration:bold;">Warning : this action cannot be undone!  So please be careful when using this.</label>
			<br />
           <input type="text" name="uninstall" id="uninstall"> <input type="submit" name="Uinstall" value="<?php _e("Remove MM Forms"); ?>">
       </div>
       </form>
	</div>