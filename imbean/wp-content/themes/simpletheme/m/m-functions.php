<?php
define("m_home","http://m.livesino.net");
function m_comments(){
	global $wpdb;$p = intval($_GET['p']);
	if ($p > 0){
	$sql = "SELECT DISTINCT ID, post_title, post_password, comment_ID, comment_post_ID, comment_author, comment_author_email, comment_date_gmt, comment_approved, comment_type, comment_author_url, comment_content FROM $wpdb->comments LEFT OUTER JOIN $wpdb->posts ON ($wpdb->comments.comment_post_ID = $wpdb->posts.ID) WHERE comment_post_ID = '$p' AND $wpdb->comments.comment_approved = '1' AND $wpdb->posts.post_status = 'publish' AND post_date < '".current_time('mysql')."' ORDER BY comment_date";
	}else{
	$sql = "SELECT DISTINCT ID, post_title, post_password, comment_ID, comment_post_ID, comment_author, comment_author_email, comment_date_gmt, comment_approved, comment_type, comment_author_url, SUBSTRING(comment_content,1,90) AS comment_content FROM $wpdb->comments LEFT OUTER JOIN $wpdb->posts ON ($wpdb->comments.comment_post_ID = $wpdb->posts.ID) WHERE comment_approved = '1' AND comment_type = '' AND post_password = '' ORDER BY comment_date_gmt DESC LIMIT 8";}
	$comments = $wpdb->get_results($sql);
	$output = '';
	$output .= "<ul class=\"nova-comments\">";
	foreach ($comments as $comment) 
	{
		$gravatar = get_avatar( esc_attr($comment->comment_author_email), 20, $avatar);
		$output .= "\n<li>". $gravatar ."<a href=\"". m_home ."/?p=". $comment->comment_post_ID ."#comment-". $comment->comment_ID ."\" id=\"comment-". $comment->comment_ID ."\" title=\"on ". $comment->post_title ."\">". strip_tags($comment->comment_author) ."</a>: ". $comment->comment_content ."</li>";
	}
	$output .= "\n</ul>";
	echo $output;
}
function m_category(){
	$cat_i = 0;
	$post_cat = '';
	foreach((get_the_category()) as $category) {
		if ( 0 < $cat_i )
		$post_cat .= ', ';
		$post_cat .= $category->cat_name;
		echo $post_cat;
		++$cat_i;
	}
}
function m_standard(){
	if (is_single()){ $standard_url = get_permalink($post->ID);}
	else{ $standard_url = get_option('home');}
	echo $standard_url;
}
?>