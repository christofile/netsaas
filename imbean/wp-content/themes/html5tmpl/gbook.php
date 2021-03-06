<?php
/*
Template Name: Guestbook
*/
?>
<?php get_header(); ?>

<div id="roll">
  <div title="回到顶部" id="roll_top"></div>
  <div title="查看评论" id="ct"></div>
  <div title="转到底部" id="fall"></div>
</div>
 <div id="content">
    <div class="main">
      <div id="map">
        <div class="site">当前位置： <a title="返回首页" href="<?php echo get_settings('Home'); ?>/">首页</a> &gt; 留言板</div>
        </div>
  <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

<div class="article article_c">
      <?php if (get_option('swt_type') == 'Display') { ?>
      <div class="v_comment">
        <ul>
          <?php
$counts = $wpdb->get_results("SELECT COUNT(comment_ID) AS cnt, comment_author, comment_author_url, comment_author_email FROM (SELECT * FROM $wpdb->comments LEFT OUTER JOIN $wpdb->posts ON ($wpdb->posts.ID=$wpdb->comments.comment_post_ID) WHERE comment_date > date_sub( NOW(), INTERVAL 3 MONTH ) AND user_id='0' AND comment_author_email != '' AND post_password='' AND comment_approved='1' AND comment_type='') AS tempcmt GROUP BY comment_author_email ORDER BY cnt DESC LIMIT 56");
foreach ($counts as $count) {
$a = get_bloginfo('wpurl') . '/avatar/' . md5(strtolower($count->comment_author_email)) . '.png';
$c_url = $count->comment_author_url;
$mostactive .= '<li class="mostactive">' . '<a href="'. $c_url . '" title="' . $count->comment_author . ' (留下'. $count->cnt . '个脚印)" target="_blank" rel="external nofollow"><img src="' . $a . '" alt="' . $count->comment_author . ' (留下'. $count->cnt . '个脚印)" class="avatar" /></a></li>';
}
echo $mostactive;
?>
        </ul>
     
      <?php { echo ''; } ?>
      <?php } else { include(TEMPLATEPATH . '/gbook2.php'); } ?>
      <div class="clear"></div>
</div>

  <div class="article article_c article_b">
    <?php comments_template(); ?>
  </div>
    <?php endwhile; else: ?>
  <?php endif; ?>
</div>


<?php get_footer(); ?>
