<?php get_header(); ?>
<div id="content">
<div id="breadcrumb" class="fuss nova">
<?php if (is_category()) { ?>分类 &raquo; <strong><?php single_cat_title(); ?></strong>
<?php } elseif (is_day()) { ?>存档 &raquo; <strong><?php the_time('F jS, Y'); ?></strong>
<?php } elseif (is_month()) { ?>存档 &raquo; <strong><?php the_time('F, Y'); ?></strong>
<?php } elseif (is_year()) { ?>存档 &raquo; <strong><?php the_time('Y'); ?></strong>
<?php } elseif (is_tag()) { ?>标签 &raquo; <strong><?php single_tag_title(); ?></strong>
<?php } elseif (is_author()) { ?><?php $curauth = get_userdata(get_query_var('author')); ?>作者 &raquo; <strong><?php echo $curauth->nickname; ?></strong>
<?php } ?>
</div>
<?php if (is_author()) { ?>
<div class="archive-meta">
<div class="author-avatar gravatar admin hfr"><?php echo get_avatar($curauth->user_email, '36', $avatar); ?></div>
<p><?php echo $curauth->user_description; ?></p><br/>
<p>注册时间：<?php echo $curauth->user_registered; ?></p>
<p>目前发表了 <strong><?php the_author_posts(); ?></strong> 篇文章</p>
</div>
<?php } elseif (is_tag()&&tag_description()) { ?>
<div class="archive-meta"><?php echo tag_description(); ?></div>
<?php } elseif (is_category()&&category_description()) { ?>
<div class="archive-meta"><?php echo category_description(); ?></div>
<?php } ?>
<?php rewind_posts() ?>
<?php while ( have_posts() ) : the_post(); ?>
<div class="post" id="post-<?php the_ID(); ?>">
<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
<div class="post-meta"><div class="post-info hfl"><?php the_time('F j 日, Y 年 g:i A'); ?> - By <?php the_author_posts_link(); ?><?php edit_post_link('编辑', ' - ', ''); ?></div><div class="post-comments hfr"><?php comments_popup_link('<span class="large">发表</span>评论', '<span class="large">1</span> 条评论', '<span class="large">%</span> 条评论'); ?></div></div>
<div class="entry hcf">
<?php the_excerpt(); ?>
</div>
<div class="iris nova">
<span class="post-tags">标签: <?php the_tags('', ', ', ''); ?></span>
</div>
</div>
<?php endwhile; ?>
<div class="navigation"><?php pagenavi(); ?></div>
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>