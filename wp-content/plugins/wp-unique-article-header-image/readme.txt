=== Wp Unique Article Header Image ===
Contributors: Brajesh Singh
Tags: header image,image,unique header,unique,post,admin
Requires at least: 2.3.3
Tested up to: 2.6.3
Stable tag: trunk
Allows you to add/associate unique header image with each single post/page.So you can spice your blog to have a unique header image per post/page.
== Description ==

if you have ever thought of the following,then this plugin is for you.

    * You wanted to show unique header image on each single article page of your wordpress(or may be on some of the single article pages)
    * You wanted to have a unique background image for each of your single article page(or may be just on some of them)

If your answer is yes,Then this plugin is for you.It allows you to upload an image using the wordpress new post/edit post/page window.
For more information and how to use please visit http://www.thinkinginwordpress.com/2008/09/wp-unique-article-header-image-plugin-for-wordpress/




== Installation ==
1.Installation
 -Unzip the folder gt-unique-header-image
-Upload to your wp-content/plugins directory
-Go to admin wodpress admin panel->Plugins->"Gt Unique Header Image" and activate it.
-If you are using wordpress 2.5 or heigher,please turn off revisions
 to do that put this code at the top of your wp-config.php
  
<?php define('WP_POST_REVISIONS', false); ?>
That's all you are done

2.Go to Options->"GT Unique header Image"
 put their the url of the default header image to be used and also the url of the home page header(It will be used on categories,archives and other pages too).

2.Go to post new/edit window and you will see the screen having something below  the post title saying Gt Unique header Upload,Browse the image
3.save/publish the post and visit the edit screen ,you will see the thumbnail of the image associated with the post.



Please see the [Wp Unique Article header Image](http://www.thinkinginwordpress.com/2008/09/wp-unique-article-header-image-plugin-for-wordpress/) for more details.
== Frequently Asked Questions ==

1.To get the image associated with the single article page(post/psge) use
<?php $image_url=get_get_image_url();?> ..it will return the absolute url of image assocuiated with the post/page
2.to use and show the header image(or you may linke to use it as any other options)
<img src="<?php echo gt_get_image_url();?>"  />
That's it, you may use your custom css and everything
3.Further to fetch the header image assocted with a particlular post with id $post_id,use following

<img src="<?php echo gt_get_image_url();?>"  />

Please visit the [Wp Unique Article header Image](http://www.thinkinginwordpress.com/2008/09/wp-unique-article-header-image-plugin-for-wordpress/) for up-to-date information.If you have any query,any issue ,please drop amessage there.I will love to assist you.

== Screenshots ==

Please visit the [Wp Unique Article header Image](http://www.thinkinginwordpress.com/2008/09/wp-unique-article-header-image-plugin-for-wordpress/)for screenshots & user examples.





