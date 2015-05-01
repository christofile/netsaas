<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<title><?php if(is_single() || is_page() || is_archive() || is_404() || is_search()) { ?><?php wp_title('|',true,'right'); ?><?php } ?><?php bloginfo('name'); ?><?php if( $paged == "" ) $pagenum = "";else echo $pagenum = " - 第 ".$paged." 页"; ?></title>
<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo('stylesheet_url'); ?>"/>
<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php rss_feed(); ?>"/>
<link rel="alternate" type="application/atom+xml" title="Atom 0.3" href="<?php bloginfo('atom_url'); ?>"/>
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>"/>
<?php wp_head(); ?></head>
<body id="main">
<div id="h">
<div id="header">
<div class="features"></div>
<div>
<a href="<?php echo get_option('home'); ?>/"><img src="<?php bloginfo('stylesheet_directory');?>/images/livesino_logo.png" class="logo hfl" alt="<?php bloginfo('name'); ?>"/></a>
<ul class="nav hfl">
<?php wp_list_pages('depth=1&title_li=0&sort_column=menu_order&number=6'); ?>
</ul>
<div class="search hfr">
<?php include (TEMPLATEPATH . '/widget-search.php'); ?>
</div>
</div>
</div>
