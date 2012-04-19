<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content
 * after.  Calls sidebar-footer.php for bottom widgets.
 *
 * @package WordPress
 * @subpackage Twenty_Ten
 * @since Twenty Ten 1.0
 */
?>

	<div id="footer" role="contentinfo">
		<div id="colophon">
					<!-- addthis button -->
			<div class="addbtn-holder">
				<blockquote>
				  <blockquote>
				    <p><a class="addthis_button" href="http://www.addthis.com/bookmark.php?v=250&amp;username=takeittoeleven"><img src="http://s7.addthis.com/static/btn/v2/lg-share-en.gif" width="125" height="16" alt="Bookmark and Share" /></a>
			        </p>
			      </blockquote>
		      </blockquote>
			</div>
			<!-- promo-area -->
	  <div class="promo-area">
				<div class="img-holder">
					<img src="<?php print get_stylesheet_directory_uri() ?>/images/img05-1.gif" alt="image05" />
				</div>
				<strong class="txt-learn">Learn to earn</strong>
				<a href="/basketball/learn-to-earn" class="more">Click here to learn what it takes to earn our "I DID IT" shirt.</a>
			</div>
			<!-- bottom-section -->
			<div class="bottom-section">
				<div class="toolbar">
					<ul class="links">
						<li><a href="http://www.facebook.com/jrobinson.camps" target="_blank"><img src="<?php print get_stylesheet_directory_uri() ?>/images/img06.gif" alt="image06" /></a></li>
						<li><a href="http://twitter.com/jrobinson3" target="_blank"><img src="<?php print get_stylesheet_directory_uri() ?>/images/img07.gif" alt="image06" /></a></li>
						<li><a href="http://www.youtube.com/user/jrobinsoncamps" target="_blank"><img src="<?php print get_stylesheet_directory_uri() ?>/images/img08.gif" alt="image06" /></a></li>
						<li><a href="http://www.myspace.com/jrobwrestlingcamps" target="_blank"><img src="<?php print get_stylesheet_directory_uri() ?>/images/img09.gif" alt="image06" /></a></li>
					</ul>
					<div class="subscribe">
						<form action="http://jrobinsoncamps.us1.list-manage.com/subscribe/post?u=c294c3a9fd3f3fb7e3008b320&amp;id=fc399ae567" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank">
							<fieldset>
								<legend>subscribe</legend>
								<span>WANT MORE INFO?</span>
								<div class="row">
									<input type="text" class="txt" title="email"  value="" name="EMAIL" id="mce-EMAIL" />
									<input type="submit" class="sbm" value="Subscribe" name="subscribe" id="mc-embedded-subscribe"/>
								</div>
							</fieldset>
						</form>
                   </div>
				</div>
				<!-- columns -->
                
                            <div class="columns">
								<div class="col">
									<h3>INTENSIVE CAMPS</h3>
									<?php echo events_sidebar(4, 1);?>
                                </div>
				</div>
			</div>
            <div class="footer-links">
                <span class="copyright">&copy; <?php echo date("Y"); ?> J Robinson Inc. All rights reserved.</span>
                  <?php wp_nav_menu('menu=Footer'); ?>
            </div>
		</div><!-- #colophon -->
	</div><!-- #footer -->

</div><!-- #wrapper -->
<?php
	/* Always have wp_footer() just before the closing </body>
	 * tag of your theme, or you will break many plugins, which
	 * generally use this hook to reference JavaScript files.
	 */

	wp_footer();
?>

</body>
</html>
