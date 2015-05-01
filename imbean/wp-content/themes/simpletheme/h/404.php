<?php
add_action('wp_head', 'kudos_css');
get_header();
?>
<div id="kudos" class="hfl">
<div class="kudos-frame-left hfl">
<div class="kudos-nav hfr">
<h3>热门</h3>
<?php wp_tag_cloud('smallest=9&largest=12&number=9&format=list&order=RAND'); ?>
</div>
</div>
<div class="kudos-frame-right hfr">
<div class="kudos-post"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/error-title.gif" id="kudos-post" alt="404 ERROR"/></div>
<div class="kudos-post entry">
<p class="kodus-error">很抱歉，您试图访问的 <span class="highlight"><?php echo $_SERVER["REQUEST_URI"]; ?></span> 不存在，请确认您访问的地址。</p>
<p class="kodus-error">或者，您可以:</p>
<ol class="kodus-error kodus-ol">
<li><a href="javascript:window.history.back();">后退</a></li>
<li><a href="<?php echo get_option('home'); ?>/">返回首页</a></li>	
<li><a href="javascript:void(0);" id="search-focus">搜索</a></li>
</ol>
</div>
<!--<div class="translator"><a href="javascript:void(0);" onclick="translatePage();" style="color:#4B72A9">Translate this page</a></div>-->
</div>
</div>
<?php get_footer(); ?>