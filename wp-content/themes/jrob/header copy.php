<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package WordPress
 * @subpackage Twenty_Ten
 * @since Twenty Ten 1.0
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<title><?php
	/*
	 * Print the <title> tag based on what is being viewed.
	 * We filter the output of wp_title() a bit -- see
	 * twentyten_filter_wp_title() in functions.php.
	 */
	wp_title( '|', true, 'right' );

	?></title>

<meta name="description" content="J Robinson Wrestling Camps has 34 years experience producing elite athletes. We ensure your wrestling camp experience will improve your wrestling skills and your life."/>

<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico" />
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<?php
	/* We add some JavaScript to pages with the comment form
	 * to support sites with threaded comments (when in use).
	 */
	if ( is_singular() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );

	/* Always have wp_head() just before the closing </head>
	 * tag of your theme, or you will break many plugins, which
	 * generally use this hook to add elements to <head> such
	 * as styles, scripts, and meta tags.
	 */
	wp_head();
?>
	<link href="<?php print get_stylesheet_directory_uri() ?>/css/lightbox.css" rel="stylesheet" type="text/css" media="screen" />
	<!--[if IE 8]>
    <link rel="stylesheet" type="text/css" media="all" href="<?php print get_stylesheet_directory_uri() ?>/css/ie8.css" />
	<![endif]-->
	<script type="text/javascript" src="<?php print get_stylesheet_directory_uri() ?>/js/swfobject.js"></script>
	<script type="text/javascript">
		swfobject.embedSWF('<?php print get_stylesheet_directory_uri() ?>/swf/home-flash.swf', 'placeholder-1', "848", "269", '9.0.0', '<?php print get_stylesheet_directory_uri() ?>/swf/expressInstall.swf', {}, { wmode: 'transparent' }, {});
	</script>
	<script src="<?php print get_stylesheet_directory_uri() ?>/js/prototype.js" type="text/javascript"></script>
	<script src="<?php print get_stylesheet_directory_uri() ?>/js/scriptaculous.js?load=effects" type="text/javascript"></script>
    <script src="<?php print get_stylesheet_directory_uri() ?>/js/lightbox++.js" type="text/javascript"></script>
</head>

<body <?php body_class(); ?>>
<div id="wrapper" class="hfeed">
	<h1 class="logo"><a href="/">J Robinson</a></h1>
    <!-- top-bar -->
    <div class="top-bar">
        <?php wp_nav_menu('menu=Top Menu'); ?>
    </div>
    <div class="clearer"></div>
	<div id="header">
    <!-- main-nav -->
        <strong class="slogan">INTENSIVE WRESTLING CAMPS</strong>
        <strong class="sub-text">#1 Intensive camp since 1978</strong>
        <?php wp_nav_menu( array( 'container_class' => 'menu-header', 'theme_location' => 'primary' ) ); ?>
        <?php /*?><?php wp_page_menu( $args ); ?><?php */?> 
    </div>
    
		
	</div><!-- #header -->

	
