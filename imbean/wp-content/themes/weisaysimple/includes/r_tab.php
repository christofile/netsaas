<h3><span>最新日志</span><span class="selected">热评日志</span><span>随机日志</span></h3>
	<div id="tab-content">
		<ul class="hide"><?php $myposts = get_posts('numberposts=10&offset=0');foreach($myposts as $post) :?>
					<li><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></li>
					<?php endforeach; ?></ul>
		<ul><?php simple_get_most_viewed(); ?>
</ul>
		<ul class="hide"><?php $myposts = get_posts('numberposts=10&orderby=rand');foreach($myposts as $post) :?>
					<li><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></li>
					<?php endforeach; ?></ul>
					</div>