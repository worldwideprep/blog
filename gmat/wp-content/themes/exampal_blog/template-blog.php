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
<script type="text/javascript">
    jQuery(document).ready(function () {


        var sticky = new Waypoint.Sticky({
            element: jQuery('.scroll_toggle')[0],
            direction: 'up',
            offset: 'bottom-in-view'
        })

        infinite = new Waypoint.Infinite({
            element: jQuery('.tab_content ol')[0]
        })

    });
</script>

<!-- Banner area start -->
<div class="home_top">
<?php if(get_field('f1')!='') echo '<h2>'.get_field('f1').'</h2>'; ?>
<?php $f2=get_field('f2');
if($f2!='') {
	if(get_field('banner_active',$f2)==1) {
		if(get_field('banner_code',$f2)!='') echo get_field('banner_code',$f2);
		else {
			echo '<a href="'.get_field('banner_url',$f2).'" target="_blank" class="banner_img"><img src="'.get_field('banner_image',$f2).'" alt="banner"/></a>';
		}
	}
}
?>
</div>
<!-- Banner area end -->


<?php
 $f2_side=get_field('f2_side');
 $fd1=get_field('fd1');
$fd2=get_field('fd2');
$f3=get_field('f3');
$f4=get_field('f4');
$f5=get_field('f5');
$f6=get_field('f6');
$f7=get_field('f7');
$f8=get_field('f8');
$f9=get_field('f9');



query_posts('cat=-1&order=ASC');
while (have_posts()) : the_post();

 $postid = get_the_ID();

 // $last = wp_get_recent_posts(array('post_status'=>'publish'));
// $checkhidepostID = $last['0']['ID'];
$checkhide = get_post_meta($postid, 'hide_post_home','true');

if($checkhide=='Hidepost')
{
  //echo 'hide';

}else
{
     $f3 = get_the_ID();

}

endwhile;
wp_reset_query();
//echo 'post id'.$postid2;


if($f3!='') {
	$bg_img=wp_get_attachment_image_src(get_post_thumbnail_id($f3),'large');
	$text = get_excerpt_by_id($f3,'40');
	$text=get_the_excerpt($f3);

	echo '</section>';

echo '<section class="newsletter_top mobile_only" style="margin-bottom:0!important;"><div class="container">
<div class="col span_4 nl_head">'.$f4.'</div>
<div class="col span_3 nl_right">'.$f7.'</div>		
<div class="col span_5 nl_center"><div class="nl_form_top">';
echo do_shortcode('[mc4wp_form id="97"]'); echo '
</div></div>					
<div class="cl"></div></div>
</section>';


	echo '<section class="home_featured_post" style="background:url('.$bg_img[0].') center center no-repeat #fff;background-size:cover;">
 		<div class="container"><div class="inside_featured">
 		<h1 class="post_title">'.get_the_title($f3).'</h1>
 		<div class="excerpt">'.$text.'</div>
 		<a class="read_more button" href="'.get_the_permalink($f3).'">'.__('Read More','exempal_blog').'</a>
 		</div></div><div class="home_featured_overlay"></div>
 		</section>';
}

echo '<section class="newsletter_top no_mobile"><div class="container">
<div class="col span_4 nl_head">'.$f4.'</div>
<div class="col span_5 nl_center"><div class="nl_form_top">';
echo do_shortcode('[mc4wp_form id="97"]'); echo '
</div></div>
<div class="col span_3 nl_right">'.$f7.'</div>							
<div class="cl"></div></div>
</section>';


echo '<section class="container">';
wp_enqueue_script( 'wayp-sticky' );
wp_enqueue_script( 'wayp-inf' );

?>
<div class="posts_loop page_inner">
<div class="tab_head"><div class="tab_in">
<input type="hidden" class="secur" value="<?php echo wp_create_nonce('security_token'); ?>"/>
<a class="tab_open active" href="#tab_recent" data-type="recent">Recent Posts</a>
<a class="tab_open" href="#tab_must" data-type="must_read">Must Read Posts</a>
<?php if(is_array($f9)&&!empty($f9)) {
	foreach($f9 as $f9v) {
		if($f9v!=''&&get_cat_name($f9v)!='') echo '<a href="#cat_'.$f9v.'" class="tab_open cat_'.$f9v.'" data-type="cat" data-cat="'.$f9v.'">'.get_cat_name($f9v).'</a>';
	}
}?>
</div></div><div class="cl"></div>
<?php
if(!isset($_GET['paged']))$_GET['paged']='1';
if(!isset($_GET['type']))$_GET['type']='recent';

wp_reset_query();
//query_posts('cat='.$cats.'&posts_per_page='.$per_page.'&paged='.get_query_var('paged'));
echo '<div class="posts_left col span_8"> &nbsp;';
echo '<div class="tab_content">';
$paged = esc_attr($_GET['paged']);
if($paged=='')$paged='1';
$type = esc_attr($_GET['type']);
if($type=='')$type='recent';

			$ids = get_field('f8','217');

		if($type=='recent') query_posts('posts_per_page=5&post_status=publish&post_type=post&paged='.$paged);

 		if($type=='must_read') {
			query_posts(array('post__in'=>$ids,'post_status'=>'publish','post_type'=>'post','posts_per_page'=>'5','paged'=>$paged));
		}
		//if($type=='cat') {

		if(isset($_GET['customcat'])) {
            $getcustomcat=$_GET['customcat'];
			//echo "cat".$_GET['customcat'];
			query_posts('cat='.$getcustomcat.'&post_status=publish&posts_per_page=5&post_type=post&paged='.$paged);
		}
	//	if($type=='recent') query_posts('posts_per_page=5&post_status=publish&post_type=post&paged='.$paged);

