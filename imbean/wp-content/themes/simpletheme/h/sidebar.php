<div id="sidebar">
<div class="sidebar-widget">
<div class="sidebar-feeds"><a href="<?php rss_feed(); ?>" id="feeds" target="_blank" style="color:#F36E30;text-decoration:none;">RSS 订阅</a></div>
<!--<div class="translator"><a href="javascript:void(0);" onclick="translatePage();" style="color:#4B72A9">Translate this page</a></div>-->
</div>
<div class="sidebar-widget hslice" id="webslice">
<div class="entry-title hdn">最新文章</div>
<a rel="entry-content" href="<?php echo get_option('home'); ?>/webslice" class="hdn">Web Slices</a>
<a rel="bookmark" href="<?php echo get_option('home'); ?>" class="hdn"><?php bloginfo('name'); ?> Web Slices</a>
<h4><span class="sidebar-title hfl">最新文章 <a href="javascript:void(0);" onclick="window.external.AddToFavoritesBar('<?php echo get_option('home'); ?>/#webslice','Web Slice','slice');" class="sidebar-more" style="color:#37ab22" title="订阅最新文章的 Web Slice">Web Slice</a></span></h4>
<ul class="entry-content sidebar-list hcf">
<?php wp_get_archives('type=postbypost&limit=6'); ?>
</ul>
</div>
<?php if ( is_home() ) { ?>
<div class="sidebar-widget">
<h4><span class="sidebar-title hfl">最新评论</span></h4>
<ul class="sidebar-list hcf">
<?php get_recent_comments(); ?>
</ul>
</div>
<?php } ?>
<div class="sidebar-widget">
<h4><span class="sidebar-title hfl">存档</span></h4>
<select class="hcf" name="archive-dropdown" onchange="document.location.href=this.options[this.selectedIndex].value;"> 
<?php wp_get_archives('type=monthly&format=option&show_post_count=1'); ?>
</select>
</div>
<?php if ( is_home() ) { ?>
<div class="sidebar-widget">
<h4><span class="sidebar-title hfl">链接</span></h4>
<ul class="sidebar-list hcf">
<?php wp_list_bookmarks('title_li=&categorize=0&orderby=id'); ?>
</ul>
</div>
<?php } ?>
<div class="sidebar-widget">
<h4><span class="sidebar-title hfl">Meta</span></h4>
<ul class="sidebar-list hcf">
<?php wp_register(); ?>
<li><?php wp_loginout(); ?></li>
</ul>
</div>
</div>
<!--/sidebar -->