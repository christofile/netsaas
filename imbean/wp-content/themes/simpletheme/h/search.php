<?php
header('Content-type: text/html;charset=UTF-8');
if (isset($_GET['s'])) {
	$go = $_GET['s'];
	$to = get_settings('home');
	if ($go != '') {
		if ($go == '~' | $go == '~home') {
			wp_redirect($to);
			die();
		}
		if ($go == '~random' | $go == '~r') {
			$query = "SELECT ID FROM $wpdb->posts WHERE post_type = 'post' AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
			$to = get_permalink($wpdb->get_var($query));
			wp_redirect($to);
			die();
		}
	}
}
?>
<?php get_header(); ?>
<div id="content">
<div class="fuss nova">搜索结果 &raquo; <strong><?php the_search_query() ?></strong></div>
<?php if (have_posts()) : ?>
<?php while (have_posts()) : the_post(); ?>
<div class="post" id="post-<?php the_ID(); ?>">
<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="到 <?php the_title(); ?> 的永久链接"><?php the_title(); ?></a></h2>
<div class="post-meta"><div class="post-info hfl"><?php the_time('F j 日, Y 年 g:i A'); ?> - By <?php the_author_posts_link(); ?><?php edit_post_link('编辑', ' - ', ''); ?></div><div class="post-comments hfr"><?php comments_popup_link('<span class="large">发表</span>评论', '<span class="large">1</span> 条评论', '<span class="large">%</span> 条评论'); ?></div></div>
<div class="entry hcf">
<?php the_excerpt(); ?>
</div>
</div>
<?php endwhile; ?>
<div class="navigation"><?php pagenavi(); ?></div>
<?php else : ?>
<div class="fuss nova">没有找到文章。尝试再次搜索?</div>
<?php endif; ?>
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>