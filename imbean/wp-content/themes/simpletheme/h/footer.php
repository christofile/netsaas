<div id="footer" class="hcf">
<div class="footer-bg hpa"></div>
<div class="footer-credits">
<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr>
<td><span class="footer-fuss">Theme</span><br/><a href="http://livesino.net/theme-codename-h" style="color:#FF0000;">Codename H</a> <span style="color:#999999;">Rev.96</span></td>
<td><span class="footer-fuss">Powered by</span><br/><a href="http://v7v3.com" style="color:#81BF34;" target="_blank">Wordpress</a></td>
<td><span class="footer-fuss">Hosted by</span><br/><a href="http://www.mediatemple.net/" style="color:#0A328C;" target="_blank">(MT) Media Temple</a></td>
<td><span class="footer-fuss">Syndicated by</span><br/><a href="<?php rss_feed(); ?>" style="color:#FF6600;" target="_blank"><?php bloginfo('name'); ?></a></td>
<td><span class="footer-fuss">Mobilized by</span><br/><a href="http://livesino.net/theme-codename-h#mobile" style="color:#5386E1;" target="_blank">Theme Codename H</a></td>
<td><span class="footer-fuss"><?php echo get_num_queries(); ?> Queries</span><br/><span style="color:#6994B9;"><?php timer_stop(1); ?> Seconds</span></td>
<td><span class="footer-fuss">Copyright 2010 <?php bloginfo('name'); ?></span><br/><a href="<?php echo get_option('home'); ?>/" style="color:#000000;" target="_blank">保留一切权利</a></td>
</tr>
</table>
</div>
</div>
</div><!--/livesino -->
<div class="hdn">
<div id="rss-feeds-box" class="rss-feeds">
<div class="sidebar-feeds"><a href="<?php rss_feed(); ?>" target="_blank" style="color:#F36E30;text-decoration:none;">订阅 RSS feed</a></div>
<div class="sidebar-widget">
<h4><span class="sidebar-title hfl"><?php bloginfo('name'); ?></span></h4>
<div class="htc"><a href="<?php rss_feed(); ?>" target="_blank" style="font-size:16px;color:#F36E30;">通过 RSS 订阅 <?php bloginfo('name'); ?></a></div>
</div>
<div class="sidebar-widget" style="line-height:50px;">
<h4><span class="sidebar-title hfl">快速订阅</span></h4>
<div>
<a title="快速订阅至 Google Reader 或 iGoogle" href="http://fusion.google.com/add?feedurl=<?php rss_feed(); ?>" target="_blank" rel="nofollow" style="color:#2578CD;font-size:28px;">Google</a>&nbsp;&nbsp;
<a title="快速订阅至 鲜果" href="http://xianguo.com/subscribe?url=<?php rss_feed(); ?>" target="_blank" rel="nofollow" style="color:#FA930D;font-size:18px;">鲜果</a>&nbsp;&nbsp;
<a title="快速订阅至 My Yahoo!" href="http://add.my.yahoo.com/rss?url=<?php rss_feed(); ?>" target="_blank" rel="nofollow"  style="color:#D32930;font-size:15px;">My Yahoo!</a>&nbsp;&nbsp;
<a title="快速订阅至 QQ 邮箱" href="http://mail.qq.com/cgi-bin/feed?u=<?php rss_feed(); ?>" target="_blank" rel="nofollow" style="color:#0C57A6;font-size:21px;">QQ 邮箱</a>&nbsp;&nbsp;
<a title="快速订阅至 抓虾" href="http://www.zhuaxia.com/add_channel.php?url=<?php rss_feed(); ?>" target="_blank" rel="nofollow" style="color:#79A539;font-size:23px;">抓虾</a>&nbsp;&nbsp;
<a title="快速订阅至 有道阅读" href="http://reader.yodao.com/#url=<?php rss_feed(); ?>" target="_blank" rel="nofollow" style="color:#D64040;font-size:18px;">有道阅读</a>&nbsp;&nbsp;
<a title="快速订阅至 豆瓣九点" href="http://9.douban.com/reader/subscribe?url=<?php rss_feed(); ?>" target="_blank" rel="nofollow" style="color:#048480;font-size:24px;">豆瓣九点</a>&nbsp;&nbsp;
<a title="快速订阅至 Netvibes" href="http://www.netvibes.com/subscribe.php?url=<?php rss_feed(); ?>" target="_blank" rel="nofollow" style="color:#0EA704;font-size:20px;">Netvibes</a>&nbsp;&nbsp;
<a title="快速订阅至 哪吒" href="http://inezha.com/add?url=<?php rss_feed(); ?>" target="_blank" rel="nofollow"  style="color:#D23D05;font-size:19px;">哪吒</a>
</div>
</div>
</div>
</div>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
<script type="text/javascript" src="<?php bloginfo('stylesheet_directory');?>/colorbox/jquery.colorbox-min.js"></script>
<script type="text/javascript" src="<?php bloginfo('stylesheet_directory');?>/app.js"></script>
<!--<script type="text/javascript" src="http://api.microsofttranslator.com/v1/Ajax.svc/Embed?appId=XE0RNFhCWbIVA4970mT4Yt2WCkcGTyHe"></script>
<script type="text/javascript">
function translatePage(){Microsoft.Translator.translate(document.body,"zh-CHS", "en");}
</script>-->
<?php wp_footer(); ?>
</body>
</html>