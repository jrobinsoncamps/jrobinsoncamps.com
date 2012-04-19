/*
*	Plugin Name: mm-sniplet
*	Description: The TinyMCE module loads the shared sniplets. The user can then add the 
*				 sniplets to the content of the page and posts 
*	Author: Abdul Shahid
*	Date: 30 July 2008
*	Version: 1.0
*	Author URI: http://www.motionmill.com
*   Copyright © 2008-2009, Motionmill, All rights reserved.
*/

(function() {
	// Load plugin specific language pack
	tinymce.PluginManager.requireLangPack('mm_forms');

	tinymce.create('tinymce.plugins.MM_FromsPlugin', {
		/**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 * This call is done before the editor instance has finished it's initialization so use the onInit event
		 * of the editor instance to intercept that event.
		 *
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @param {string} url Absolute URL to where the plugin is located.
		 */
		init : function(ed, url) {
			// Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceMM_Froms');
			ed.addCommand('mceMM_Froms', function() {
				ed.windowManager.open({
					file : url + '/dialog.php',
					width : 320 + parseInt(ed.getLang('mm_forms.delta_width', 0)),
					height : 140 + parseInt(ed.getLang('mm_forms.delta_height', 0)),
					inline : 1
				});
			});

			// Register sniplet button
			ed.addButton('mm_forms', {
				title : 'mm_forms.desc',
				cmd : 'mceMM_Froms',
				image : url + '/img/mm-forms.gif'
			});

			// Add a node change handler, selects the button in the UI when a image is selected
			ed.onNodeChange.add(function(ed, cm, n) {
				cm.setActive('mm_forms', n.nodeName == 'IMG');
			});
		}


	});

	// Register plugin
	tinymce.PluginManager.add('mm_forms', tinymce.plugins.MM_FromsPlugin);
})();