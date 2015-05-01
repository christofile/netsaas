<?php get_header(); ?>
<div id="content">
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<div class="post" id="post-<?php the_ID(); ?>">
<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
<div class="post-meta"><div class="post-info hfl"><?php the_time('F j 日, Y 年 g:i A'); ?> - By <?php the_author_posts_link(); ?><?php edit_post_link('编辑', ' - ', ''); ?></div><div class="post-comments hfr"><?php comments_popup_link('<span class="large">发表</span>评论', '<span class="large">1</span> 条评论', '<span class="large">%</span> 条评论'); ?></div></div>
<div class="entry hcf">
<?php the_content(); ?>
</div>
</div>
<?php comments_template(); ?>
<?php endwhile; else: ?>
<p>抱歉，内容不存在。</p>
<?php endif; ?>
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>