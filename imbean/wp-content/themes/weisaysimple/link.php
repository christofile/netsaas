<?php
/*
Template Name: Link
*/
?>
<?php get_header(); ?>
<div id="roll"><div title="回到顶部" id="roll_top"></div><div title="查看评论" id="ct"></div><div title="转到底部" id="fall"></div></div>
<div id="content">
<div class="main">
<div id="mapsite">当前位置： <a title="返回首页" href="<?php echo get_settings('Home'); ?>/">首页</a> &gt; 链接</div>
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<script type="text/javascript">
jQuery(document).ready(function($){
$(".weisaylink a").each(function(e){
	$(this).prepend("<img src=https://www.google.com.hk/s2/favicons?domain="+this.href.replace(/^(http:\/\/[^\/]+).*$/, '$1').replace( 'http://', '' )+" style=float:left;padding:5px;>");
}); 
});
</script>
<div class="left left_page">
<div class="article article_page">
<div class="weisaylink"><ul>
<?php wp_list_bookmarks('orderby=id&category_orderby=id'); ?></ul>
</div>
<div class="clear"></div>
<div class="linkstandard">
<h2 style="color:#FF0000">申请友情链接前请看：</h2><ul>
<li>一、在您申请本站友情链接之前请先做好本站链接，否则不会通过，谢谢！</li>
<li>二、如果您的站还未被baidu或google收录，申请链接暂不予受理！</li>
<li>三、如果您的站原创内容少之又少，申请连接不予受理！</li>
<li>四、其他暂且保留，有想到的再添加。</li></ul>
</div>

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