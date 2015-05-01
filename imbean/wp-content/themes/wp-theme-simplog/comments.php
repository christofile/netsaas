<?php
/**
 * The template for displaying Comments.
 *
 * The area of the page that contains both current comments
 * and the comment form. The actual display of comments is
 * handled by a callback to simplog_comment() which is
 * located in the functions.php file.
 *
 * @package simplog
 * @since simplog 1.0
 */
?>

<?php
	/*
	 * If the current post is protected by a password and
	 * the visitor has not yet entered the password we will
	 * return early without loading the comments.
	 */
	if ( post_password_required() )
		return;
?>

	<section id="comments" class="comment-area">

	<?php // You can start editing here -- including this comment! ?>

	<?php if ( have_comments() ) : ?>

                <a href="#comment" class="btn btn-large"><?php esc_html_e('Submit a comment', THEMICO_DOMAIN); ?></a>

		<h2 class="comments-title">
                    <?php
                            printf( _n( '%s Comment', '%s Comments', get_comments_number(), THEMICO_DOMAIN ),
                                     get_comments_number() );
                    ?>
		</h2>

		<ol class="commentlist">
			<?php
				/* Loop through and list the comments. Tell wp_list_comments()
				 * to use simplog_comment() to format the comments.
				 * If you want to overload this in a child theme then you can
				 * define simplog_comment() and that will be used instead.
				 * See simplog_comment() in inc/template-tags.php for more.
				 */
				wp_list_comments( array( 'callback' => 'simplog_comment' ) );
			?>
		</ol><!-- .commentlist -->

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>

                    <?php simplog_comments_pagination(); ?>

		<?php endif; // check for comment navigation ?>

	<?php else: ?>
		<p class="nocomments-yet"><?php _e( 'No comments yet.', 'simplog' ); ?></p>

	<?php endif; // have_comments() ?>

	<?php
		// If comments are closed and there are comments, let's leave a little note, shall we?
		if ( ! comments_open() && '0' != get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
	?>
		<p class="nocomments"><?php _e( 'Comments are closed.', 'simplog' ); ?></p>
	<?php endif; ?>

	<?php comment_form(); ?>

</section><!-- #comments .comments-area -->
