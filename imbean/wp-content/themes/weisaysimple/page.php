<?php get_header(); ?>
<div id="roll"><div title="回到顶部" id="roll_top"></div><div title="查看评论" id="ct"></div><div title="转到底部" id="fall"></div></div>
<div id="content">
<div class="main">
<div id="mapsite">当前位置： <a title="返回首页" href="<?php echo get_settings('Home'); ?>/">首页</a> &gt; <?php the_title(); ?></div>
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<div class="left left_page">
<h2><?php the_title(); ?></h2>
<div class="article article_page">
        <div class="context"><?php the_content('Read more...'); ?></div>
</div>
</div>

<div class="articles articles_page">
<?php comments_template(); ?>
</div>

	<?php endwhile; else: ?>
	<?php endif; ?>
</div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>