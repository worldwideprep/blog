<?php


/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * Please see /external/starkers-utilities.php for info on Starkers_Utilities::get_template_parts()
 *
 * @package 	WordPress
 * @subpackage 	Starkers
 * @since 		Starkers 4.0
 * 
 * Template Name: Blog
 * 
 */
?>


<?php Starkers_Utilities::get_template_parts( array( 'parts/shared/html-header', 'parts/shared/header' ) ); ?>
<?php 
//query_posts('cat='.$cats.'&posts_per_page='.$per_page.'&paged='.get_query_var('paged'));
//query_posts('posts_per_page=5&posty_type=post&paged='.get_query_var('paged'));
if ( have_posts() ): ?>

<?php 
if(has_post_thumbnail()!='') {
	$bg_img=wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()),'large');
	$text = get_excerpt_by_id(get_the_ID(),'40');
	$a_id=$post->post_author;
	echo '</section><section class="home_featured_post" style="background:url('.$bg_img[0].') center center no-repeat #fff;background-size:cover;">
 		<div class="container"><div class="inside_featured">
 		<h1 class="post_title">'.get_the_title(get_the_ID()).'</h1>
 		<time class="posted_single no_mobile" datetime="'; the_time( 'Y-m-d' ); echo '" pubdate>'.__('Posted On ','exampal_blog'); the_time( 'Y-m-d' ); echo '</time>';
 		echo '<span class="single_author">'.get_avatar(get_the_author_meta('user_email',$a_id),$size='80').' By '.get_the_author_meta('first_name',$a_id).' '.get_the_author_meta('last_name',$a_id).'</span>';
 		echo '</div></div><div class="home_featured_overlay"></div>
 		<div class="posts_full single_post">
         </section>
         <section class="home_featured_post" style="background:#161819; padding:0;">
         <div class="posts_full single_post"><div id="desktopbanner"><a href="https://exampal.com/"><img src="http://blog.exampal.com/wp-content/uploads/2016/08/Desktop-banner.png" /></a></div></div>
          <div class="posts_full single_post"><div id="mobilebanner"><a href="https://exampal.com/"><img src="http://blog.exampal.com/wp-content/uploads/2016/08/Mobile-banner.png" /></a></div></div>
           </section>
 		<section class="container">';
 		echo '<time class="posted_single mobile_only" datetime="'; the_time( 'Y-m-d' ); echo '" pubdate>'.__('Posted On ','exampal_blog'); the_time( 'Y-m-d' ); echo '</time>';
		echo '<div class="share_top">'.do_shortcode('[addtoany]').'</div>';
}
?>


<?php 

$f2=get_field('f2');
$fd1=get_field('fd1');
$fd2=get_field('fd2');
if((!empty($f2)&&$f2!='')
||(!empty($fd1)&&$fd1!='')||
(!empty($fd2)&&$fd2!='')) echo '<div class="single_post_left">';
?>
<div class="posts_loop page_inner">

<div class="posts_full single_post">
    
<ol>
<?php while ( have_posts() ) : the_post(); ?>
	<li>
		<article>
		<?php $bg_img=wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()),'large'); ?>
			<?php the_content(); ?>
		</article>
		<?php 
		echo '<div class="share_bot">'.do_shortcode('[addtoany]').'</div>';
		echo '<div class="single_author bottom_auth"><div class="img_ava">'.get_avatar(get_the_author_meta('user_email',$a_id),$size='80').'</div><div class="bio_right">
 		'.get_the_author_meta('first_name').' '.get_the_author_meta('last_name',$a_id).' '.get_the_author_meta('posit',$a_id).'
 		<div class="author_bio">'.get_the_author_meta('description',$a_id).'</div></div><div class="cl"></div></div>';
 		
		?>
	</li>
<?php endwhile; ?>
</ol>
</div>

<?php
//NEWSLETTER

$cat_op=get_field('cat_op','option');
$cat = get_the_category();
$cat_id = $cat[0]->cat_ID;

$c1='Sign up to our blog';
$c2='Get weekly tips on your way to business school';
$c3='Your email...';
$c4='Sign up to our blog';

foreach($cat_op as $cat_op_v) {
	if($cat_op_v['category']==$cat_id) {
		$c1=$cat_op_v['f1'];
		$c2=$cat_op_v['f2'];
		$c3=$cat_op_v['f3'];
		$c4=$cat_op_v['f4'];
		echo '<script type="text/javascript">
 		jQuery(document).ready(function(){
			jQuery(\'.single_nl .nl_form_top input[type="text"]\').attr("placeholder","'.$c3.'");
			jQuery(\'.single_nl .nl_form_top input.submit\').val("'.$c4.'");
		});
 		</script>';
	}
}

echo '<section class="newsletter_top single_nl"><div class="container">
<div class="single_sign_up nl_head">'.$c1.'</div>
<div class="single_info nl_right">'.$c2.'</div>
<div class="nl_form_top">';
echo do_shortcode('[mc4wp_form id="97"]'); 
echo '</div>						
<div class="cl"></div></div>
</section>';

//RELATED

echo '<section class="related"><div class="container">';
if(function_exists('echo_crp')) echo_crp();
echo '<div class="cl"></div><a class="button show_more">'.__('all posts','exampal_blog').'</a></div>
</section>';


//COMMENTS
echo '<section class="comments"><div class="container">';
comments_template();
echo '<div class="cl"></div></div>
</section>';
?>

<?php else: ?>
<h2>No post to display</h2>
<?php endif; ?>
</div>

<?php 

if((!empty($f2)&&$f2!='')||
(!empty($fd1)&&$fd1!='')||
(!empty($fd2)&&$fd2!='')) {
echo '</div><div class="single_post_right">';

    if(get_field('banner_active',$fd1)==1) {
		if(get_field('banner_code',$fd1)!='') echo get_field('banner_code',$fd1);
		else {
			echo '<div class="nl_form_top righbanform"><a href="'.get_field('banner_url',$fd1).'" target="_blank" class="banner_img"><img src="'.get_field('banner_image',$fd1).'" alt="banner"/></a>
			'.do_shortcode('[mc4wp_form id="97"]').'
			</div>';
			?>
			<script type="text/javascript">
			    jQuery(document).ready(function(){
			        jQuery('.nl_form_top.righbanform input.submit').val('<?php echo get_field('download_button_text',$fd1);?>').addClass('submit-download');
			    });
			</script>
			<?php
		}
	}
    if(get_field('banner_active',$fd2)==1) {
		if(get_field('banner_code',$fd2)!='') echo get_field('banner_code',$fd2);
		else {
		    $vid_url=get_field('youtube_video_url',$fd2);
		    parse_str( parse_url( $vid_url, PHP_URL_QUERY ), $my_array_of_vars );
            $vid_url=$my_array_of_vars['v'];   
			echo '<div class="youb_vid"><iframe width="300" height="250" src="https://www.youtube.com/embed/'.$vid_url.'" frameborder="0" allowfullscreen></iframe></div>';
		}
	}
if(get_field('banner_active',$f2)==1) {
		if(get_field('banner_code',$f2)!='') echo get_field('banner_code',$f2);
		else {
			echo '<a href="'.get_field('banner_url',$f2).'" target="_blank" class="banner_img"><img src="'.get_field('banner_image',$f2).'" alt="banner"/></a>';
		}
	}
	
	echo '</div>';
}
?>
<?php Starkers_Utilities::get_template_parts( array( 'parts/shared/footer','parts/shared/html-footer' ) ); ?>
