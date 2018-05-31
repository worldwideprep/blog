<?php
/**
 * The template for displaying comments
 *
 * The area of the page that contains both current comments
 * and the comment form.
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}

?>

<div id="comments" class="comments-area comments">
<?php 
$uid = get_current_user_id();
if($uid!='0') echo '<div class="c_logged">';
else echo '<div class="c_not_logged">';
?>
<div class="c_number">
	<?php if ( have_comments() ) { ?>
		<h2 class="comments-title">
			<?php
				$comments_number = get_comments_number();
					printf(
						/* translators: 1: number of comments, 2: post title */
						_nx(
							'<span class="numb">%1$s</span> Comment',
							'<span class="numb">%1$s</span> Comments',
							$comments_number,
							'comments title',
							'exampal_blog'
						),
						number_format_i18n( $comments_number ),
						get_the_title()
					);
			?>
		</h2>
	<?php } else {?>
	<h2><?php _e('No Comments. Be the first to post','exampal_blog');?></h2>
	<?php } ?>
	<?php 
	$cl_class='';
	$cl_class_list='';
	$d_closed='';
if(!isset($_COOKIE['c_toggle']))$_COOKIE['c_toggle']='';
	if($_COOKIE['c_toggle']=='closed') { $cl_class=''; $cl_class_list=''; $d_closed='closed'; }
	else { $cl_class='c_close'; $cl_class_list='c_open'; $d_closed='';}?>
	<a class="c_toggle <?php echo $cl_class;?>" title="close/open comments" data-closed="<?php echo $d_closed;?>"></a>
</div><!-- c_number end -->
	
<?php
$ava='';
$ava=get_avatar($uid,$size='48');
	echo '<div class="comment_av">'.$ava.'</div><div class="comm_form_right">';
		comment_form( array(
			'title_reply_before' => '<h2 id="reply-title" class="comment-reply-title">',
			'title_reply_after'  => '</h2>',
		) );
	echo '<div class="cl"></div></div>';
	?>
	
	<div class="cl"></div>
	<?php if ( have_comments() ) { ?>
		<?php the_comments_navigation(); ?>

		<ol class="comment-list <?php echo $cl_class_list;?>">
			<?php
				wp_list_comments( array(
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 42,
				) );
			?>
		</ol><!-- .comment-list -->

		<?php the_comments_navigation(); ?>

	<?php }; // Check for have_comments(). ?>

	<?php
		// If comments are closed and there are comments, let's leave a little note, shall we?
		if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) {
	?>
		<p class="no-comments"><?php _e( 'Comments are closed.', 'twentysixteen' ); ?></p>
	<?php }; ?>

</div><!-- .comments-area -->
</div>