if ( have_posts() ): ?>

<ol>
<?php while ( have_posts() ) : the_post(); ?>

	<li class="infinite-item <?php echo $hidemeta = get_post_meta(get_the_ID(), 'hide_post_home','true'); ?>">

		<article>
		<?php $bg_img=wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()),'large');
		$text = get_excerpt_by_id(get_the_ID(),'60','yes');
    	$text=get_the_excerpt(get_the_ID());
		?>
			<?php if(has_post_thumbnail()){?><div class="col span_6"><a href="<?php the_permalink(); ?>" title="<?php the_title();?>">
			<img src="<?php echo $bg_img[0]; ?>" alt="<?php echo get_the_title();?>"/></a></div><?php }?>
			<?php if(has_post_thumbnail()){?><div class="content_right col span_6"><?php }?>
			<time datetime="<?php the_time( 'Y-m-d' ); ?>" pubdate><?php _e('Posted On ','exampal_blog');?><?php the_time( 'Y-m-d' ); ?></time>
			<a class="article_list_title" href="<?php the_permalink(); ?>" title="Permalink to <?php the_title(); ?>" rel="bookmark"><?php the_title(); ?></a>

			<div class="content_excerpt"><?php echo $text; ?>

			</div>
			<?php if(has_post_thumbnail()){?></div><?php }?>
			<div class="cl"></div>
		</article>
	</li>
<?php endwhile; ?>

<?php if(isset($_GET['customcat'])){ ?>
    <a class="infinite-more-link" href="<?php $paged_n=$paged+1; echo get_home_url().'/?paged='.$paged_n.'&type='.$type.'&customcat='.$_GET['customcat'];?>"></a>
<?php } else { ?>
    <a class="infinite-more-link" href="<?php $paged_n=$paged+1; echo get_home_url().'/?paged='.$paged_n;?>"></a>
<?php } ?>
<?php else: ?>
<h2>No posts to display</h2>
<?php endif; ?>
</ol>
</div>
</div>
<div class="posts_right col span_4">
<div class="follow_widget widget"><h3>Follow Us:</h3><div class="right_icons_follow">
<a href="<?php echo get_field('l1','option');?>" target="_blank"><i class="fa fa-facebook"></i></a>
<a href="<?php echo get_field('l2','option');?>" target="_blank"><i class="fa fa-twitter"></i></a>
<a href="<?php echo get_field('l3','option');?>" target="_blank"><i class="fa fa-linkedin"></i></a>
</div></div>

<div class="bann3">
    <?php

    if(get_field('banner_active',$fd1)==1) {
		if(get_field('banner_code',$fd1)!='') echo get_field('banner_code',$fd1);
		else {
		    $banner_url = get_field('banner_url',$fd1);
		    $banner_image = get_field('banner_image',$fd1);
		    ?>
			<div class="nl_form_top righbanform">
                <?php
                    if (!empty($banner_image)) {
                        if (!empty($banner_url)) {
                            ?>
                            <a href="<?php echo $banner_url; ?>" target="_blank" class="banner_img">
                                <img src="<?php echo $banner_image; ?>" alt="banner"/>
                            </a>
                            <?php
                        } else {
                            ?>
                                <img src="<?php echo $banner_image; ?>" alt="banner"/>
                            <?php
                        }
                    }
                ?>
			    <?php echo do_shortcode('[mc4wp_form id="98"]'); ?>
			</div>
			<!--<script type="text/javascript">
			    jQuery(document).ready(function(){
			        jQuery('.nl_form_top.righbanform input.submit').val('<?php /*echo get_field('download_button_text',$fd1);*/?>').addClass('submit-download');
			    });
			</script>-->
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

dynamic_sidebar( 'blog-home-sidebar-top' );

    if(get_field('banner_active',$f2_side)==1) {
		if(get_field('banner_code',$f2_side)!='') echo get_field('banner_code',$f2_side);
		else {
			echo '<a href="'.get_field('banner_url',$f2_side).'" target="_blank" class="banner_img"><img src="'.get_field('banner_image',$f2_side).'" alt="banner"/></a>';
		}
	}
    ?>
</div>
<br>
 <?php dynamic_sidebar( 'blog-home-sidebar' ); ?>
<br>
<a href="#" class="scroll_toggle" title="scroll top/bottom"><img src="<?php echo get_template_directory_uri(); ?>/images/top.png" alt="scroll to top"/></a>

</div>
</div>

<?php
echo '<section class="newsletter_top mobile_only"><div class="container">
<div class="col span_4 nl_head">'.$f4.'</div>
<div class="col span_3 nl_right">'.$f7.'</div>		
<div class="col span_5 nl_center"><div class="nl_form_top">';
echo do_shortcode('[mc4wp_form id="97"]'); echo '
</div></div>					
<div class="cl"></div></div>
</section>';
?>


<?php Starkers_Utilities::get_template_parts( array( 'parts/shared/footer','parts/shared/html-footer' ) ); ?>
