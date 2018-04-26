<?php /* Category archives template */ ?>
<?php Starkers_Utilities::get_template_parts( array( 'parts/shared/html-header', 'parts/shared/header' ) ); ?>

<div class="posts_left col span_8">

<?php if ( have_posts() ): ?>
<h2 style="padding:20px 0;">Category Archive: <?php echo single_cat_title( '', false ); ?></h2>
<ol>
<?php while ( have_posts() ) : the_post(); ?>

	<li class="infinite-item <?php echo $hidemeta = get_post_meta(get_the_ID(), 'hide_post_home','true'); ?>">
		<article>
		<?php $bg_img=wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()),'medium');
		$text = get_excerpt_by_id(get_the_ID(),'60','yes'); 
    	$text=get_the_excerpt(get_the_ID());
		?>
			<?php if(has_post_thumbnail()){?><div class="col span_6"><a href="<?php the_permalink(); ?>" title="<?php the_title;?>">
			<img  src="<?php echo $bg_img[0]; ?>" alt="<?php echo get_the_title();?>"/></a></div><?php }?>
			<?php if(has_post_thumbnail()){?><div class="content_right col span_6"><?php }?>
			<time datetime="<?php the_time( 'Y-m-d' ); ?>" pubdate><?php _e('Posted On ','exampal_blog');?><?php the_time( 'Y-m-d' ); ?></time>
			<a class="article_list_title" href="<?php esc_url( the_permalink() ); ?>" title="Permalink to <?php the_title(); ?>" rel="bookmark"><?php the_title(); ?></a>
			
			<div class="content_excerpt"><?php echo $text; ?> 
			
			</div>
			<?php if(has_post_thumbnail()){?></div><?php }?>
			<div class="cl"></div>
		</article>
	</li>
<?php endwhile; ?>
</ol>
<?php else: ?>
<h2>No posts to display in <?php echo single_cat_title( '', false ); ?></h2>
<?php endif; ?>

</div>

<div class="posts_right col span_4" style="padding:30px 0;">
<div class="follow_widget widget"><h3>Follow Us:</h3><div class="right_icons_follow">
<a href="<?php echo get_field('l1','option');?>" target="_blank"><i class="fa fa-facebook"></i></a>
<a href="<?php echo get_field('l2','option');?>" target="_blank"><i class="fa fa-twitter"></i></a>
<a href="<?php echo get_field('l3','option');?>" target="_blank"><i class="fa fa-linkedin"></i></a>
</div></div>

<div class="bann3">
<a class="banner_img" target="_blank" href="#"><img alt="banner" src="http://blog.exampal.com/wp-content/uploads/2016/06/screenshot_160x600.jpg" class=""></a>
</div>

<a href="#" class="scroll_toggle" title="scroll top/bottom"><img src="<?php echo get_template_directory_uri(); ?>/images/top.png" alt="scroll to top"/></a>

</div>


<?php Starkers_Utilities::get_template_parts( array( 'parts/shared/footer','parts/shared/html-footer' ) ); ?>