<?php
require_once('../wp-config.php');
require_once('m-functions.php');
wp('pagename=');
?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.2//EN" "http://www.openmobilealliance.org/tech/DTD/xhtml-mobile12.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=UTF-8"/>
<meta http-equiv="Cache-Control" content="no-cache"/>
<meta name="HandheldFriendly" content="true"/>
<title><?php wp_title('|',true,'right'); ?><?php bloginfo('name'); ?> 移动版</title>
<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php rss_feed(); ?>"/>
<link rel="stylesheet" type="text/css" media="all" href="<?php echo m_home; ?>/m-style.css"/>
<link rel="canonical" href="<?php m_standard(); ?>"/>
</head>
<body id="main">
<div class="h">
<div class="header">
<h1><a href="<?php echo m_home; ?>" style="color:#016D89;"><?php bloginfo('name'); ?> 移动版</a> &nbsp;<span class="sep">|</span> &nbsp;<a href="<?php m_standard();?>" style="color:#016D89;">完整版</a></h1>
</div>
<?php if(empty($_GET['p'])): ?>
<?php if(have_posts()): ?>
<div class="nova">
<div class="nova-title"><h2>最新文章</h2></div>
<ul class="nova-posts">
<?php while (have_posts()): the_post(); ?>
<li><a href="<?php echo m_home; ?>/?p=<?php the_id(); ?>"><?php the_title(); ?></a><?php if ('open' == $post->comment_status): ?>&nbsp;|&nbsp;<span class="m-comment"><a href="<?php echo m_home; ?>/?p=<?php the_id(); ?>#comments"><?php comments_number('暂无评论', '1 条评论', '% 条评论'); ?></a></span><?php else: echo '<span class=\"m-comment\">评论关闭</span>'; endif; ?></li>
<?php endwhile; ?>
</ul>
</div>
<div class="nova">
<div class="nova-title"><h2>最新评论</h2></div>
<?php m_comments(); ?>
</div>
<?php endif; ?>
<?php else : ?>
<?php if (have_posts()) : ?>
<?php while (have_posts()) : the_post(); ?>
<div class="nova">
<div class="nova-title"><h2><?php the_title(); ?></h2></div>
<div class="nova-post-meta">
//. <?php the_time('Y-m-d'); ?> &nbsp;//. <?php the_author(); ?> &nbsp;//. <?php m_category(); ?>
</div>
<div class="nova-post-entry">
<?php the_content(); ?>
</div>
</div>
<div class="nova" id="comments">
<div class="nova-title"><h2><?php if ('open' == $post->comment_status) : ?><?php comments_number('暂无评论', '1 条评论', '% 条评论'); ?><?php else : ?>抱歉，评论功能未启用。<?php endif; ?></h2></div>
<?php m_comments(); ?>
</div>
<?php endwhile; ?>
<?php else : ?>
<p>No Posts Matched Your Criteria.</p>
<?php endif; ?>
<?php endif; ?>
<div class="nova">
<span class="copyright">&copy; 2010 <?php bloginfo('name'); ?></span> | <span class="sys"><?php echo get_num_queries(); ?> Queries, <?php timer_stop(1); ?> Seconds</span><br/>
<small class="rev">Powered by <a href="http://livesino.net/theme-codename-h">Theme Codename H</a> Rev.96</small>
</div>
</div>
</body>
</html>